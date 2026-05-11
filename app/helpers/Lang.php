<?php

declare(strict_types=1);

final class Lang
{
    private static ?array $strings = null;

    public static function locale(): string
    {
        if (!empty($_SESSION['lang']) && in_array($_SESSION['lang'], ['pt-BR', 'en-US'], true)) {
            return $_SESSION['lang'];
        }
        if (!empty($_SESSION['user']['lang_pref'])) {
            return $_SESSION['user']['lang_pref'];
        }
        $app = require BASE_PATH . '/config/app.php';
        return $app['default_lang'];
    }

    public static function setLocale(string $locale): void
    {
        if (in_array($locale, ['pt-BR', 'en-US'], true)) {
            $_SESSION['lang'] = $locale;
        }
    }

    public static function load(): array
    {
        if (self::$strings !== null) {
            return self::$strings;
        }
        $file = BASE_PATH . '/lang/' . self::locale() . '.php';
        self::$strings = is_readable($file) ? (require $file) : [];
        return self::$strings;
    }

    public static function get(string $key, array $replace = []): string
    {
        $all = self::load();
        $text = $all[$key] ?? $key;
        foreach ($replace as $k => $v) {
            $text = str_replace(':' . $k, (string) $v, $text);
        }
        return $text;
    }

    public static function e(string $key, array $replace = []): string
    {
        return htmlspecialchars(self::get($key, $replace), ENT_QUOTES, 'UTF-8');
    }
}
