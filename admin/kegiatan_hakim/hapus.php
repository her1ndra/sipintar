<?php
// Alamat Admin untuk modul Kegiatan Hakim. Isi halaman dipakai bersama dengan penilai/kegiatan_hakim/hapus.php
require_once __DIR__ . '/../../config/config.php';
requireRole('Admin');
require __DIR__ . '/../../penilai/kegiatan_hakim/hapus.php';
