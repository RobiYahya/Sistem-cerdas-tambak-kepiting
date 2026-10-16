(function () {
    'use strict';

    // ============ 1. GRAFIK TREN (Chart.js, data dummy dari controller) ============
    const hours = Array.from({ length: 25 }, (_, i) => String(i).padStart(2, '0'));

    (window.TRENDS || []).forEach(function (t) {
        const canvas = document.getElementById('chart-' + t.id);
        if (!canvas) return;

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: hours,
                datasets: [{
                    data: t.data,
                    borderColor: '#8cc63f',
                    borderWidth: 2.5,
                    tension: 0.42,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    pointHoverBackgroundColor: '#8cc63f',
                    fill: false,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#153024',
                        titleFont: { family: 'Poppins' },
                        bodyFont: { family: 'Poppins' },
                        displayColors: false,
                        callbacks: { title: (items) => 'Jam ' + items[0].label + ':00' },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { color: '#d9dde0' },
                        ticks: {
                            color: '#7b848e',
                            font: { family: 'Poppins', size: 12 },
                            autoSkip: false,
                            maxRotation: 0,
                            // tampilkan label tiap 4 jam: 00, 04, 08, ... 24
                            callback: function (val, idx) { return idx % 4 === 0 ? this.getLabelForValue(val) : ''; },
                        },
                    },
                    y: {
                        min: t.min,
                        max: t.max,
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            color: '#7b848e',
                            font: { family: 'Poppins', size: 12 },
                            stepSize: t.step,
                            padding: 8,
                        },
                    },
                },
            },
        });
    });

    // ============ 2. PAGINATION TREN (4 grafik per halaman) ============
    const trendCards = document.querySelectorAll('.trend-card');
    const pgButtons  = document.querySelectorAll('.pg[data-go]');
    const pageInfo   = document.getElementById('pageInfo');
    const totalPages = Math.ceil(trendCards.length / 4);
    let trendPage = 0;

    function showTrendPage(p) {
        trendPage = Math.max(0, Math.min(totalPages - 1, p));
        trendCards.forEach((c) => { c.hidden = Number(c.dataset.page) !== trendPage; });
        pgButtons.forEach((b) => b.classList.toggle('active', Number(b.dataset.go) === trendPage));
        pageInfo.textContent = 'Halaman ' + (trendPage + 1) + ' dari ' + totalPages;
        // chart yang sebelumnya tersembunyi perlu dihitung ulang ukurannya
        window.dispatchEvent(new Event('resize'));
    }
    pgButtons.forEach((b) => b.addEventListener('click', () => showTrendPage(Number(b.dataset.go))));
    document.getElementById('pgPrev').addEventListener('click', () => showTrendPage(trendPage - 1));
    document.getElementById('pgNext').addEventListener('click', () => showTrendPage(trendPage + 1));
    showTrendPage(0);

    // ============ 3. KARTU PARAMETER GESER (2 kartu per halaman) ============
    const paramCards = document.querySelectorAll('.param-card');
    const paramDots  = document.querySelectorAll('#paramDots i');
    const paramPages = Math.ceil(paramCards.length / 2);
    let paramPage = 0;

    function showParamPage(p) {
        paramPage = (p + paramPages) % paramPages; // berputar
        paramCards.forEach((c) => { c.hidden = Number(c.dataset.page) !== paramPage; });
        paramDots.forEach((d, i) => d.classList.toggle('on', i === paramPage));
    }
    document.getElementById('paramPrev').addEventListener('click', () => showParamPage(paramPage - 1));
    document.getElementById('paramNext').addEventListener('click', () => showParamPage(paramPage + 1));
    showParamPage(0);

    // ============ 4. TOMBOL RENTANG WAKTU (24 jam / 7 hari / 30 hari) ============
    // Masih tampilan saja: hanya mengganti status aktif. Nanti dihubungkan ke API/database.
    document.querySelectorAll('#ranges .chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('#ranges .chip').forEach((c) => c.classList.remove('active'));
            chip.classList.add('active');
        });
    });
})();