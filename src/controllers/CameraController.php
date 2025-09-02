<?php

class CameraController extends Controller {

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /gallery');
            exit;
        }

        $csrfToken = CSRFProtection::generateToken();
        error_log("DEBUG - Generated CSRF token: " . $csrfToken);

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

        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (!isset($_FILES['image']) || (!isset($_POST['frame']) && !isset($_POST['emoji']))) {
            echo json_encode(['success' => false, 'message' => 'Missing image or overlays']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $frame = $_POST['frame'] ?? 'none';
        $emoji = $_POST['emoji'] ?? 'none';
        $uploadFile = $_FILES['image'];

        if ($frame === 'none' && $emoji === 'none') {
            echo json_encode(['success' => false, 'message' => 'Please select at least one frame or emoji']);
            exit;
        }

        if ($uploadFile['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Upload error']);
            exit;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $uploadFile['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type']);
            exit;
        }

        if ($uploadFile['size'] > UPLOAD_MAX_SIZE) {
            echo json_encode(['success' => false, 'message' => 'File too large']);
            exit;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalFilename = $uploadFile['name'];
        $extension = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $filename = 'img_' . $userId . '_' . time() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        if (!move_uploaded_file($uploadFile['tmp_name'], $uploadPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save image']);
            exit;
        }

        $this->applyOverlays($uploadPath, $frame, $emoji);

        $imageModel = new Image();
        $relativeFilename = 'images/' . $filename;
        $overlayData = json_encode(['frame' => $frame, 'emoji' => $emoji]);
        if ($imageModel->create($userId, $relativeFilename, $originalFilename, $overlayData)) {
            echo json_encode(['success' => true, 'message' => 'Image uploaded successfully']);
        } else {
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

        $image = $imageModel->getById($imageId);
        if (!$image || $image['user_id'] != $userId) {
            echo json_encode(['success' => false, 'message' => 'Image not found or access denied']);
            exit;
        }

        if ($imageModel->delete($imageId, $userId)) {

            $filePath = __DIR__ . '/../../public/uploads/' . $image['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete image']);
        }
    }

    private function applyOverlays($imagePath, $frame, $emoji) {
        if ($frame === 'none' && $emoji === 'none') {
            return true;
        }

        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) return false;

        $mimeType = $imageInfo['mime'];
        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($imagePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($imagePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($imagePath);
                break;
            default:
                return false;
        }

        if (!$image) return false;

        $width = imagesx($image);
        $height = imagesy($image);

        if ($frame !== 'none') {
            $this->applyFrame($image, $frame, $width, $height);
        }

        if ($emoji !== 'none') {
            $this->applyEmoji($image, $emoji, $width, $height);
        }

        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($image, $imagePath, 100);
                break;
            case 'image/png':
                imagepng($image, $imagePath, 0);
                break;
            case 'image/gif':
                imagegif($image, $imagePath);
                break;
        }

        imagedestroy($image);
        return true;
    }    private function applyFrame($image, $frameType, $width, $height) {
        $frameWidth = 20;

        switch ($frameType) {
            case 'classic':
                $color = imagecolorallocate($image, 180, 137, 0);
                break;
            case 'modern':
                $color = imagecolorallocate($image, 55, 65, 81);
                break;
            case 'vintage':
                $color = imagecolorallocate($image, 194, 65, 12);
                break;
            default:
                return;
        }

        imagefilledrectangle($image, 0, 0, $width, $frameWidth, $color);

        imagefilledrectangle($image, 0, $height - $frameWidth, $width, $height, $color);

        imagefilledrectangle($image, 0, 0, $frameWidth, $height, $color);

        imagefilledrectangle($image, $width - $frameWidth, 0, $width, $height, $color);
    }

    private function applyEmoji($image, $emoji, $width, $height) {
        $emojiSize = min($width, $height) / 10;
        $x = $width - $emojiSize - 15;
        $y = $height - $emojiSize - 15;

        switch ($emoji) {
            case '😊':
                $this->drawSmiley($image, $x, $y, $emojiSize);
                break;
            case '❤️':
                $this->drawHeart($image, $x, $y, $emojiSize);
                break;
            case '🎉':
                $this->drawParty($image, $x, $y, $emojiSize);
                break;
            case '🔥':
                $this->drawFire($image, $x, $y, $emojiSize);
                break;
            case '✨':
                $this->drawStar($image, $x, $y, $emojiSize);
                break;
            case '🌟':
                $this->drawBigStar($image, $x, $y, $emojiSize);
                break;
            case '💯':
                $this->drawHundred($image, $x, $y, $emojiSize);
                break;
            default:
                $bgColor = imagecolorallocate($image, 255, 255, 255);
                imagefilledellipse($image, $x + $emojiSize/2, $y + $emojiSize/2, $emojiSize, $emojiSize, $bgColor);
        }
    }

    private function drawSmiley($image, $x, $y, $size) {
        $yellow = imagecolorallocate($image, 255, 223, 0);
        $black = imagecolorallocate($image, 0, 0, 0);

        imagefilledellipse($image, $x + $size/2, $y + $size/2, $size, $size, $yellow);

        $eyeSize = $size * 0.1;
        imagefilledellipse($image, $x + $size*0.35, $y + $size*0.35, $eyeSize, $eyeSize, $black);
        imagefilledellipse($image, $x + $size*0.65, $y + $size*0.35, $eyeSize, $eyeSize, $black);

        imagearc($image, $x + $size/2, $y + $size/2, $size*0.6, $size*0.4, 0, 180, $black);
    }

    private function drawHeart($image, $x, $y, $size) {
        $red = imagecolorallocate($image, 220, 20, 60);

        $heartSize = $size * 0.8;
        imagefilledellipse($image, $x + $heartSize*0.35, $y + $heartSize*0.4, $heartSize*0.5, $heartSize*0.5, $red);
        imagefilledellipse($image, $x + $heartSize*0.65, $y + $heartSize*0.4, $heartSize*0.5, $heartSize*0.5, $red);

        $points = array(
            $x + $heartSize*0.2, $y + $heartSize*0.6,
            $x + $heartSize*0.8, $y + $heartSize*0.6,
            $x + $heartSize*0.5, $y + $heartSize*1.0
        );
        imagefilledpolygon($image, $points, 3, $red);
    }

    private function drawParty($image, $x, $y, $size) {
        $colors = [
            imagecolorallocate($image, 255, 0, 0),
            imagecolorallocate($image, 0, 255, 0),
            imagecolorallocate($image, 0, 0, 255),
            imagecolorallocate($image, 255, 255, 0),
            imagecolorallocate($image, 255, 0, 255),
        ];

        for ($i = 0; $i < 8; $i++) {
            $confettiX = $x + rand(0, $size);
            $confettiY = $y + rand(0, $size);
            $confettiSize = $size * 0.1;
            $color = $colors[$i % count($colors)];
            imagefilledellipse($image, $confettiX, $confettiY, $confettiSize, $confettiSize, $color);
        }
    }

    private function drawFire($image, $x, $y, $size) {
        $orange = imagecolorallocate($image, 255, 165, 0);
        $red = imagecolorallocate($image, 255, 69, 0);
        $yellow = imagecolorallocate($image, 255, 255, 0);

        imagefilledellipse($image, $x + $size/2, $y + $size*0.8, $size*0.8, $size*0.4, $red);
        imagefilledellipse($image, $x + $size/2, $y + $size*0.6, $size*0.6, $size*0.6, $orange);
        imagefilledellipse($image, $x + $size/2, $y + $size*0.4, $size*0.4, $size*0.4, $yellow);
    }

    private function drawStar($image, $x, $y, $size) {
        $gold = imagecolorallocate($image, 255, 215, 0);

        $centerX = $x + $size/2;
        $centerY = $y + $size/2;
        $radius = $size/3;

        for ($i = 0; $i < 5; $i++) {
            $angle = $i * 2 * M_PI / 5 - M_PI/2;
            $pointX = $centerX + cos($angle) * $radius;
            $pointY = $centerY + sin($angle) * $radius;
            imagefilledellipse($image, $pointX, $pointY, $size*0.15, $size*0.15, $gold);
        }

        imagefilledellipse($image, $centerX, $centerY, $size*0.3, $size*0.3, $gold);
    }

    private function drawBigStar($image, $x, $y, $size) {
        $gold = imagecolorallocate($image, 255, 215, 0);
        $yellow = imagecolorallocate($image, 255, 255, 0);

        $centerX = $x + $size/2;
        $centerY = $y + $size/2;

        imagefilledellipse($image, $centerX, $centerY, $size*0.8, $size*0.8, $gold);
        imagefilledellipse($image, $centerX, $centerY, $size*0.5, $size*0.5, $yellow);

        for ($i = 0; $i < 8; $i++) {
            $angle = $i * M_PI / 4;
            $lineX = $centerX + cos($angle) * $size*0.4;
            $lineY = $centerY + sin($angle) * $size*0.4;
            imageline($image, $centerX, $centerY, $lineX, $lineY, $yellow);
        }
    }

    private function drawHundred($image, $x, $y, $size) {
        $red = imagecolorallocate($image, 220, 20, 60);
        $white = imagecolorallocate($image, 255, 255, 255);

        imagefilledrectangle($image, $x, $y, $x + $size, $y + $size*0.6, $red);

        $font = 3;
        $text = "100";
        $textWidth = imagefontwidth($font) * strlen($text);
        $textHeight = imagefontheight($font);
        $textX = $x + ($size - $textWidth) / 2;
        $textY = $y + ($size*0.6 - $textHeight) / 2;

        imagestring($image, $font, $textX, $textY, $text, $white);
    }
}
