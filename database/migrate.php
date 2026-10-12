<?php
/**
 * Runner migrasi sederhana (CLI).
 *   php database/migrate.php            -> jalankan migrasi yang belum diterapkan
 *   php database/migrate.php --status   -> tampilkan status tiap file
 * Migrasi yang sudah diterapkan dicatat di tabel schema_migrations.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config/config.php';

$pdo = Database::getConnection();
$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        filename varchar(190) NOT NULL PRIMARY KEY,
        applied_at timestamp NOT NULL DEFAULT current_timestamp()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
);

$applied = array_column($pdo->query('SELECT filename FROM schema_migrations')->fetchAll(), 'filename');
$files = glob(__DIR__ . '/migrations/*.sql') ?: [];
sort($files);
$statusOnly = in_array('--status', $argv ?? [], true);
$ran = 0;

foreach ($files as $file) {
    $name = basename($file);
    $done = in_array($name, $applied, true);
    if ($statusOnly) {
        echo ($done ? '[sudah]  ' : '[belum]  ') . $name . PHP_EOL;
        continue;
    }
    if ($done) {
        continue;
    }
    echo "Menerapkan $name ... ";
    try {
        $pdo->exec((string) file_get_contents($file));
        $pdo->prepare('INSERT INTO schema_migrations (filename) VALUES (?)')->execute([$name]);
        echo "OK" . PHP_EOL;
        $ran++;
    } catch (Throwable $e) {
        echo "GAGAL" . PHP_EOL . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}
if (!$statusOnly) {
    echo $ran === 0 ? 'Tidak ada migrasi baru.' . PHP_EOL : "$ran migrasi diterapkan." . PHP_EOL;
}
