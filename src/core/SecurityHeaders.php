<?php

class SecurityHeaders {
    public static function setSecurityHeaders() {
        // Protection XSS
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');

        // CSP plus permissif pour Tailwind CDN (à affiner en production)
        // header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https:; img-src 'self' data:; font-src 'self' https:; connect-src 'self' https:");

        // CSP temporairement désactivé pour debug Tailwind
        // Réactiver après avoir résolu les problèmes de style

        // HTTPS uniquement en production
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        // Protection contre le clickjacking
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
