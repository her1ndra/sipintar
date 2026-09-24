<?php
require_once __DIR__ . '/../../config/config.php';
requireLogin();
$user = currentUser();
if ($user['role'] !== 'Admin' && ($user['role'] !== 'Penilai' || $user['nama_jabatan'] !== 'Ketua')) {
    http_response_code(403);
    die('Akses kegiatan hakim hanya untuk Admin atau Ketua.');
}
$pdo = Database::getConnection();
$data = $pdo->query(
    "SELECT kh.*, p.nama_lengkap
     FROM kegiatan_hakim kh
     JOIN pegawai p ON kh.id_pegawai = p.id_pegawai
     JOIN jabatan j ON p.id_jabatan = j.id_jabatan
     WHERE j.nama_jabatan LIKE '%Hakim%'
     ORDER BY kh.tanggal_mulai DESC"
)->fetchAll();
$pageTitle = 'Tracing kegiatan hakim';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Catat kegiatan</a>
<table class="table table-bordered table-striped bg-white">
<thead><tr><th>Hakim</th><th>Jenis kegiatan</th><th>Nama kegiatan</th><th>Tanggal</th><th>Tempat</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach ($data as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
  <td><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
  <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
  <td><?= htmlspecialchars($row['tanggal_mulai']) ?><?= $row['tanggal_selesai'] ? ' s.d. ' . htmlspecialchars($row['tanggal_selesai']) : '' ?></td>
  <td><?= htmlspecialchars($row['lokasi'] ?: '-') ?></td>
  <td>
    <button
      type="button"
      class="btn btn-sm btn-outline-primary download-pdf"
      data-kegiatan="<?= htmlspecialchars(json_encode([
          'id' => (int) $row['id_kegiatan'],
          'nama' => $row['nama_lengkap'],
          'jenis' => $row['jenis_kegiatan'],
          'kegiatan' => $row['nama_kegiatan'],
          'penyelenggara' => $row['penyelenggara'] ?: '-',
          'tanggal' => $row['tanggal_mulai'] . ($row['tanggal_selesai'] ? ' s.d. ' . $row['tanggal_selesai'] : ''),
          'tempat' => $row['lokasi'] ?: '-',
          'lampiran' => $row['file_bukti'] ? BASE_URL . '/' . ltrim($row['file_bukti'], '/') : null,
      ], JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>"
    >Download PDF</button>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$data): ?>
<tr><td colspan="6" class="text-center text-muted">Belum ada data kegiatan hakim.</td></tr>
<?php endif; ?>
</tbody>
</table>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
<script>
document.querySelectorAll('.download-pdf').forEach(function (button) {
  button.addEventListener('click', async function () {
    var data = JSON.parse(button.dataset.kegiatan);
    var originalText = button.textContent;
    button.disabled = true;
    button.textContent = 'Menyiapkan PDF...';

    try {
      if (!window.jspdf || !window.jspdf.jsPDF || !window.PDFLib) {
        throw new Error('Library PDF belum tersedia.');
      }

      var pdf = new window.jspdf.jsPDF();
      pdf.setFontSize(16);
      pdf.text('DATA KEGIATAN HAKIM', 105, 20, { align: 'center' });
      pdf.setFontSize(11);
      var tableX = 20;
      var labelWidth = 45;
      var valueWidth = 125;
      var rowY = 30;
      var rows = [
        ['Nama', data.nama],
        ['Jenis kegiatan', data.jenis],
        ['Nama kegiatan', data.kegiatan],
        ['Penyelenggara', data.penyelenggara],
        ['Tanggal', data.tanggal],
        ['Tempat', data.tempat]
      ];
      rows.forEach(function (item) {
        var valueLines = pdf.splitTextToSize(String(item[1]), valueWidth - 8);
        var rowHeight = Math.max(10, valueLines.length * 6 + 4);
        pdf.rect(tableX, rowY, labelWidth, rowHeight);
        pdf.rect(tableX + labelWidth, rowY, valueWidth, rowHeight);
        pdf.setFont(undefined, 'bold');
        pdf.text(item[0], tableX + 4, rowY + 6);
        pdf.setFont(undefined, 'normal');
        pdf.text(valueLines, tableX + labelWidth + 4, rowY + 6);
        rowY += rowHeight;
      });
      var y = rowY;

      var attachmentExtension = data.lampiran
        ? data.lampiran.split('?')[0].split('.').pop().toLowerCase()
        : '';
      if (data.lampiran && ['jpg', 'jpeg'].includes(attachmentExtension)) {
        var imageResponse = await fetch(data.lampiran);
        var imageBlob = await imageResponse.blob();
        var imageData = await new Promise(function (resolve, reject) {
          var reader = new FileReader();
          reader.onload = function () { resolve(reader.result); };
          reader.onerror = reject;
          reader.readAsDataURL(imageBlob);
        });
        pdf.setFont(undefined, 'bold');
        pdf.text('Lampiran surat tugas', 20, y + 8);
        pdf.addImage(imageData, 'JPEG', 20, y + 14, 170, 0);
      } else if (data.lampiran && attachmentExtension === 'pdf') {
        // Halaman PDF surat tugas ditambahkan langsung setelah halaman data.
      } else {
        pdf.setFont(undefined, 'bold');
        pdf.text('Lampiran surat tugas', 20, y + 8);
        pdf.setFont(undefined, 'normal');
        pdf.text(data.lampiran ? 'File Word terlampir pada sistem.' : 'Tidak ada lampiran.', 20, y + 16);
      }

      var generatedBytes = new Uint8Array(await pdf.output('arraybuffer'));
      if (data.lampiran && attachmentExtension === 'pdf') {
        var generatedDocument = await window.PDFLib.PDFDocument.load(generatedBytes);
        var attachmentResponse = await fetch(data.lampiran);
        var attachmentBytes = await attachmentResponse.arrayBuffer();
        var attachmentDocument = await window.PDFLib.PDFDocument.load(attachmentBytes);
        var copiedPages = await generatedDocument.copyPages(
          attachmentDocument,
          attachmentDocument.getPageIndices()
        );
        copiedPages.forEach(function (page) { generatedDocument.addPage(page); });
        generatedBytes = await generatedDocument.save();
      }

      var blob = new Blob([generatedBytes], { type: 'application/pdf' });
      var downloadUrl = URL.createObjectURL(blob);
      var link = document.createElement('a');
      link.href = downloadUrl;
      link.download = 'kegiatan-hakim-' + data.id + '.pdf';
      link.click();
      URL.revokeObjectURL(downloadUrl);
    } catch (error) {
      alert('PDF gagal dibuat: ' + error.message);
    } finally {
      button.disabled = false;
      button.textContent = originalText;
    }
  });
});
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
