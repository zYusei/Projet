<?php
/**
 * session.php
 * Démarrage de session sécurisé + helpers (current_user, is_logged_in, flash, csrf, require_login, logout)
 * À inclure en tout premier dans chaque page PHP.
 */

// Démarrage sécurisé
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Retourne l'utilisateur courant ou null */
function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

/** Vérifie si un utilisateur est connecté */
function is_logged_in(): bool {
    return !empty($_SESSION['user']);
}

/** Messages flash : écriture et lecture */
function flash(string $key, ?string $msg = null) {
    if ($msg === null) {
        if (!empty($_SESSION['flash'][$key])) {
            $m = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $m;
        }
        return null;
    }
    $_SESSION['flash'][$key] = $msg;
}

/** Génère un token CSRF */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

/** Vérifie un token CSRF */
function verify_csrf(string $token): bool {
    return hash_equals($_SESSION['csrf'] ?? '', $token);
}

/** Redirige si non connecté */
function require_login(): void {
    if (!is_logged_in()) {
        flash('error', 'Veuillez vous connecter pour continuer.');
        header('Location: connexion.php');
        exit;
    }
}

/** Déconnexion propre */
function logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
