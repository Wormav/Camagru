<?php

class ProfileController extends Controller {

    public function index() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        if (!$user) {
            session_destroy();
            $this->redirect('/login');
            return;
        }

        $this->view('profile/index', [
            'title' => 'Profile - Camagru',
            'user' => $user
        ]);
    }

    public function update() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
            return;
        }

        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        if (!$user) {
            session_destroy();
            $this->redirect('/login');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        // Validation des champs de base
        if (empty($username)) {
            $errors[] = 'Username is required';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = 'Username must be between 3 and 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username can only contain letters, numbers and underscores';
        }

        if (empty($email)) {
            $errors[] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address';
        }

        // Vérifier que l'username n'est pas déjà pris (sauf par l'utilisateur actuel)
        if ($userModel->checkUsernameExists($username, $_SESSION['user_id'])) {
            $errors[] = 'Username is already taken';
        }

        // Vérifier que l'email n'est pas déjà pris (sauf par l'utilisateur actuel)
        if ($userModel->checkEmailExists($email, $_SESSION['user_id'])) {
            $errors[] = 'Email is already taken';
        }

        // Si un nouveau mot de passe est fourni, valider
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                $errors[] = 'Current password is required to change password';
            } elseif (!$userModel->verifyPassword($_SESSION['user_id'], $currentPassword)) {
                $errors[] = 'Current password is incorrect';
            }

            if (strlen($newPassword) < 8) {
                $errors[] = 'New password must be at least 8 characters long';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New password confirmation does not match';
            }
        }

        if (!empty($errors)) {
            $this->view('profile/index', [
                'title' => 'Profile - Camagru',
                'user' => $user,
                'errors' => $errors,
                'username' => $username,
                'email' => $email
            ]);
            return;
        }

        // Mettre à jour le profil
        if ($userModel->updateProfile($_SESSION['user_id'], $username, $email)) {
            // Si un nouveau mot de passe est fourni, le mettre à jour aussi
            if (!empty($newPassword)) {
                $userModel->updatePassword($_SESSION['user_id'], $newPassword);
            }

            // Mettre à jour la session avec les nouvelles informations
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;

            $_SESSION['success'] = 'Profile updated successfully!';
        } else {
            $_SESSION['error'] = 'An error occurred while updating your profile. Please try again.';
        }

        $this->redirect('/profile');
    }

    public function uploadProfilePicture() {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profile');
            return;
        }

        if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Please select a valid image file.';
            $this->redirect('/profile');
            return;
        }

        $file = $_FILES['profile_picture'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Validation du fichier
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = 'Only JPEG, PNG and GIF images are allowed.';
            $this->redirect('/profile');
            return;
        }

        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = 'Image file is too large. Maximum size is 5MB.';
            $this->redirect('/profile');
            return;
        }

        // Créer le dossier de profil s'il n'existe pas
        $uploadDir = __DIR__ . '/../../public/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'profile_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        $filePath = $uploadDir . $fileName;

        // Supprimer l'ancienne photo de profil si elle existe
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        if ($user && $user['profile_picture']) {
            $oldFilePath = $uploadDir . $user['profile_picture'];
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // Déplacer le fichier uploadé
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Mettre à jour la base de données
            if ($userModel->updateProfilePicture($_SESSION['user_id'], $fileName)) {
                // Mettre à jour la session avec la nouvelle photo
                $_SESSION['profile_picture'] = $fileName;
                $_SESSION['success'] = 'Profile picture updated successfully!';
            } else {
                $_SESSION['error'] = 'An error occurred while updating your profile picture.';
                // Supprimer le fichier en cas d'erreur de base de données
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        } else {
            $_SESSION['error'] = 'Failed to upload the image. Please try again.';
        }

        $this->redirect('/profile');
    }
}
