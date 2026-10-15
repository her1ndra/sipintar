<?php

function unggahFotoDokumentasi(?array $files, string $subfolder): array
{
    if (!$files) {
        return ['paths' => [], 'error' => null];
    }
    if (!in_array($subfolder, ['kuesioner', 'wawancara'], true)) {
        return ['paths' => [], 'error' => 'Folder dokumentasi tidak valid.'];
    }
    foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $key) {
        if (!isset($files[$key]) || !is_array($files[$key])) {
            return ['paths' => [], 'error' => 'Data foto dokumentasi tidak valid.'];
        }
    }
    if (count($files['name']) > 10) {
        return ['paths' => [], 'error' => 'Maksimal 10 foto dokumentasi dapat diunggah sekaligus.'];
    }
    $indexes = array_keys($files['name']);
    foreach (['type', 'tmp_name', 'error', 'size'] as $key) {
        if (array_keys($files[$key]) !== $indexes) {
            return ['paths' => [], 'error' => 'Data foto dokumentasi tidak valid.'];
        }
    }
    foreach ($indexes as $index) {
        if (!is_string($files['name'][$index]) || !is_string($files['type'][$index])
            || !is_string($files['tmp_name'][$index])
            || !is_int($files['error'][$index]) || !is_int($files['size'][$index])) {
            return ['paths' => [], 'error' => 'Data foto dokumentasi tidak valid.'];
        }
    }

    $paths = [];
    foreach ($indexes as $index) {
        $name = $files['name'][$index];
        $single = [
            'name' => $name,
            'type' => $files['type'][$index],
            'tmp_name' => $files['tmp_name'][$index],
            'error' => $files['error'][$index],
            'size' => $files['size'][$index],
        ];
        $upload = simpanUploadAman(
            $single,
            $subfolder,
            ['image/jpeg' => 'jpg', 'image/png' => 'png']
        );
        if ($upload['status'] === 'error') {
            hapusFileBuktiPenilaian($paths);
            return ['paths' => [], 'error' => 'Foto dokumentasi harus JPG/PNG, maksimal 5 MB per foto, dan paling banyak 10 foto.'];
        }
        if ($upload['status'] === 'ok') {
            $paths[] = $upload['path'];
        }
    }

    return ['paths' => $paths, 'error' => null];
}
