<?php

function unggahFotoDokumentasiKuesioner(?array $files): array
{
    if (!$files) {
        return ['paths' => [], 'error' => null];
    }
    foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $key) {
        if (!isset($files[$key]) || !is_array($files[$key])) {
            return ['paths' => [], 'error' => 'Data foto dokumentasi tidak valid.'];
        }
    }
    if (count($files['name']) > 10) {
        return ['paths' => [], 'error' => 'Maksimal 10 foto dokumentasi dapat diunggah sekaligus.'];
    }
    foreach (['type', 'tmp_name', 'error', 'size'] as $key) {
        if (count($files[$key]) !== count($files['name'])) {
            return ['paths' => [], 'error' => 'Data foto dokumentasi tidak valid.'];
        }
    }

    $paths = [];
    foreach ($files['name'] as $index => $name) {
        $single = [
            'name' => $name,
            'type' => $files['type'][$index] ?? '',
            'tmp_name' => $files['tmp_name'][$index] ?? '',
            'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
            'size' => $files['size'][$index] ?? 0,
        ];
        $upload = simpanUploadAman(
            $single,
            'kuesioner',
            ['image/jpeg' => 'jpg', 'image/png' => 'png']
        );
        if ($upload['status'] === 'error') {
            hapusFileBuktiPenilaian($paths);
            return ['paths' => [], 'error' => 'Foto dokumentasi harus JPG/PNG dan maksimal 5 MB per foto.'];
        }
        if ($upload['status'] === 'ok') {
            $paths[] = $upload['path'];
        }
    }

    return ['paths' => $paths, 'error' => null];
}
