<?php

class EmailSender {

    public function sendVerificationEmail($email, $username, $token) {
        $subject = 'Verify your Camagru account';
        $verificationLink = APP_URL . "/verify?token=" . $token;

        $message = "Hello $username,\n\n";
        $message .= "Thank you for registering with Camagru!\n\n";
        $message .= "Please click the following link to verify your email address:\n";
        $message .= $verificationLink . "\n\n";
        $message .= "If you did not create this account, you can safely ignore this email.\n\n";
        $message .= "Best regards,\n";
        $message .= "The Camagru Team";

        $headers = "From: " . MAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . MAIL_FROM . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        return mail($email, $subject, $message, $headers);
    }

    public function sendPasswordResetEmail($email, $username, $token) {
        $subject = 'Reset your Camagru password';
        $resetLink = APP_URL . "/reset-password?token=" . $token;

        $message = "Hello $username,\n\n";
        $message .= "You have requested to reset your password.\n\n";
        $message .= "Please click the following link to reset your password:\n";
        $message .= $resetLink . "\n\n";
        $message .= "If you did not request this, you can safely ignore this email.\n\n";
        $message .= "Best regards,\n";
        $message .= "The Camagru Team";

        $headers = "From: " . MAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . MAIL_FROM . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        return mail($email, $subject, $message, $headers);
    }
}
