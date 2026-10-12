<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
hapusDenganAman(
    'DELETE FROM analisis_tugas WHERE id_analisis_tugas = ?',
    (int) ($_POST['id'] ?? 0),
    'Analisis tugas berhasil dihapus.',
    'hapus_analisis_tugas',
    'Hapus analisis tugas'
);
