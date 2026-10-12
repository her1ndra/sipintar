<?php
class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host    = (string) appConfig('db_host', 'localhost');
            $port    = (string) appConfig('db_port', '3306');
            $db      = (string) appConfig('db_name', 'si_pintar');
            $user    = (string) appConfig('db_user', '');
            $pass    = (string) appConfig('db_pass', '');
            $charset = 'utf8mb4';

            if ($user === '') {
                error_log('SI PINTAR: db_user belum dikonfigurasi (lihat config/local.example.php).');
                http_response_code(500);
                die('Konfigurasi database belum diatur. Hubungi administrator.');
            }

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                error_log('SI PINTAR: koneksi database gagal: ' . $e->getMessage());
                http_response_code(500);
                die(APP_DEBUG
                    ? 'Koneksi database gagal: ' . htmlspecialchars($e->getMessage())
                    : 'Layanan sedang tidak tersedia. Hubungi administrator.');
            }
        }
        return self::$instance;
    }
}
