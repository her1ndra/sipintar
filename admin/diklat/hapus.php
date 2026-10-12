<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
hapusDenganAman(
    'DELETE FROM kebutuhan_diklat WHERE id_kebutuhan = ?',
    (int) ($_POST['id'] ?? 0),
    'Kebutuhan diklat berhasil dihapus.',
    'hapus_kebutuhan_diklat',
    'Hapus kebutuhan diklat'
);
