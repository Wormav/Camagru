<?php

class AuthController extends Controller {

    public function registerForm() {
        $this->view('auth/register', [
            'title' => 'Register - Camagru'
        ]);
    }

    public function register() {
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
