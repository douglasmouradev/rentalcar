<?php

declare(strict_types=1);

final class Auth
{
    public static function check(): bool
    {
        return !empty($_SESSION['user']['id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function isOwner(): bool
    {
        return self::role() === 'owner';
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'lang_pref' => $user['lang_pref'] ?? 'pt-BR',
        ];
        if (!empty($_SESSION['lang'])) {
            return;
        }
        $_SESSION['lang'] = $user['lang_pref'] ?? 'pt-BR';
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function refreshUserFromDb(): void
    {
        $id = self::id();
        if ($id === null) {
            return;
        }
        $stmt = Database::pdo()->prepare('SELECT id, name, email, role, lang_pref FROM users WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            self::login($row);
        }
    }
}
