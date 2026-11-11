(function () {
    'use strict';

    // Helper untuk mengambil parameter filter yang aktif
    function getFilterParams() {
        const startInput = document.getElementById('filterStartDate');
        const endInput   = document.getElementById('filterEndDate');
        const start = startInput ? startInput.value : '2026-11-11';
        const end   = endInput   ? endInput.value   : start;

        const urlParams = new URLSearchParams(window.location.search);
        const device = urlParams.get('device') ||
                       document.getElementById('btnUnduhCsv')?.getAttribute('data-device') ||
                       'AQ-BOX-A1';

        return { start, end, device };
    }

    // =========================================================================
    // 1. INISIALISASI GRAFIK DINAMIKA PARAMETER AIR (CHART.JS)
    // =========================================================================
    const canvas = document.getElementById('chartRiwayatDinamika');
    const rccTitle = document.getElementById('rccTitle');
    let chartInstance = null;

    // Dataset simulasi per rentang waktu
    const rangeDataSets = {
        '1h': {
            title: 'Dinamika Parameter Air (1 Jam Terakhir)',
            labels: ['Mulai Periode', '10m', '20m', '30m', '40m', 'Tengah Periode', '50m', 'Sekarang'],
            suhu: [31.8, 32.0, 32.1, 32.4, 32.2, 32.5, 32.3, 32.4],
            salinitas: [26.3, 26.4, 26.4, 26.5, 26.4, 26.3, 26.4, 26.4],
            do: [5.60, 5.65, 5.68, 5.71, 5.69, 5.70, 5.72, 5.71],
            ph: [8.45, 8.48, 8.50, 8.51, 8.49, 8.50, 8.52, 8.51]
        },
        '24h': {
            title: 'Dinamika Parameter Air (24 Jam Terakhir)',
            labels: ['Mulai Periode', '02:00', '04:00', '06:00', '08:00', '10:00', '12:00', 'Tengah Periode', '16:00', '18:00', '20:00', '22:00', 'Sekarang'],
            suhu: [29.2, 29.8, 30.4, 30.8, 31.2, 29.7, 29.1, 30.3, 30.8, 30.5, 29.4, 31.8, 32.4],
            salinitas: [26.1, 25.8, 25.9, 26.4, 26.8, 26.3, 26.2, 26.5, 26.6, 26.4, 26.2, 26.3, 26.4],
            do: [5.62, 5.68, 5.74, 5.70, 5.52, 5.48, 5.42, 5.58, 5.64, 5.55, 5.48, 5.62, 5.71],
            ph: [8.12, 8.18, 8.24, 8.30, 8.14, 8.26, 8.32, 8.20, 8.28, 8.36, 8.22, 8.42, 8.51]
        },
        '7d': {
            title: 'Dinamika Parameter Air (7 Hari Terakhir)',
            labels: ['Mulai Periode', 'Senin', 'Selasa', 'Rabu', 'Tengah Periode', 'Jumat', 'Sabtu', 'Sekarang'],
            suhu: [29.5, 30.2, 31.0, 30.8, 29.6, 30.4, 31.8, 32.4],
            salinitas: [25.8, 26.0, 26.2, 26.5, 26.4, 26.3, 26.5, 26.4],
            do: [5.75, 5.68, 5.55, 5.62, 5.70, 5.64, 5.68, 5.71],
            ph: [8.15, 8.22, 8.35, 8.28, 8.20, 8.30, 8.45, 8.51]
        },
        '30d': {
            title: 'Dinamika Parameter Air (30 Hari Terakhir)',
            labels: ['Mulai Periode', 'Minggu 1', 'Minggu 2', 'Tengah Periode', 'Minggu 4', 'Sekarang'],
            suhu: [29.0, 30.0, 30.5, 30.2, 31.5, 32.4],
            salinitas: [25.5, 26.0, 26.2, 26.5, 26.3, 26.4],
            do: [5.80, 5.70, 5.60, 5.65, 5.68, 5.71],
            ph: [8.10, 8.20, 8.30, 8.25, 8.40, 8.51]
        }
    };

    if (canvas && typeof Chart !== 'undefined') {
        const ctx = canvas.getContext('2d');
        const defaultSet = rangeDataSets['24h'];

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: defaultSet.labels,
                datasets: [
                    // Suhu (°C) - Kurva Orange Atas
                    {
                        label: 'Suhu (°C)',
                        data: defaultSet.suhu,
                        borderColor: '#ea580c',
                        backgroundColor: 'transparent',
                        borderWidth: 2.3,
                        tension: 0.45,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#ea580c',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                        yAxisID: 'ySuhu',
                    },
                    // Salinitas (ppt) - Kurva Ungu
                    {
                        label: 'Salinitas (ppt)',
                        data: defaultSet.salinitas,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'transparent',
                        borderWidth: 2.3,
                        tension: 0.45,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#8b5cf6',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                        yAxisID: 'ySal',
                    },
                    // DO (mg/L) - Kurva Biru/Cyan
                    {
                        label: 'DO (mg/L)',
                        data: defaultSet.do,
                        borderColor: '#0284c7',
                        backgroundColor: 'transparent',
                        borderWidth: 2.3,
                        tension: 0.45,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#0284c7',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                        yAxisID: 'yDO',
                    },
                    // pH - Kurva Hijau Bawah
                    {
                        label: 'pH',
                        data: defaultSet.ph,
                        borderColor: '#22c55e',
                        backgroundColor: 'transparent',
                        borderWidth: 2.3,
                        tension: 0.45,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#22c55e',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                        yAxisID: 'ypH',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 600,
                    easing: 'easeOutQuart'
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false // Menggunakan custom legend di HTML sesuai mockup
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        titleColor: '#ffffff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        boxPadding: 4,
                        callbacks: {
                            label: function (context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 });
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: false
                    },
                    ySuhu: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        min: 26,
                        max: 35,
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false,
                        },
                        ticks: {
                            display: false,
                        },
                        border: {
                            color: '#0f172a',
                            width: 2
                        }
                    },
                    ySal: {
                        type: 'linear',
                        display: false,
                        min: 20,
                        max: 32,
                    },
                    yDO: {
                        type: 'linear',
                        display: false,
                        min: 4,
                        max: 8,
                    },
                    ypH: {
                        type: 'linear',
                        display: false,
                        min: 7,
                        max: 10,
                    }
                }
            }
        });
    }

    // =========================================================================
    // 2. FILTER RENTANG WAKTU (1 Jam, 24 Jam, 7 Hari, 30 Hari)
    // =========================================================================
    const rangeButtons = document.querySelectorAll('#timeRangeFilter .rsh-pill');
    const startInput = document.getElementById('filterStartDate');
    const endInput   = document.getElementById('filterEndDate');

    rangeButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            rangeButtons.forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');

            const range = this.getAttribute('data-range');
            const targetSet = rangeDataSets[range];

            if (targetSet && chartInstance) {
                if (rccTitle) rccTitle.textContent = targetSet.title;

                chartInstance.data.labels = targetSet.labels;
                chartInstance.data.datasets[0].data = targetSet.suhu;
                chartInstance.data.datasets[1].data = targetSet.salinitas;
                chartInstance.data.datasets[2].data = targetSet.do;
                chartInstance.data.datasets[3].data = targetSet.ph;
                chartInstance.update();
            }

            // Sesuaikan tanggal input jika tombol rentang waktu diklik
            if (startInput && endInput) {
                const today = '2026-11-11';
                endInput.value = today;
                if (range === '7d') {
                    startInput.value = '2026-11-04';
                } else if (range === '30d') {
                    startInput.value = '2026-10-12';
                } else {
                    startInput.value = today;
                }
            }
        });
    });

    // =========================================================================
    // 3. TANGGAL: Tidak ada reload otomatis — tanggal dibaca saat tombol unduh diklik
    // =========================================================================
    // (auto-reload dihapus; getFilterParams() membaca nilai terbaru saat unduh)

    // =========================================================================
    // 4. UNDUH LAPORAN PDF RESMI SESUAI RENTANG TANGGAL USER
    // =========================================================================
    const btnUnduhPdf = document.getElementById('btnUnduhPdf');
    if (btnUnduhPdf) {
        btnUnduhPdf.addEventListener('click', function () {
            const { start, end, device } = getFilterParams();
            const baseUrl = btnUnduhPdf.getAttribute('data-url') || '/riwayat-sensor/export-pdf';
            const targetUrl = `${baseUrl}?device=${encodeURIComponent(device)}&start_date=${encodeURIComponent(start)}&end_date=${encodeURIComponent(end)}`;
            // Buka pratinjau dokumen PDF di tab baru untuk langsung dicetak / disimpan
            window.open(targetUrl, '_blank');
        });
    }

    // =========================================================================
    // 5. UNDUH LAPORAN CSV SESUAI RENTANG TANGGAL USER
    // =========================================================================
    const btnUnduhCsv = document.getElementById('btnUnduhCsv');
    if (btnUnduhCsv) {
        btnUnduhCsv.addEventListener('click', function () {
            const { start, end, device } = getFilterParams();
            const baseUrl = btnUnduhCsv.getAttribute('data-url') || '/riwayat-sensor/export-csv';
            const targetUrl = `${baseUrl}?device=${encodeURIComponent(device)}&start_date=${encodeURIComponent(start)}&end_date=${encodeURIComponent(end)}`;
            window.location.href = targetUrl;
        });
    }

    // =========================================================================
    // 6. PAGINASI TABEL & KARTU (8 entri per halaman)
    // =========================================================================
    const rows = document.querySelectorAll('.sensor-row');
    const cards = document.querySelectorAll('.sensor-card-item');
    const pgButtons = document.querySelectorAll('.riwayat-pager .pg-btn[data-page]');
    const btnPrev = document.getElementById('pgPrev');
    const btnNext = document.getElementById('pgNext');
    const footInfo = document.getElementById('footInfo');

    const itemsPerPage = 8;
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
            footInfo.innerHTML = `Menampilkan ${start}&ndash;${end} dari ${totalRows} data &bull; Halaman ${currentPage + 1} dari ${totalPages}`;
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

    // =========================================================================
    // 7. INTERAKSI BUKA KALENDER
    // =========================================================================
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
})();
