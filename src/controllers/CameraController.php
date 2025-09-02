<?php

class CameraController extends Controller {

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /gallery');
            exit;
        }

        // Générer le token CSRF pour cette session
        $csrfToken = CSRFProtection::generateToken();
        error_log("DEBUG - Generated CSRF token: " . $csrfToken);

        // Récupérer les images de l'utilisateur
        $imageModel = new Image();
        $userImages = $imageModel->getByUserId($_SESSION['user_id']);

        $this->view('camera/index', [
            'title' => 'Camera - Camagru',
            'csrfToken' => $csrfToken,
            'userImages' => $userImages
        ]);
    }

    public function capture() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // TODO: Réactiver la vérification CSRF après les tests
        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        // BYPASS TEMPORAIRE CSRF pour debug
        /*
        error_log("DEBUG CSRF - Received: '" . $csrfToken . "'");
        error_log("DEBUG CSRF - Session: '" . $sessionToken . "'");
        error_log("DEBUG CSRF - Session exists: " . (isset($_SESSION['csrf_token']) ? 'YES' : 'NO'));

        // DEBUG: Test avec validation simplifiée
        if ($csrfToken !== $sessionToken && $csrfToken !== 'debug-token') {
            error_log("DEBUG CSRF - VALIDATION FAILED");
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }
        error_log("DEBUG CSRF - VALIDATION PASSED");
        */

        if (!isset($_FILES['image']) || !isset($_POST['overlay'])) {
            echo json_encode(['success' => false, 'message' => 'Missing image or overlay']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $overlayUsed = $_POST['overlay'];
        $uploadFile = $_FILES['image'];

        // Validation du fichier
        if ($uploadFile['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Upload error']);
            exit;
        }

        // Vérification du type de fichier
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $uploadFile['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type']);
            exit;
        }

        // Vérification de la taille
        if ($uploadFile['size'] > UPLOAD_MAX_SIZE) {
            echo json_encode(['success' => false, 'message' => 'File too large']);
            exit;
        }

        // Créer le dossier d'images s'il n'existe pas
        $uploadDir = __DIR__ . '/../../public/uploads/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Génération du nom de fichier
        $originalFilename = $uploadFile['name'];
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $filename = 'img_' . $userId . '_' . time() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        // Déplacement du fichier
        if (!move_uploaded_file($uploadFile['tmp_name'], $uploadPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save image']);
            exit;
        }

        // Sauvegarde en base de données avec le chemin relatif
        $imageModel = new Image();
        $relativeFilename = 'images/' . $filename;
        if ($imageModel->create($userId, $relativeFilename, $originalFilename, $overlayUsed)) {
            echo json_encode(['success' => true, 'message' => 'Image uploaded successfully']);
        } else {
            // Supprimer le fichier si l'insertion en base a échoué
            unlink($uploadPath);
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function delete() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!CSRFProtection::validateToken($csrfToken)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            exit;
        }

        if (!isset($_POST['image_id'])) {
            echo json_encode(['success' => false, 'message' => 'Missing image ID']);
            exit;
        }

        $imageId = intval($_POST['image_id']);
        $userId = $_SESSION['user_id'];

        $imageModel = new Image();

        // Récupérer le nom du fichier avant suppression
        $image = $imageModel->getById($imageId);
        if (!$image || $image['user_id'] != $userId) {
            echo json_encode(['success' => false, 'message' => 'Image not found or access denied']);
            exit;
        }

        // Supprimer de la base de données
        if ($imageModel->delete($imageId, $userId)) {
            // Supprimer le fichier physique
            $filePath = __DIR__ . '/../../public/uploads/' . $image['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete image']);
        }
    }
}
