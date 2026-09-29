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
$namaBulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
];
$dataPerBulan = [];
foreach ($data as $row) {
    $bulan = substr($row['tanggal_mulai'], 0, 7);
  $dataPerBulan[$bulan][] = $row;
}
$pageTitle = 'Tracing kegiatan hakim';
require_once __DIR__ . '/../../includes/header.php';
?>
<a href="tambah.php" class="btn btn-primary mb-3">+ Catat kegiatan</a>
<?php if (!$data): ?>
<div class="alert alert-info">Belum ada data kegiatan hakim.</div>
<?php endif; ?>
<?php foreach ($dataPerBulan as $bulan => $kegiatanBulan): ?>
<section class="mb-4">
  <h4 class="mb-3"><?= htmlspecialchars($namaBulan[substr($bulan, 5, 2)] . ' ' . substr($bulan, 0, 4)) ?></h4>
  <div class="table-responsive">
    <table class="table table-bordered bg-white pegawai-table">
      <thead><tr><th>Hakim</th><th>Jenis kegiatan</th><th>Nama kegiatan</th><th>Penyelenggara</th><th>Tanggal</th><th>Tempat</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($kegiatanBulan as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
        <td><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
        <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
        <td><?= htmlspecialchars($row['penyelenggara'] ?: '-') ?></td>
        <td><?= htmlspecialchars($row['tanggal_mulai']) ?><?= $row['tanggal_selesai'] ? ' s.d. ' . htmlspecialchars($row['tanggal_selesai']) : '' ?></td>
        <td><?= htmlspecialchars($row['lokasi'] ?: '-') ?></td>
        <td class="text-nowrap">
          <a href="edit.php?id=<?= (int) $row['id_kegiatan'] ?>" class="btn btn-sm btn-warning table-action-btn" title="Edit" aria-label="Edit"><i class="fas fa-edit" aria-hidden="true"></i></a>
          <form method="post" action="hapus.php" class="d-inline" onsubmit="return confirm('Hapus kegiatan hakim ini?')">
            <input type="hidden" name="id" value="<?= (int) $row['id_kegiatan'] ?>">
            <button type="submit" class="btn btn-sm btn-danger table-action-btn" title="Hapus" aria-label="Hapus"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>
          </form>
          <div class="dropdown d-inline-block">
            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle table-action-btn" data-toggle="dropdown" title="Unduh" aria-label="Unduh" aria-haspopup="true">
              <i class="fas fa-download" aria-hidden="true"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <button
                type="button"
                class="dropdown-item download-pdf"
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
              ><i class="fas fa-file-pdf text-danger mr-2" aria-hidden="true"></i>PDF</button>
              <a class="dropdown-item" href="cetak.php?id=<?= (int) $row['id_kegiatan'] ?>&amp;format=word"><i class="fas fa-file-word text-primary mr-2" aria-hidden="true"></i>Word</a>
            </div>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<?php endforeach; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
<script>
document.querySelectorAll('.download-pdf').forEach(function (button) {
  button.addEventListener('click', async function () {
    var data = JSON.parse(button.dataset.kegiatan);
    var originalContent = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>Menyiapkan PDF...';

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
      button.innerHTML = originalContent;
    }
  });
});
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
