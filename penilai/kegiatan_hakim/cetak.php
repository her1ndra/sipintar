<?php
require_once __DIR__ . '/../../config/config.php';
requireLogin();
$user = currentUser();
if ($user['role'] !== 'Admin' && ($user['role'] !== 'Penilai' || $user['nama_jabatan'] !== 'Ketua')) {
    http_response_code(403);
    die('Akses kegiatan hakim hanya untuk Admin atau Ketua.');
}

$id = (int) ($_GET['id'] ?? 0);
$pdo = Database::getConnection();
$stmt = $pdo->prepare(
    "SELECT kh.*, p.nama_lengkap
     FROM kegiatan_hakim kh
     JOIN pegawai p ON kh.id_pegawai = p.id_pegawai
     JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE kh.id_kegiatan = ? AND j.nama_jabatan LIKE '%Hakim%'"
);
$stmt->execute([$id]);
$data = $stmt->fetch();
if (!$data) {
    http_response_code(404);
    die('Data kegiatan hakim tidak ditemukan.');
}

$attachmentUrl = $data['file_bukti'] ? BASE_URL . '/' . ltrim($data['file_bukti'], '/') : null;
$attachmentExtension = $data['file_bukti'] ? strtolower(pathinfo($data['file_bukti'], PATHINFO_EXTENSION)) : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Unduh PDF kegiatan hakim</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <style>
    body { font-family: Arial, sans-serif; color: #222; margin: 32px; }
    h1 { font-size: 22px; text-align: center; margin-bottom: 28px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #777; padding: 9px 10px; text-align: left; vertical-align: top; }
    th { width: 28%; background: #f2f2f2; }
    .attachment { margin-top: 28px; page-break-inside: avoid; }
    .attachment img { display: block; max-width: 100%; max-height: 650px; margin-top: 12px; }
    .attachment object { width: 100%; height: 700px; margin-top: 12px; }
    .download-button { margin-bottom: 20px; padding: 8px 14px; cursor: pointer; }
  </style>
</head>
<body>
  <button class="download-button" type="button" id="download-pdf">Download PDF</button>
  <h1>DATA KEGIATAN HAKIM</h1>
  <table>
    <tr><th>Nama</th><td><?= htmlspecialchars($data['nama_lengkap']) ?></td></tr>
    <tr><th>Jenis kegiatan</th><td><?= htmlspecialchars($data['jenis_kegiatan']) ?></td></tr>
    <tr><th>Nama kegiatan</th><td><?= htmlspecialchars($data['nama_kegiatan']) ?></td></tr>
    <tr><th>Penyelenggara</th><td><?= htmlspecialchars($data['penyelenggara'] ?: '-') ?></td></tr>
    <tr><th>Tanggal</th><td><?= htmlspecialchars($data['tanggal_mulai']) ?><?= $data['tanggal_selesai'] ? ' s.d. ' . htmlspecialchars($data['tanggal_selesai']) : '' ?></td></tr>
    <tr><th>Tempat</th><td><?= htmlspecialchars($data['lokasi'] ?: '-') ?></td></tr>
  </table>

  <div class="attachment">
    <strong>Lampiran surat tugas</strong>
    <?php if ($attachmentUrl && in_array($attachmentExtension, ['jpg', 'jpeg'], true)): ?>
      <img src="<?= htmlspecialchars($attachmentUrl) ?>" alt="Lampiran surat tugas">
    <?php elseif ($attachmentUrl && $attachmentExtension === 'pdf'): ?>
      <object data="<?= htmlspecialchars($attachmentUrl) ?>" type="application/pdf">
        <p>PDF tidak dapat ditampilkan. <a href="<?= htmlspecialchars($attachmentUrl) ?>" target="_blank" rel="noopener">Buka lampiran</a>.</p>
      </object>
    <?php elseif ($attachmentUrl): ?>
      <p>File Word: <a href="<?= htmlspecialchars($attachmentUrl) ?>" target="_blank" rel="noopener">Buka lampiran surat tugas</a></p>
    <?php else: ?>
      <p>Tidak ada lampiran.</p>
    <?php endif; ?>
  </div>
  <script>
    (function () {
      var button = document.getElementById('download-pdf');
      var attachmentUrl = <?= $attachmentUrl ? json_encode($attachmentUrl) : 'null' ?>;
      var attachmentExtension = <?= $attachmentExtension ? json_encode($attachmentExtension) : 'null' ?>;
      var details = [
        ['Nama', <?= json_encode($data['nama_lengkap']) ?>],
        ['Jenis kegiatan', <?= json_encode($data['jenis_kegiatan']) ?>],
        ['Nama kegiatan', <?= json_encode($data['nama_kegiatan']) ?>],
        ['Penyelenggara', <?= json_encode($data['penyelenggara'] ?: '-') ?>],
        ['Tanggal', <?= json_encode($data['tanggal_mulai'] . ($data['tanggal_selesai'] ? ' s.d. ' . $data['tanggal_selesai'] : '')) ?>],
        ['Tempat', <?= json_encode($data['lokasi'] ?: '-') ?>]
      ];

      function imageDataUrl(url) {
        return fetch(url).then(function (response) {
          if (!response.ok) throw new Error('Lampiran tidak dapat dibaca.');
          return response.blob();
        }).then(function (blob) {
          return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onload = function () { resolve(reader.result); };
            reader.onerror = reject;
            reader.readAsDataURL(blob);
          });
        });
      }

      button.addEventListener('click', function () {
        if (!window.jspdf || !window.jspdf.jsPDF) {
          alert('Generator PDF belum tersedia. Periksa koneksi internet lalu coba lagi.');
          return;
        }

        button.disabled = true;
        var pdf = new window.jspdf.jsPDF();
        pdf.setFontSize(16);
        pdf.text('DATA KEGIATAN HAKIM', 105, 20, { align: 'center' });
        pdf.setFontSize(11);
        var y = 38;
        details.forEach(function (detail) {
          pdf.setFont(undefined, 'bold');
          pdf.text(detail[0], 20, y);
          pdf.setFont(undefined, 'normal');
          var lines = pdf.splitTextToSize(String(detail[1]), 125);
          pdf.text(lines, 65, y);
          y += Math.max(8, lines.length * 6);
        });

        function finish() {
          if (attachmentUrl && !['jpg', 'jpeg'].includes(attachmentExtension)) {
            pdf.setFont(undefined, 'bold');
            pdf.text('Lampiran surat tugas', 20, y + 8);
            pdf.setFont(undefined, 'normal');
            pdf.text('Buka lampiran: ' + attachmentUrl, 20, y + 16);
          } else if (!attachmentUrl) {
            pdf.setFont(undefined, 'bold');
            pdf.text('Lampiran surat tugas', 20, y + 8);
            pdf.setFont(undefined, 'normal');
            pdf.text('Tidak ada lampiran.', 20, y + 16);
          }
          pdf.save('kegiatan-hakim-' + <?= json_encode($data['id_kegiatan']) ?> + '.pdf');
          button.disabled = false;
        }

        if (attachmentUrl && ['jpg', 'jpeg'].includes(attachmentExtension)) {
          imageDataUrl(attachmentUrl).then(function (image) {
            pdf.setFont(undefined, 'bold');
            pdf.text('Lampiran surat tugas', 20, y + 8);
            pdf.addImage(image, 'JPEG', 20, y + 14, 170, 0);
            finish();
          }).catch(function () {
            finish();
          });
        } else {
          finish();
        }
      });
    }());
  </script>
</body>
</html>
