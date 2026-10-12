<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
hapusDenganAman(
    'DELETE FROM analisis_kesenjangan_kompetensi WHERE id_kesenjangan = ?',
    (int) ($_POST['id'] ?? 0),
    'Analisis kesenjangan berhasil dihapus.',
    'hapus_gap',
    'Hapus analisis kesenjangan'
);
