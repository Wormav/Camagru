<?php

class ImageController extends Controller {

    public function show($id) {
        $imageModel = new Image();
        $image = $imageModel->getByIdWithUser($id);

        if (!$image) {
            header('Location: /gallery');
            exit;
        }

        $this->view('image/show', [
            'title' => 'Image Detail - Camagru',
            'image' => $image
        ]);
    }

    public function likeStatus() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_GET['image_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $imageId = (int)$_GET['image_id'];

        $imageModel = new Image();
        $isLiked = $imageModel->isLikedByUser($userId, $imageId);

        echo json_encode(['success' => true, 'liked' => $isLiked]);
    }

    public function getComments() {
        header('Content-Type: application/json');

        if (!isset($_GET['image_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $imageId = (int)$_GET['image_id'];
        $imageModel = new Image();
        $comments = $imageModel->getComments($imageId);

        echo json_encode(['success' => true, 'comments' => $comments]);
    }

    public function like() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        if (!isset($_POST['image_id'])) {
            echo json_encode(['success' => false, 'message' => 'Missing image ID']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $imageId = (int)$_POST['image_id'];

        try {
            $imageModel = new Image();
            $result = $imageModel->toggleLike($userId, $imageId);
            $likeCount = $imageModel->getLikeCount($imageId);

            echo json_encode([
                'success' => true,
                'liked' => $result,
                'like_count' => $likeCount
            ]);
        } catch (Exception $e) {
            error_log("Error in like controller: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to toggle like']);
        }
    }

    public function comment() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        if (!isset($_POST['image_id']) || !isset($_POST['comment'])) {
            echo json_encode(['success' => false, 'message' => 'Missing data']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $imageId = (int)$_POST['image_id'];
        $comment = trim($_POST['comment']);

        if (empty($comment)) {
            echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
            exit;
        }

        $imageModel = new Image();
        $commentId = $imageModel->addComment($userId, $imageId, $comment);

        if ($commentId) {
            $imageData = $imageModel->getByIdWithUser($imageId);
            if ($imageData && $imageData['user_id'] != $userId) {
                $this->sendCommentNotification($imageData, $comment);
            }

            echo json_encode(['success' => true, 'comment_id' => $commentId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
        }
    }

    public function deleteComment() {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        if (!isset($_POST['comment_id'])) {
            echo json_encode(['success' => false, 'message' => 'Missing comment ID']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $commentId = (int)$_POST['comment_id'];

        $imageModel = new Image();
        if ($imageModel->deleteComment($commentId, $userId)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete comment']);
        }
    }

    private function sendCommentNotification($imageData, $comment) {
        $userModel = new User();
        $author = $userModel->findById($imageData['user_id']);

        if ($author && $author['notifications_enabled']) {
            $commenterData = $userModel->findById($_SESSION['user_id']);

            $subject = "New comment on your photo - Camagru";

            $message = "Hello {$author['username']},\n\n";
            $message .= "{$commenterData['username']} just commented on your photo:\n\n";
            $message .= "\"" . htmlspecialchars_decode($comment) . "\"\n\n";
            $message .= "View your photo and respond: " . (defined('APP_URL') ? APP_URL : 'http://localhost') . "/image/{$imageData['id']}\n\n";
            $message .= "You can disable these notifications in your profile settings.\n\n";
            $message .= "Best regards,\n";
            $message .= "The Camagru Team";

            try {
                $emailSender = new EmailSender();
                $result = $emailSender->send($author['email'], $subject, $message);

                if ($result) {
                    error_log("Comment notification sent successfully to {$author['email']}");
                } else {
                    error_log("Failed to send comment notification to {$author['email']}");
                }
            } catch (Exception $e) {
                error_log("Error sending comment notification: " . $e->getMessage());
            }
        }
    }
}
