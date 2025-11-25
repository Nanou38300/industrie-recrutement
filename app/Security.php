<?php

namespace App;

/**
 * Classe Security - Gestion centralisée de la sécurité
 * Protection CSRF, validation inputs, gestion sessions sécurisées
 */
class Security
{
    private const TOKEN_NAME = 'csrf_token';
    private const TOKEN_TIME_NAME = 'csrf_token_time';
    private const TOKEN_LIFETIME = 3600; // 1 heure

    /**
     * Génère un token CSRF et le stocke en session
     */
    public static function generateCSRFToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION[self::TOKEN_NAME] = $token;
        $_SESSION[self::TOKEN_TIME_NAME] = time();

        return $token;
    }

    /**
     * Vérifie la validité du token CSRF
     */
    public static function validateCSRFToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier que le token existe en session
        if (!isset($_SESSION[self::TOKEN_NAME]) || !isset($_SESSION[self::TOKEN_TIME_NAME])) {
            return false;
        }

        // Vérifier l'expiration du token
        if (time() - $_SESSION[self::TOKEN_TIME_NAME] > self::TOKEN_LIFETIME) {
            self::destroyCSRFToken();
            return false;
        }

        // Comparer les tokens de manière sécurisée
        return hash_equals($_SESSION[self::TOKEN_NAME], $token);
    }

    /**
     * Détruit le token CSRF de la session
     */
    public static function destroyCSRFToken(): void
    {
        unset($_SESSION[self::TOKEN_NAME], $_SESSION[self::TOKEN_TIME_NAME]);
    }

    /**
     * Récupère le token CSRF actuel ou en génère un nouveau
     */
    public static function getCSRFToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::TOKEN_NAME]) || 
            !isset($_SESSION[self::TOKEN_TIME_NAME]) ||
            time() - $_SESSION[self::TOKEN_TIME_NAME] > self::TOKEN_LIFETIME) {
            return self::generateCSRFToken();
        }

        return $_SESSION[self::TOKEN_NAME];
    }

    /**
     * Génère un champ input hidden pour les formulaires
     */
    public static function getCSRFInput(): string
    {
        $token = self::getCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Valide le token CSRF depuis la requête POST
     */
    public static function validateRequest(): bool
    {
        $token = $_POST['csrf_token'] ?? '';
        return self::validateCSRFToken($token);
    }

    /**
     * Filtre et valide une entrée utilisateur
     */
    public static function sanitizeInput(string $input, int $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS): string
    {
        return filter_var(trim($input), $filter);
    }

    /**
     * Valide un email
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valide un entier
     */
    public static function validateInt($value): ?int
    {
        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered !== false ? $filtered : null;
    }

    /**
     * Échappe pour HTML (protection XSS)
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Régénère l'ID de session (après login)
     */
    public static function regenerateSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    /**
     * Configure les paramètres de session sécurisés
     */
    public static function configureSecureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_secure', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? '1' : '0');
            ini_set('session.gc_maxlifetime', '3600'); // 1 heure
            
            session_start();
        }
    }

    /**
     * Vérifie le timeout de session (30 minutes d'inactivité)
     */
    public static function checkSessionTimeout(int $timeout = 1800): bool
    {
        if (isset($_SESSION['LAST_ACTIVITY'])) {
            if (time() - $_SESSION['LAST_ACTIVITY'] > $timeout) {
                session_unset();
                session_destroy();
                return false;
            }
        }
        $_SESSION['LAST_ACTIVITY'] = time();
        return true;
    }

    /**
     * Vérifie si l'utilisateur est authentifié
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['utilisateur']['id']);
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public static function hasRole(string $role): bool
    {
        return self::isAuthenticated() && ($_SESSION['utilisateur']['role'] ?? '') === $role;
    }

    /**
     * Redirige si non authentifié
     */
    public static function requireAuth(string $redirectUrl = '/utilisateur/login'): void
    {
        if (!self::isAuthenticated()) {
            header("Location: $redirectUrl");
            exit;
        }
    }

    /**
     * Redirige si n'a pas le rôle requis
     */
    public static function requireRole(string $role, string $redirectUrl = '/'): void
    {
        if (!self::hasRole($role)) {
            header("Location: $redirectUrl");
            exit;
        }
    }

    /**
     * Valide un upload de fichier
     */
    public static function validateFileUpload(array $file, array $allowedExtensions = [], int $maxSize = 5242880): array
    {
        $errors = [];

        // Vérifier les erreurs d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur lors de l'upload du fichier.";
            return $errors;
        }

        // Vérifier la taille
        if ($file['size'] > $maxSize) {
            $errors[] = "Le fichier est trop volumineux (max: " . ($maxSize / 1024 / 1024) . " MB).";
        }

        // Vérifier l'extension
        if (!empty($allowedExtensions)) {
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedExtensions, true)) {
                $errors[] = "Extension de fichier non autorisée. Extensions autorisées: " . implode(', ', $allowedExtensions);
            }
        }

        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'image/gif'
        ];

        if (!in_array($mimeType, $allowedMimes, true)) {
            $errors[] = "Type de fichier non autorisé.";
        }

        return $errors;
    }

    /**
     * Génère un nom de fichier sécurisé
     */
    public static function generateSecureFilename(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $basename = pathinfo($originalName, PATHINFO_FILENAME);
        $basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $basename);
        $uniqueId = uniqid('', true);
        
        return $basename . '_' . $uniqueId . '.' . $extension;
    }

    /**
     * Limite le taux de tentatives (rate limiting simple)
     */
    public static function rateLimitCheck(string $key, int $maxAttempts = 5, int $timeWindow = 300): bool
    {
        if (!isset($_SESSION['rate_limit'][$key])) {
            $_SESSION['rate_limit'][$key] = [
                'attempts' => 1,
                'first_attempt' => time()
            ];
            return true;
        }

        $data = $_SESSION['rate_limit'][$key];

        // Réinitialiser si la fenêtre de temps est dépassée
        if (time() - $data['first_attempt'] > $timeWindow) {
            $_SESSION['rate_limit'][$key] = [
                'attempts' => 1,
                'first_attempt' => time()
            ];
            return true;
        }

        // Incrémenter les tentatives
        $_SESSION['rate_limit'][$key]['attempts']++;

        // Vérifier si le maximum est atteint
        return $_SESSION['rate_limit'][$key]['attempts'] <= $maxAttempts;
    }

    /**
     * Log des activités de sécurité (basique)
     */
    public static function logSecurityEvent(string $event, array $context = []): void
    {
        $logFile = __DIR__ . '/../logs/security.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userId = $_SESSION['utilisateur']['id'] ?? 'guest';
        $contextJson = json_encode($context);

        $logMessage = "[$timestamp] [$ip] [User:$userId] $event - $contextJson" . PHP_EOL;
        
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}
