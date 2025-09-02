# Test de sécurité Camagru

## Tests de protection CSRF

### 1. Test de formulaire sans token CSRF
Testez en supprimant manuellement le champ `csrf_token` d'un formulaire pour vérifier que la validation fonctionne.

### 2. Test avec token CSRF invalide
Testez en modifiant la valeur du token CSRF pour vérifier le rejet.

### 3. Vérification des headers de sécurité
Utilisez les outils de développement du navigateur pour vérifier la présence des headers de sécurité :
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`
- `Content-Security-Policy`

## Checklist de sécurité

### ✅ Protection SQL Injection
- [x] Requêtes préparées utilisées dans tous les modèles
- [x] Configuration PDO sécurisée

### ✅ Protection XSS
- [x] `htmlspecialchars()` utilisé pour échapper les données
- [x] Headers de sécurité configurés

### ✅ Protection CSRF (Nouvellement implémentée)
- [x] Classe `CSRFProtection` créée
- [x] Tokens CSRF ajoutés dans tous les formulaires :
  - [x] Login
  - [x] Register
  - [x] Forgot Password
  - [x] Reset Password
  - [x] Profile Update
  - [x] Profile Picture Upload
- [x] Validation CSRF dans tous les contrôleurs
- [x] Headers de sécurité configurés

## Utilisation

Les tokens CSRF sont maintenant automatiquement générés et validés.
Chaque formulaire inclut automatiquement le token via `<?= CSRFProtection::getTokenField() ?>`.

Les contrôleurs valident automatiquement le token avec `CSRFProtection::validateToken($token)`.
