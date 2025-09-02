<?php

class EmailSender {

    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] EMAIL: $message\n";
        file_put_contents('/tmp/camagru_email.log', $logMessage, FILE_APPEND | LOCK_EX);
        error_log($logMessage);
    }

    public function sendVerificationEmail($email, $username, $token) {
        $this->log("=== DÉBUT ENVOI EMAIL DE VÉRIFICATION ===");
        $this->log("Destinataire: $email");
        $this->log("Nom d'utilisateur: $username");
        $this->log("Token: $token");

        $subject = 'Verify your Camagru account';
        $verificationLink = APP_URL . "/verify?token=" . $token;

        $message = "Hello $username,\n\n";
        $message .= "Thank you for registering with Camagru!\n\n";
        $message .= "Please click the following link to verify your email address:\n";
        $message .= $verificationLink . "\n\n";
        $message .= "If you did not create this account, you can safely ignore this email.\n\n";
        $message .= "Best regards,\n";
        $message .= "The Camagru Team";

        $this->log("Sujet: $subject");
        $this->log("Lien de vérification: $verificationLink");

        $this->log("MAIL_HOST: " . (defined('MAIL_HOST') ? MAIL_HOST : 'NON DÉFINI'));
        $this->log("MAIL_PORT: " . (defined('MAIL_PORT') ? MAIL_PORT : 'NON DÉFINI'));
        $this->log("MAIL_USERNAME: " . (defined('MAIL_USERNAME') ? MAIL_USERNAME : 'NON DÉFINI'));
        $this->log("MAIL_PASSWORD: " . (defined('MAIL_PASSWORD') ? (strlen(MAIL_PASSWORD) . ' caractères') : 'NON DÉFINI'));
        $this->log("MAIL_FROM: " . (defined('MAIL_FROM') ? MAIL_FROM : 'NON DÉFINI'));
        $this->log("APP_ENV: " . (defined('APP_ENV') ? APP_ENV : 'NON DÉFINI'));

        $this->log("TENTATIVE D'ENVOI SMTP...");
        try {
            $result = $this->sendViaSMTP($email, $subject, $message);
            $this->log("Résultat envoi SMTP: " . ($result ? 'SUCCÈS' : 'ÉCHEC'));
            return $result;
        } catch (Exception $e) {
            $this->log("ERREUR SMTP: " . $e->getMessage());

            $this->log("Tentative avec mail() + configuration SMTP...");
            $result = $this->sendWithMailFunction($email, $subject, $message);
            $this->log("Résultat fallback: " . ($result ? 'SUCCÈS' : 'ÉCHEC'));
            return $result;
        }
    }

    private function sendViaSMTP($to, $subject, $message) {
        $this->log("=== CONNEXION SMTP ===");

        if (!defined('MAIL_HOST') || !defined('MAIL_USERNAME') || !defined('MAIL_PASSWORD')) {
            throw new Exception("Configuration SMTP incomplète");
        }

        $host = MAIL_HOST;
        $port = MAIL_PORT;
        $username = MAIL_USERNAME;
        $password = MAIL_PASSWORD;
        $from = MAIL_FROM;

        $this->log("Connexion à $host:$port");

        $smtp = fsockopen($host, $port, $errno, $errstr, 30);
        if (!$smtp) {
            throw new Exception("Impossible de se connecter à $host:$port - $errno: $errstr");
        }

        $this->log("Connexion établie");

        $response = $this->readSMTPResponse($smtp);
        $this->log("Réponse initiale: " . trim($response));

        fputs($smtp, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n");
        $response = $this->readSMTPResponse($smtp);
        $this->log("EHLO: " . trim($response));

        fputs($smtp, "STARTTLS\r\n");
        $response = $this->readSMTPResponse($smtp);
        $this->log("STARTTLS: " . trim($response));

        if (strpos($response, '220') !== 0) {
            throw new Exception("STARTTLS échoué: " . trim($response));
        }

        $this->log("Tentative d'activation TLS...");

        if (stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
            $this->log("TLS 1.2 activé avec succès");
        } elseif (stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT)) {
            $this->log("TLS 1.1 activé avec succès");
        } elseif (stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            $this->log("TLS générique activé avec succès");
        } else {
            throw new Exception("Impossible d'activer TLS - Toutes les versions ont échoué");
        }


        fputs($smtp, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n");
        $response = $this->readSMTPResponse($smtp);
        $this->log("EHLO après TLS: " . trim($response));

        fputs($smtp, "AUTH LOGIN\r\n");
        $response = $this->readSMTPResponse($smtp);
        $this->log("AUTH LOGIN: " . trim($response));

        fputs($smtp, base64_encode($username) . "\r\n");
        $response = $this->readSMTPResponse($smtp);
        $this->log("Username: " . trim($response));


        fputs($smtp, base64_encode($password) . "\r\n");
        $response = $this->readSMTPResponse($smtp);
        $this->log("Password: " . trim($response));

        fputs($smtp, "MAIL FROM: <$from>\r\n");
        $response = $this->readSMTPResponse($smtp);
        $this->log("MAIL FROM: " . trim($response));

        fputs($smtp, "RCPT TO: <$to>\r\n");
        $response = $this->readSMTPResponse($smtp);
        $this->log("RCPT TO: " . trim($response));

        fputs($smtp, "DATA\r\n");
        $response = $this->readSMTPResponse($smtp);
        $this->log("DATA: " . trim($response));

        $headers = "From: $from\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "\r\n";

        fputs($smtp, $headers . $message . "\r\n.\r\n");
        $response = $this->readSMTPResponse($smtp);
        $this->log("Message envoyé: " . trim($response));

        fputs($smtp, "QUIT\r\n");
        fclose($smtp);

        $this->log("=== ENVOI SMTP TERMINÉ ===");

        return strpos($response, '250') === 0;
    }

    public function sendPasswordResetEmail($email, $username, $token) {
        $this->log("=== DÉBUT ENVOI EMAIL DE RESET ===");

        $subject = 'Reset your Camagru password';
        $resetLink = APP_URL . "/reset-password?token=" . $token;

        $message = "Hello $username,\n\n";
        $message .= "You have requested to reset your password.\n\n";
        $message .= "Please click the following link to reset your password:\n";
        $message .= $resetLink . "\n\n";
        $message .= "If you did not request this, you can safely ignore this email.\n\n";
        $message .= "Best regards,\n";
        $message .= "The Camagru Team";

        try {
            $result = $this->sendViaSMTP($email, $subject, $message);
            return $result;
        } catch (Exception $e) {
            $this->log("ERREUR: " . $e->getMessage());
            return false;
        }
    }

    private function readSMTPResponse($smtp) {
        $response = '';
        while (true) {
            $line = fgets($smtp, 515);
            $response .= $line;

            if (preg_match('/^\d{3} /', $line)) {
                break;
            }
        }
        return $response;
    }

    private function sendWithMailFunction($to, $subject, $message) {
        $this->log("=== ENVOI AVEC MAIL() + CONFIGURATION SMTP ===");

        ini_set('SMTP', MAIL_HOST);
        ini_set('smtp_port', MAIL_PORT);
        ini_set('sendmail_from', MAIL_FROM);

        $headers = array(
            'From: ' . MAIL_FROM,
            'Reply-To: ' . MAIL_FROM,
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0',
            'Content-type: text/plain; charset=UTF-8'
        );

        $headersString = implode("\r\n", $headers);

        $this->log("Headers: " . $headersString);

        $result = mail($to, $subject, $message, $headersString);
        $this->log("Résultat mail(): " . ($result ? 'SUCCÈS' : 'ÉCHEC'));

        if (!$result) {
            $error = error_get_last();
            $this->log("Erreur mail(): " . ($error ? $error['message'] : 'Inconnue'));
        }

        return $result;
    }

    public function send($email, $subject, $message) {
        $this->log("=== ENVOI EMAIL GÉNÉRIQUE ===");
        $this->log("Destinataire: $email");
        $this->log("Sujet: $subject");

        $this->log("TENTATIVE D'ENVOI SMTP...");
        try {
            $result = $this->sendViaSMTP($email, $subject, $message);
            $this->log("Résultat envoi SMTP: " . ($result ? 'SUCCÈS' : 'ÉCHEC'));
            return $result;
        } catch (Exception $e) {
            $this->log("ERREUR SMTP: " . $e->getMessage());

            $this->log("Tentative avec mail()...");
            try {
                $result = $this->sendWithMailFunction($email, $subject, $message);
                $this->log("Résultat envoi mail(): " . ($result ? 'SUCCÈS' : 'ÉCHEC'));
                return $result;
            } catch (Exception $e) {
                $this->log("ERREUR mail(): " . $e->getMessage());
                return false;
            }
        }
    }
}
