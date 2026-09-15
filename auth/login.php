<?php
require_once __DIR__ . '/../config/config.php';

if (!empty($_SESSION['id_user'])) {
    header('Location: ' . BASE_URL . ($_SESSION['role'] === 'Admin' ? '/admin/dashboard.php' : '/penilai/dashboard.php'));
    exit;
}
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Masuk - <?= APP_NAME ?></title>

    <link href="<?= BASE_URL ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }

        :root {
            --pn-green: #006837;
            --pn-green-dark: #004d29;
            --pn-gold: #F9A825;
            --bg-color: #f0f4f3;
            --text-dark: #2c3e50;
            --text-muted: #7f8c8d;
        }

        body {
            background-color: var(--bg-color);
            background-image: linear-gradient(rgba(0, 50, 20, 0.7), rgba(0, 50, 20, 0.7)), url('<?= BASE_URL ?>/assets/img/pn-yk.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px 0;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            width: 90%;
            max-width: 460px;
            border-radius: 20px;
            backdrop-filter: blur(5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 30px 45px;
        }

        .top-bar {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 8px;
            background: linear-gradient(90deg, var(--pn-green) 0%, var(--pn-gold) 100%);
        }

        .header-area { text-align: center; margin-bottom: 2vh; }

        .logo-img {
            height: 100px;
            max-height: 130px;
            width: auto;
            margin-bottom: 10px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        .app-name {
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--pn-green);
            line-height: 1;
            margin: 0;
            letter-spacing: -1px;
        }

        .app-desc {
            font-size: 1rem;
            color: var(--text-dark);
            margin-top: 8px;
            font-weight: 500;
            line-height: 1.4;
        }

        .form-content { display: flex; flex-direction: column; gap: 15px; }

        .form-label {
            display: block;
            margin-bottom: 5px;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 1rem;
        }

        .input-wrapper { position: relative; }

        .input-error {
            display: none;
            margin-top: 6px;
            color: #b91c1c;
            font-size: 0.85rem;
        }

        .form-input {
            width: 100%;
            height: 55px;
            padding: 10px 50px;
            border: 2px solid #eaecf0;
            border-radius: 14px;
            font-size: 1.1rem;
            color: #333;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--pn-green);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(0, 104, 55, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #98a2b3;
            font-size: 1.3rem;
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #98a2b3;
            font-size: 1.2rem;
        }
        .toggle-password:hover { color: var(--pn-green); }

        .btn-submit {
            width: 100%;
            height: 60px;
            background: var(--pn-green);
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 10px;
            box-shadow: 0 6px 15px rgba(0, 104, 55, 0.25);
        }

        .btn-submit:hover {
            background: var(--pn-green-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 104, 55, 0.35);
        }

        .alert-box {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.95rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer {
            margin-top: 20px;
            font-size: 0.9rem;
            color: var(--text-muted);
            text-align: center;
        }

        @media screen and (max-height: 720px) {
            .login-container { padding: 20px 30px; }
            .logo-img { height: 70px; margin-bottom: 5px; }
            .app-name { font-size: 2rem; }
            .app-desc { font-size: 0.9rem; margin-top: 5px; }
            .form-input { height: 48px; font-size: 1rem; }
            .btn-submit { height: 50px; font-size: 1.1rem; }
            .form-content { gap: 10px; }
            .footer { margin-top: 10px; font-size: 0.8rem; }
        }
    </style>
</head>
<body>

    <div class="login-container">

        <div class="top-bar"></div>

        <div class="header-area">
            <img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo PN Yogyakarta" class="logo-img">
            <h1 class="app-name">SI PINTAR</h1>
            <p class="app-desc">Pengembangan Kompetensi Aparatur<br>Pengadilan Negeri Yogyakarta</p>
        </div>

        <?php if ($error): ?>
            <div class="alert-box">
                <i class="fas fa-exclamation-triangle"></i> <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>/auth/proses_login.php" class="form-content">

            <div>
                <label class="form-label" for="nip">NIP</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="nip" name="nip" class="form-input" placeholder="Masukkan NIP" inputmode="numeric" pattern="[0-9]+" oninput="validateNip(this)" required autofocus autocomplete="off" aria-describedby="nip-error">
                </div>
                <small id="nip-error" class="input-error">NIP harus berupa angka.</small>
            </div>

            <div>
                <label class="form-label" for="password">Kata Sandi</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" class="form-input" placeholder="Masukkan kata sandi" required>
                    <i class="fas fa-eye-slash toggle-password" onclick="togglePass()" id="eye-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                MASUK <i class="fas fa-arrow-right"></i>
            </button>

        </form>

        <div class="footer">
            &copy; <?= date('Y') ?> Pengadilan Negeri Yogyakarta
        </div>

    </div>

    <script>
        function validateNip(input) {
            var error = document.getElementById("nip-error");
            var hasInvalidCharacter = /[^0-9]/.test(input.value);

            error.style.display = hasInvalidCharacter ? "block" : "none";
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        function togglePass() {
            var passInput = document.getElementById("password");
            var icon = document.getElementById("eye-icon");
            if (passInput.type === "password") {
                passInput.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
                icon.style.color = "#006837";
            } else {
                passInput.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
                icon.style.color = "#98a2b3";
            }
        }
    </script>

</body>
</html>
