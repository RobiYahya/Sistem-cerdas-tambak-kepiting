(function () {
    'use strict';

    // ============ 1. PAGINASI TABEL & KARTU (12 entri per halaman) ============
    const rows = document.querySelectorAll('.sensor-row');
    const cards = document.querySelectorAll('.sensor-card-item');
    const pgButtons = document.querySelectorAll('.riwayat-pager .pg-btn[data-page]');
    const btnPrev = document.getElementById('pgPrev');
    const btnNext = document.getElementById('pgNext');
    const footInfo = document.getElementById('footInfo');

    const itemsPerPage = 12;
    const totalRows = rows.length;
    const totalPages = Math.ceil(totalRows / itemsPerPage) || 1;
    let currentPage = 0;

    function renderPage(page) {
        currentPage = Math.max(0, Math.min(totalPages - 1, page));

        // Paginasi baris tabel
        rows.forEach(function (row) {
            const rowPage = parseInt(row.getAttribute('data-page'), 10);
            row.style.display = (rowPage === currentPage) ? '' : 'none';
        });

        // Paginasi kartu mobile
        cards.forEach(function (card) {
            const cardPage = parseInt(card.getAttribute('data-page'), 10);
            card.style.display = (cardPage === currentPage) ? '' : 'none';
        });

        // Update status aktif tombol paginasi
        pgButtons.forEach(function (btn) {
            const btnPage = parseInt(btn.getAttribute('data-page'), 10);
            btn.classList.toggle('active', btnPage === currentPage);
        });

        if (btnPrev) btnPrev.disabled = (currentPage === 0);
        if (btnNext) btnNext.disabled = (currentPage === totalPages - 1);

        if (footInfo) {
            const start = (currentPage * itemsPerPage) + 1;
            const end = Math.min((currentPage + 1) * itemsPerPage, totalRows);
            footInfo.textContent = `Menampilkan ${start}–${end} dari ${totalRows} data • Halaman ${currentPage + 1} dari ${totalPages}`;
        }
    }

    pgButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            renderPage(parseInt(this.getAttribute('data-page'), 10));
        });
    });

    if (btnPrev) {
        btnPrev.addEventListener('click', function () {
            renderPage(currentPage - 1);
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', function () {
            renderPage(currentPage + 1);
        });
    }

    // Inisialisasi halaman pertama
    renderPage(0);

    // ============ 2. FITUR UNDUH CSV ============
    const btnUnduhCsv = document.getElementById('btnUnduhCsv');
    if (btnUnduhCsv) {
        btnUnduhCsv.addEventListener('click', function (e) {
            const csvUrl = btnUnduhCsv.getAttribute('data-url');
            if (csvUrl) {
                window.location.href = csvUrl;
                return;
            }

            // Fallback unduh instan via JavaScript Blob
            let csv = "\uFEFFTanggal;Waktu;pH;Salinitas (ppt);DO (mg/L);Suhu (°C);NTU;Level (%);Skor;Status;Aksi Aktuator\r\n";
            rows.forEach(function (tr) {
                const cells = tr.querySelectorAll('td');
                const rowData = Array.from(cells).map(function (td) {
                    return td.innerText.trim().replace(/\s+/g, ' ');
                });
                csv += rowData.join(';') + "\r\n";
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', 'riwayat_sensor_09-11-2026.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }

    // ============ 3. INTERAKSI BUKA KALENDER ============
    document.querySelectorAll('.date-box').forEach(function (box) {
        const input = box.querySelector('input[type="date"]');
        if (!input) return;

        box.addEventListener('click', function (e) {
            if (e.target === input) return;
            try {
                if (typeof input.showPicker === 'function') {
                    input.showPicker();
                } else {
                    input.focus();
                }
            } catch (err) {
                input.focus();
            }
        });
    });

    // ============ 4. TOGGLE TAMPILAN (TABEL VS KARTU HP) ============
    const cardTable = document.querySelector('.card-table');
    const viewButtons = document.querySelectorAll('.view-toggle .vt-btn');

    viewButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const mode = this.getAttribute('data-view');
            viewButtons.forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');

            if (cardTable) {
                cardTable.classList.remove('mode-table', 'mode-cards');
                cardTable.classList.add('mode-' + mode);
            }
        });
    });
})();
