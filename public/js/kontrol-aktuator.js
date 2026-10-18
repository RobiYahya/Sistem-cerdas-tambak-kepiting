/**
 * kontrol-aktuator.js
 *
 * Alur logika:
 *  - Mode Otomatis ON  → semua aktuator ON + dikunci (tidak bisa disentuh)
 *  - User coba matikan → tampil modal konfirmasi
 *  - Konfirmasi "Ya"   → mode jadi Manual, aktuator bisa diatur bebas
 *  - Konfirmasi "Batal"→ mode tetap Otomatis
 */

document.addEventListener('DOMContentLoaded', function () {

    const modeCheckbox   = document.getElementById('modeCheckbox');
    const modalBackdrop  = document.getElementById('modalBackdrop');
    const btnBatal       = document.getElementById('btnBatalModal');
    const btnKonfirmasi  = document.getElementById('btnKonfirmasiManual');
    const deviceCbs      = document.querySelectorAll('.ka-device-checkbox');

    /* ================================================
       UTILITAS
       ================================================ */

    /** Nyalakan / matikan semua aktuator sekaligus */
    function setAllDevices(on) {
        deviceCbs.forEach(function (cb) {
            cb.checked = on;
            syncCardState(cb);
        });
    }

    /** Update tampilan card (warna hijau + teks status) */
    function syncCardState(cb) {
        var id     = cb.dataset.id;
        var card   = document.getElementById('card-' + id);
        var label  = document.getElementById('status-' + id);

        if (cb.checked) {
            card.classList.add('ka-alat-on');
            if (label) label.textContent = 'Aktif';
        } else {
            card.classList.remove('ka-alat-on');
            if (label) label.textContent = 'Standby';
        }
    }

    /** Kunci / buka kunci semua tombol aktuator */
    function lockDevices(locked) {
        deviceCbs.forEach(function (cb) {
            cb.disabled = locked;
            var track = cb.nextElementSibling;
            if (track) track.style.cursor = locked ? 'not-allowed' : 'pointer';
            cb.closest('.ka-alat-card').classList.toggle('ka-alat-locked', locked);
        });
    }

    /* ================================================
       TERAPKAN STATE AWAL SAAT HALAMAN DIBUKA
       ================================================ */
    function applyModeState(isAuto) {
        if (isAuto) {
            setAllDevices(true);   // semua nyala
            lockDevices(true);     // semua dikunci
        } else {
            lockDevices(false);    // bisa disentuh
            // state tiap aktuator dibiarkan seperti adanya
        }
    }

    applyModeState(modeCheckbox.checked);

    /* ================================================
       INTERCEPT KLIK TOGGLE MODE OTOMATIS
       ================================================ */
    modeCheckbox.addEventListener('click', function (e) {
        // Kalau sedang ON dan user mau matiin → tampilkan modal dulu
        if (!this.checked) {
            // Browser sudah unchecked, kita kembalikan dulu sambil tunggu konfirmasi
            this.checked = true;
            showModal();
            e.preventDefault(); // cegah perubahan
        } else {
            // User nyalakan mode otomatis → langsung terapkan
            applyModeState(true);
        }
    });

    /* ================================================
       MODAL
       ================================================ */
    function showModal() {
        modalBackdrop.classList.add('ka-modal-open');
        modalBackdrop.setAttribute('aria-hidden', 'false');
        btnKonfirmasi.focus();
    }

    function closeModal() {
        modalBackdrop.classList.remove('ka-modal-open');
        modalBackdrop.setAttribute('aria-hidden', 'true');
    }

    // Batal → modal tutup, mode tetap otomatis
    btnBatal.addEventListener('click', closeModal);

    // Klik backdrop (luar modal) → batal
    modalBackdrop.addEventListener('click', function (e) {
        if (e.target === modalBackdrop) closeModal();
    });

    // ESC → batal
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalBackdrop.classList.contains('ka-modal-open')) {
            closeModal();
        }
    });

    // Konfirmasi → ganti ke mode manual
    btnKonfirmasi.addEventListener('click', function () {
        modeCheckbox.checked = false; // matikan mode otomatis
        applyModeState(false);        // buka kunci aktuator
        closeModal();
    });

    /* ================================================
       TOGGLE INDIVIDUAL AKTUATOR (saat mode manual)
       ================================================ */
    deviceCbs.forEach(function (cb) {
        cb.addEventListener('change', function () {
            syncCardState(this);
        });
    });

});