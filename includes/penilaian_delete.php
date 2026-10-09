<?php
function hapusSesiPenilaian(PDO $pdo, int $idWawancara): array
{
    $filePaths = [];
    foreach (['hasil_kuesioner', 'hasil_wawancara', 'bukti_wawancara'] as $table) {
        $statement = $pdo->prepare("SELECT file_bukti FROM {$table} WHERE id_wawancara = ?");
        $statement->execute([$idWawancara]);
        $filePaths = array_merge($filePaths, $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    $pdo->prepare('DELETE FROM hasil_kuesioner WHERE id_wawancara = ?')->execute([$idWawancara]);
    $pdo->prepare('DELETE FROM wawancara WHERE id_wawancara = ?')->execute([$idWawancara]);

    return $filePaths;
}

function hapusFileBuktiPenilaian(array $filePaths): void
{
    $projectRoot = realpath(__DIR__ . '/..');
    if ($projectRoot === false) {
        return;
    }

    foreach (array_unique($filePaths) as $filePath) {
        $relativePath = ltrim(str_replace('\\', '/', (string) $filePath), '/');
        if (!preg_match('#^assets/uploads/(?:kuesioner|wawancara)/[A-Za-z0-9._-]+$#D', $relativePath)) {
            continue;
        }
        $absolutePath = $projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if (is_file($absolutePath)) {
            unlink($absolutePath);
        }
    }
}