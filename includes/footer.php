</div>
</div>

<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">

            <div class="font-weight-bold text-dark mb-1">
                <?= APP_NAME ?> <span class="mx-1">&bull;</span> Pengadilan Negeri Yogyakarta
            </div>

            <div class="small text-secondary mb-2">
                Modul Pengembangan Kompetensi Aparatur
            </div>

            <div class="small text-muted">
                &copy; <?= date('Y') ?>
            </div>

        </div>
    </div>
</footer>
</div>
</div>

<script src="<?= BASE_URL ?>/assets/vendor/jquery/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/sb-admin-2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    ['tombol-logout-sidebar', 'tombol-logout-topbar'].forEach(function (id) {
        var btn = document.getElementById(id);
        if (!btn) return;
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var href = this.getAttribute('href');
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Yakin ingin keluar?',
                    text: 'Sesi Anda akan diakhiri.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#006B3F',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Keluar',
                    cancelButtonText: 'Batal'
                }).then(function (result) {
                    if (result.isConfirmed) { window.location.href = href; }
                });
            } else if (confirm('Yakin ingin keluar dari sistem?')) {
                window.location.href = href;
            }
        });
    });
});
</script>

</body>
</html>
