<?php

class AuthController extends Controller {

    public function registerForm() {
        // Si l'utilisateur est déjà connecté, rediriger vers la gallery
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/gallery');
            return;
        }

        $this->view('auth/register', [
            'title' => 'Register - Camagru'
        ]);
    }

    public function register() {
        // Si l'utilisateur est déjà connecté, rediriger vers la gallery
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/gallery');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = $this->validateRegistration($username, $email, $password, $confirmPassword);

        if (!empty($errors)) {
            $this->view('auth/register', [
                'title' => 'Register - Camagru',
                'errors' => $errors,
                'username' => $username,
                'email' => $email
            ]);
            return;
        }

        $userModel = new User();

        if ($userModel->findByUsername($username)) {
            $errors[] = 'Username already exists';
        }

        if ($userModel->findByEmail($email)) {
            $errors[] = 'Email already exists';
        }

        if (!empty($errors)) {
            $this->view('auth/register', [
                'title' => 'Register - Camagru',
                'errors' => $errors,
                'username' => $username,
                'email' => $email
            ]);
            return;
        }

        $verificationToken = bin2hex(random_bytes(32));

        if ($userModel->create($username, $email, $password, $verificationToken)) {
            $emailSender = new EmailSender();
            $emailSender->sendVerificationEmail($email, $username, $verificationToken);

            $this->view('auth/register-success', [
                'title' => 'Registration Successful - Camagru',
                'email' => $email
            ]);
        } else {
            $this->view('auth/register', [
                'title' => 'Register - Camagru',
                'errors' => ['Registration failed. Please try again.'],
                'username' => $username,
                'email' => $email
            ]);
        }
    }

    public function verify() {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $this->view('auth/verify-error', [
                'title' => 'Verification Error - Camagru'
            ]);
            return;
        }

        $userModel = new User();
        if ($userModel->verifyEmail($token)) {
            $this->view('auth/verify-success', [
                'title' => 'Email Verified - Camagru'
            ]);
        } else {
            $this->view('auth/verify-error', [
                'title' => 'Verification Error - Camagru'
            ]);
        }
    }

    public function loginForm() {
        // Si l'utilisateur est déjà connecté, rediriger vers la gallery
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/gallery');
            return;
        }

        $this->view('auth/login', [
            'title' => 'Login - Camagru'
        ]);
    }

    public function login() {
        // Si l'utilisateur est déjà connecté, rediriger vers la gallery
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/gallery');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }

        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];

        if (empty($login)) {
            $errors[] = 'Username or email is required';
        }

        if (empty($password)) {
            $errors[] = 'Password is required';
        }

        if (!empty($errors)) {
            $this->view('auth/login', [
                'title' => 'Login - Camagru',
                'errors' => $errors,
                'login' => $login
            ]);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByUsernameOrEmail($login);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->view('auth/login', [
                'title' => 'Login - Camagru',
                'errors' => ['Invalid username/email or password'],
                'login' => $login
            ]);
            return;
        }

        if (!$user['email_verified']) {
            $this->view('auth/login', [
                'title' => 'Login - Camagru',
                'errors' => ['Please verify your email before logging in'],
                'login' => $login
            ]);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        $this->redirect('/gallery');
    }

    public function logout() {
        session_destroy();
        $this->redirect('/');
    }

    public function forgotPasswordForm() {
        // Si l'utilisateur est déjà connecté, rediriger vers la gallery
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/gallery');
            return;
        }

        $this->view('auth/forgot-password', [
            'title' => 'Forgot Password - Camagru'
        ]);
    }

    public function forgotPassword() {
        // Si l'utilisateur est déjà connecté, rediriger vers la gallery
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/gallery');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/forgot-password');
            return;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $this->view('auth/forgot-password', [
                'title' => 'Forgot Password - Camagru',
                'errors' => ['Email is required'],
                'email' => $email
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view('auth/forgot-password', [
                'title' => 'Forgot Password - Camagru',
                'errors' => ['Please enter a valid email address'],
                'email' => $email
            ]);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            $this->view('auth/forgot-password', [
                'title' => 'Forgot Password - Camagru',
                'errors' => ['No account found with this email address'],
                'email' => $email
            ]);
            return;
        }

        // Générer un token de reset
        $resetToken = bin2hex(random_bytes(32));

        // Sauvegarder le token en base
        if ($userModel->createResetToken($user['id'], $resetToken)) {
            // Envoyer l'email
            $emailSender = new EmailSender();
            $emailSender->sendPasswordResetEmail($email, $user['username'], $resetToken);

            // Afficher le message de succès
            $this->view('auth/forgot-password-sent', [
                'title' => 'Reset Link Sent - Camagru',
                'email' => $email
            ]);
        } else {
            $this->view('auth/forgot-password', [
                'title' => 'Forgot Password - Camagru',
                'errors' => ['An error occurred. Please try again later.'],
                'email' => $email
            ]);
        }
    }

    public function resetPasswordForm() {
        // Si l'utilisateur est déjà connecté, rediriger vers la gallery
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/gallery');
            return;
        }

        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $this->view('auth/reset-error', [
                'title' => 'Reset Error - Camagru'
            ]);
            return;
        }

        // Vérifier que le token existe et est valide
        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $this->view('auth/reset-error', [
                'title' => 'Reset Error - Camagru'
            ]);
            return;
        }

        $this->view('auth/reset-password', [
            'title' => 'Reset Password - Camagru',
            'token' => $token
        ]);
    }

    public function resetPassword() {
        // Si l'utilisateur est déjà connecté, rediriger vers la gallery
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/gallery');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/forgot-password');
            return;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token)) {
            $this->view('auth/reset-error', [
                'title' => 'Reset Error - Camagru'
            ]);
            return;
        }

        $errors = [];

        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            $this->view('auth/reset-password', [
                'title' => 'Reset Password - Camagru',
                'errors' => $errors,
                'token' => $token
            ]);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $this->view('auth/reset-error', [
                'title' => 'Reset Error - Camagru'
            ]);
            return;
        }

        // Mettre à jour le mot de passe et supprimer le token
        if ($userModel->resetPassword($user['id'], $password)) {
            $this->view('auth/reset-success', [
                'title' => 'Password Reset - Camagru'
            ]);
        } else {
            $this->view('auth/reset-error', [
                'title' => 'Reset Error - Camagru'
            ]);
        }
    }

    private function validateRegistration($username, $email, $password, $confirmPassword) {
        $errors = [];

        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        } elseif (strlen($username) > 50) {
            $errors[] = 'Username cannot exceed 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers and underscores';
        }

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address';
        }

        if (empty($password)) {
            $errors[] = 'Password is required';
        } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
        }

        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match';
        }

        return $errors;
    }
}
