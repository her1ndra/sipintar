<?php
// $pageTitle harus sudah diset sebelum include file ini
$user = currentUser();
$currentScript = $_SERVER['SCRIPT_NAME'] ?? ($_SERVER['PHP_SELF'] ?? '');

/**
 * Menentukan apakah sebuah item menu sidebar sedang aktif,
 * berdasarkan potongan path folder/file yang sedang diakses.
 */
function siPintarMenuActive(string $needle, string $path): string
{
    return (strpos($path, $needle) !== false) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?= htmlspecialchars($pageTitle ?? APP_NAME) ?> - <?= APP_NAME ?></title>

<link href="<?= BASE_URL ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
<link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>

<body id="page-top">
<div id="wrapper">

<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar"
    style="background: linear-gradient(180deg, #004d00 10%, #004d00 100%); position: sticky; top: 0; height: 100vh; overflow-y: hidden; overflow-x: hidden; z-index: 1050;">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= BASE_URL ?>/index.php">
        <div class="sidebar-brand-icon">
            <img src="<?= BASE_URL ?>/assets/img/logo.png" style="width: 40px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
        </div>
        <div class="sidebar-brand-text mx-2" style="font-weight: 800; letter-spacing: 1px;">
            SI PINTAR <sup style="color: #F9A825;">PNYK</sup>
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <?php if ($user['role'] === 'Admin'): ?>

        <li class="nav-item <?= siPintarMenuActive('/admin/dashboard.php', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/admin/dashboard.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-heading">Kepegawaian</div>

        <li class="nav-item <?= siPintarMenuActive('/admin/pegawai/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/admin/pegawai/index.php">
                <i class="fas fa-fw fa-users"></i>
                <span>Data Pegawai</span>
            </a>
        </li>

        <li class="nav-item <?= siPintarMenuActive('/admin/analisis_tugas/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/admin/analisis_tugas/index.php">
                <i class="fas fa-fw fa-tasks"></i>
                <span>Analisis Tugas</span>
            </a>
        </li>

        <li class="nav-item <?= siPintarMenuActive('/admin/gap/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/admin/gap/index.php">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Analisis Kesenjangan</span>
            </a>
        </li>

        <li class="nav-item <?= siPintarMenuActive('/admin/sertifikat/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/admin/sertifikat/index.php">
                <i class="fas fa-fw fa-certificate"></i>
                <span>Sertifikat Pegawai</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Penilaian Kompetensi</div>

        <li class="nav-item <?= siPintarMenuActive('/admin/kuesioner/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/admin/kuesioner/index.php">
                <i class="fas fa-fw fa-clipboard-list"></i>
                <span>Kuesioner</span>
            </a>
        </li>

        <li class="nav-item <?= siPintarMenuActive('/admin/wawancara/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/admin/wawancara/index.php">
                <i class="fas fa-fw fa-comments"></i>
                <span>Wawancara</span>
            </a>
        </li>

    <?php else: ?>

        <li class="nav-item <?= siPintarMenuActive('/penilai/dashboard.php', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/penilai/dashboard.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <div class="sidebar-heading">Penilaian Kompetensi</div>

        <li class="nav-item <?= siPintarMenuActive('/penilai/kuesioner/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/penilai/kuesioner/index.php">
                <i class="fas fa-fw fa-clipboard-list"></i>
                <span>Kuesioner</span>
            </a>
        </li>

        <li class="nav-item <?= siPintarMenuActive('/penilai/wawancara/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/penilai/wawancara/index.php">
                <i class="fas fa-fw fa-comments"></i>
                <span>Wawancara</span>
            </a>
        </li>

        <li class="nav-item <?= siPintarMenuActive('/penilai/gap/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/penilai/gap/index.php">
                <i class="fas fa-fw fa-chart-bar"></i>
                <span>Analisis Kesenjangan</span>
            </a>
        </li>

        <li class="nav-item <?= siPintarMenuActive('/penilai/diklat/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/penilai/diklat/index.php">
                <i class="fas fa-fw fa-graduation-cap"></i>
                <span>Kebutuhan Diklat</span>
            </a>
        </li>

        <?php if (in_array($user['nama_jabatan'], ['Ketua', 'Wakil Ketua'], true)): ?>
        <hr class="sidebar-divider">
        <div class="sidebar-heading">Pimpinan</div>

        <li class="nav-item <?= siPintarMenuActive('/penilai/kegiatan_hakim/', $currentScript) ?>">
            <a class="nav-link" href="<?= BASE_URL ?>/penilai/kegiatan_hakim/index.php">
                <i class="fas fa-fw fa-gavel"></i>
                <span>Kegiatan Hakim</span>
            </a>
        </li>
        <?php endif; ?>

    <?php endif; ?>

    <hr class="sidebar-divider">

    <?php if ($user['role'] === 'Admin'): ?>
    <div class="sidebar-heading">Administrasi</div>
    <li class="nav-item <?= siPintarMenuActive('/admin/users/', $currentScript) ?>">
        <a class="nav-link" href="<?= BASE_URL ?>/admin/users/index.php">
            <i class="fas fa-fw fa-user-cog"></i>
            <span>Akun User</span>
        </a>
    </li>
    <hr class="sidebar-divider">
    <?php endif; ?>

    <li class="nav-item">
        <a class="nav-link" href="<?= BASE_URL ?>/auth/logout.php" id="tombol-logout-sidebar" style="cursor: pointer;">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Keluar</span>
        </a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>

<div id="content-wrapper" class="d-flex flex-column">
<div id="content" style="padding-top: 90px;">

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 fixed-top shadow">

    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">

        <div class="topbar-divider d-none d-sm-block"></div>

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small text-right">
                    <?= htmlspecialchars($user['nama'] ?? $user['nip']) ?><br>
                    <span style="font-size: .7rem;"><?= htmlspecialchars($user['role']) ?><?= $user['nama_jabatan'] ? ' - ' . htmlspecialchars($user['nama_jabatan']) : '' ?></span>
                </span>
                <img class="img-profile rounded-circle" src="<?= BASE_URL ?>/assets/img/undraw_profile.svg">
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown" style="background-color: white;">

                <a class="dropdown-item" href="<?= BASE_URL ?>/auth/logout.php" id="tombol-logout-topbar" style="background-color: white; font-size: 0.8rem; color: #006837;">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2" style="color: #006837; opacity: 0.7;"></i>
                    Keluar
                </a>
            </div>
        </li>
    </ul>
</nav>

<div class="container-fluid">

<h1 class="h3 mb-4 text-gray-800"><?= htmlspecialchars($pageTitle ?? '') ?></h1>
<?php $flash = getFlash(); if ($flash): ?>
<div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
<?php endif; ?>
