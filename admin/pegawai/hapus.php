<?php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
hapusDenganAman(
    'DELETE FROM pegawai WHERE id_pegawai = ?',
    (int) ($_POST['id'] ?? 0),
    'Data pegawai berhasil dihapus.',
    'hapus_pegawai',
    'Hapus pegawai'
);
