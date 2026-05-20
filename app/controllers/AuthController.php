<?php

declare(strict_types=1);

final class AuthController
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            header('Location: ' . Router::url(Auth::isPartner() ? '/cars' : '/dashboard'));
            exit;
        }
        View::render('auth/login', ['title' => Lang::get('auth.login_title')], 'auth');
    }

    public function login(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error(Lang::get('error.csrf'));
            header('Location: ' . Router::url('/login'));
            exit;
        }
        if (empty($_POST['privacy_accept'])) {
            Flash::error(Lang::get('error.privacy_required'));
            header('Location: ' . Router::url('/login'));
            exit;
        }
        if (LoginRateLimiter::tooManyAttempts()) {
            Flash::error(Lang::get('auth.rate_limited'));
            header('Location: ' . Router::url('/login'));
            exit;
        }
        $email = trim((string) ($_POST['email'] ?? ''));
        $pass = (string) ($_POST['password'] ?? '');
        $user = User::findByEmail($email);
        if (!$user || !(int) $user['is_active'] || !password_verify($pass, $user['password_hash'])) {
            LoginRateLimiter::hit();
            Flash::error(Lang::get('auth.invalid'));
            header('Location: ' . Router::url('/login'));
            exit;
        }
        LoginRateLimiter::clear();
        Auth::login($user);
        self::logPrivacyConsent((int) $user['id']);
        Flash::success(Lang::get('auth.welcome'));
        $dest = Auth::isPartner() ? '/cars' : '/dashboard';
        header('Location: ' . Router::url($dest));
        exit;
    }

    public function logout(): void
    {
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Flash::error(Lang::get('error.csrf'));
            header('Location: ' . Router::url('/dashboard'));
            exit;
        }
        Auth::logout();
        header('Location: ' . Router::url('/login'));
        exit;
    }

    private static function logPrivacyConsent(int $userId): void
    {
        try {
            $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
            $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
            $stmt = Database::pdo()->prepare(
                'INSERT INTO privacy_login_consent (user_id, ip_hash, user_agent_hash, created_at) VALUES (?, ?, ?, NOW())'
            );
            $stmt->execute([
                $userId,
                hash('sha256', $ip),
                hash('sha256', $ua),
            ]);
        } catch (\Throwable) {
            /* Tabela pode ainda não existir — executar migration 003 */
        }
    }
}
