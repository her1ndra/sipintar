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

<style>
#dialog-konfirmasi-hapus .modal-dialog { max-width: 550px; }
#dialog-konfirmasi-hapus .modal-content {
    color: #f1f3f4;
    background: #202124;
    border: 0;
    border-radius: 8px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, .35);
}
#dialog-konfirmasi-hapus .modal-body {
    padding: 18px 20px 34px;
    font-size: 1rem;
}
#dialog-konfirmasi-hapus .modal-footer {
    gap: 8px;
    padding: 0 20px 20px;
    border: 0;
}
#dialog-konfirmasi-hapus .btn {
    min-width: 88px;
    border: 0;
    border-radius: 24px;
}
#dialog-konfirmasi-hapus .btn-confirm {
    color: #202124;
    background: #ffb0c0;
}
#dialog-konfirmasi-hapus .btn-cancel {
    color: #fff;
    background: #793547;
}
</style>
<div class="modal fade" id="dialog-konfirmasi-hapus" tabindex="-1" role="dialog" aria-labelledby="dialog-konfirmasi-hapus-pesan" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body" id="dialog-konfirmasi-hapus-pesan"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-confirm" id="dialog-konfirmasi-hapus-ok">OK</button>
                <button type="button" class="btn btn-cancel" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/vendor/jquery/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/sb-admin-2.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/table-search.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function kirimLogout(action) {
    var f = document.createElement('form');
    f.method = 'post';
    f.action = action;
    var t = document.createElement('input');
    t.type = 'hidden';
    t.name = '_csrf';
    t.value = <?= json_encode(csrfToken()) ?>;
    f.appendChild(t);
    document.body.appendChild(f);
    f.submit();
}
document.addEventListener('DOMContentLoaded', function () {
    var pendingDeleteForm = null;
    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm-message')) {
            return;
        }
        event.preventDefault();
        pendingDeleteForm = form;
        document.getElementById('dialog-konfirmasi-hapus-pesan').textContent =
            form.getAttribute('data-confirm-message');
        $('#dialog-konfirmasi-hapus').modal('show');
    });

    document.getElementById('dialog-konfirmasi-hapus-ok').addEventListener('click', function () {
        if (!pendingDeleteForm) {
            return;
        }
        var form = pendingDeleteForm;
        pendingDeleteForm = null;
        $('#dialog-konfirmasi-hapus').modal('hide');
        HTMLFormElement.prototype.submit.call(form);
    });

    $('#dialog-konfirmasi-hapus').on('hidden.bs.modal', function () {
        pendingDeleteForm = null;
    });

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
                    if (result.isConfirmed) { kirimLogout(href); }
                });
            } else if (confirm('Yakin ingin keluar dari sistem?')) {
                kirimLogout(href);
            }
        });
    });
});
</script>

</body>
</html>
