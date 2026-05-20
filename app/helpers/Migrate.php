<?php

declare(strict_types=1);

final class Migrate
{
    /** @return list<string> */
    public static function pending(): array
    {
        self::ensureMigrationsTable();
        $applied = self::appliedVersions();
        $pending = [];
        foreach (self::migrationFiles() as $file) {
            $version = basename($file);
            if (!in_array($version, $applied, true)) {
                $pending[] = $version;
            }
        }
        return $pending;
    }

    /** @return list<string> */
    public static function run(): array
    {
        self::ensureMigrationsTable();
        $applied = [];
        $pdo = Database::pdo();
        foreach (self::pending() as $version) {
            $path = BASE_PATH . '/database/migrations/' . $version;
            $sql = (string) file_get_contents($path);
            self::runSql($sql);
            $stmt = $pdo->prepare('INSERT INTO schema_migrations (version) VALUES (?)');
            $stmt->execute([$version]);
            $applied[] = $version;
        }
        return $applied;
    }

    private static function runSql(string $sql): void
    {
        $pdo = Database::pdo();
        $buffer = '';
        foreach (preg_split('/\R/', $sql) as $line) {
            $trim = trim($line);
            if ($trim === '' || str_starts_with($trim, '--')) {
                continue;
            }
            $buffer .= $line . "\n";
            if (str_ends_with(rtrim($line), ';')) {
                $statement = trim($buffer);
                $buffer = '';
                if ($statement !== '') {
                    $pdo->exec($statement);
                }
            }
        }
        $tail = trim($buffer);
        if ($tail !== '') {
            $pdo->exec($tail);
        }
    }

    private static function ensureMigrationsTable(): void
    {
        Database::pdo()->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                version VARCHAR(64) NOT NULL PRIMARY KEY,
                applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
        );
    }

    /** @return list<string> */
    private static function appliedVersions(): array
    {
        $rows = Database::pdo()->query('SELECT version FROM schema_migrations ORDER BY version')->fetchAll();
        return array_map(static fn (array $r): string => (string) $r['version'], $rows);
    }

    /** @return list<string> */
    private static function migrationFiles(): array
    {
        $dir = BASE_PATH . '/database/migrations';
        $files = glob($dir . '/*.sql') ?: [];
        sort($files, SORT_STRING);
        return $files;
    }
}
