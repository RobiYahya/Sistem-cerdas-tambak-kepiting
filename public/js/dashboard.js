(function () {
    'use strict';

    // ============ 1. GRAFIK TREN (Chart.js, data dummy dari controller) ============
    const hours = Array.from({ length: 25 }, (_, i) => String(i).padStart(2, '0'));
    const days7 = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    const days30 = ['Tgl 1', 'Tgl 5', 'Tgl 10', 'Tgl 15', 'Tgl 20', 'Tgl 25', 'Tgl 30'];

    const chartInstances = {};

    (window.TRENDS || []).forEach(function (t) {
        const canvas = document.getElementById('chart-' + t.id);
        if (!canvas) return;

        const chart = new Chart(canvas, {
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
                        callbacks: {
                            title: (items) => {
                                const lbl = items[0].label;
                                return lbl.includes(':') || /^\d+$/.test(lbl) ? 'Jam ' + lbl + ':00' : lbl;
                            }
                        },
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
                            callback: function (val, idx) {
                                if (this.chart.data.labels.length === 25) {
                                    return idx % 4 === 0 ? this.getLabelForValue(val) : '';
                                }
                                return this.getLabelForValue(val);
                            },
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

        chartInstances[t.id] = { chart: chart, originalData: t.data, baseMin: t.min, baseMax: t.max };
    });

    // ============ 2. PAGINATION TREN (4 grafik per halaman) ============
    const trendCards = document.querySelectorAll('.trend-card');
    const pgButtons  = document.querySelectorAll('.pg[data-go]');
    const pageInfo   = document.getElementById('pageInfo');
    const totalPages = Math.ceil(trendCards.length / 4);
    let trendPage = 0;

    function showTrendPage(p) {
        if (totalPages === 0) return;
        trendPage = Math.max(0, Math.min(totalPages - 1, p));

        // Gunakan CSS class (bukan attribute hidden) agar Chart.js bisa resize
        trendCards.forEach((c) => {
            if (Number(c.dataset.page) !== trendPage) {
                c.classList.add('trend-hidden');
            } else {
                c.classList.remove('trend-hidden');
            }
        });

        pgButtons.forEach((b) => b.classList.toggle('active', Number(b.dataset.go) === trendPage));
        if (pageInfo) {
            pageInfo.textContent = 'Halaman ' + (trendPage + 1) + ' dari ' + totalPages;
        }

        // Resize semua chart di halaman yang sedang aktif agar dimensi benar
        setTimeout(() => {
            trendCards.forEach((c) => {
                if (Number(c.dataset.page) === trendPage) {
                    const canvas = c.querySelector('canvas');
                    if (canvas) {
                        const id = canvas.id.replace('chart-', '');
                        const item = chartInstances[id];
                        if (item && item.chart) {
                            item.chart.resize();
                        }
                    }
                }
            });
        }, 80);
    }

    pgButtons.forEach((b) => b.addEventListener('click', () => showTrendPage(Number(b.dataset.go))));
    const pgPrev = document.getElementById('pgPrev');
    const pgNext = document.getElementById('pgNext');
    if (pgPrev) pgPrev.addEventListener('click', () => showTrendPage(trendPage - 1));
    if (pgNext) pgNext.addEventListener('click', () => showTrendPage(trendPage + 1));
    showTrendPage(0);

    // Touch Swipe untuk Grafik Tren di HP
    const trendSection = document.querySelector('.card-trend');
    if (trendSection) {
        let touchStartX = 0;
        let touchEndX = 0;
        trendSection.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        trendSection.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) {
                showTrendPage(trendPage + 1);
            } else if (touchEndX - touchStartX > 50) {
                showTrendPage(trendPage - 1);
            }
        }, { passive: true });
    }

    // ============ 3. KARTU PARAMETER GESER (Jika mode slider aktif) ============
    const paramPanel = document.querySelector('.param-panel');
    if (paramPanel) {
        const paramCards = paramPanel.querySelectorAll('.param-card');
        const paramDots  = paramPanel.querySelectorAll('#paramDots i');
        const paramPages = Math.ceil(paramCards.length / 2);
        let paramPage = 0;

        function showParamPage(p) {
            if (paramPages === 0) return;
            paramPage = (p + paramPages) % paramPages; // berputar
            paramCards.forEach((c) => { c.hidden = Number(c.dataset.page) !== paramPage; });
            paramDots.forEach((d, i) => d.classList.toggle('on', i === paramPage));
        }

        const paramPrev = document.getElementById('paramPrev');
        const paramNext = document.getElementById('paramNext');
        if (paramPrev) paramPrev.addEventListener('click', () => showParamPage(paramPage - 1));
        if (paramNext) paramNext.addEventListener('click', () => showParamPage(paramPage + 1));
        showParamPage(0);

        // Touch Swipe untuk Parameter Air di HP jika ada panel
        let touchStartX = 0;
        let touchEndX = 0;
        paramPanel.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        paramPanel.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 40) {
                showParamPage(paramPage + 1);
            } else if (touchEndX - touchStartX > 40) {
                showParamPage(paramPage - 1);
            }
        }, { passive: true });
    }

    // ============ 4. TOMBOL RENTANG WAKTU INTERAKTIF (24 jam / 7 hari / 30 hari) ============
    document.querySelectorAll('#ranges .chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('#ranges .chip').forEach((c) => c.classList.remove('active'));
            chip.classList.add('active');

            const rangeText = chip.textContent.trim().toLowerCase();
            Object.keys(chartInstances).forEach((key) => {
                const item = chartInstances[key];
                if (!item || !item.chart) return;

                if (rangeText.includes('7')) {
                    item.chart.data.labels = days7;
                    // Ambil 7 sampel rata-rata
                    item.chart.data.datasets[0].data = [
                        item.originalData[0],
                        item.originalData[4],
                        item.originalData[8],
                        item.originalData[12],
                        item.originalData[16],
                        item.originalData[20],
                        item.originalData[24],
                    ];
                } else if (rangeText.includes('30')) {
                    item.chart.data.labels = days30;
                    item.chart.data.datasets[0].data = [
                        item.originalData[2],
                        item.originalData[6],
                        item.originalData[10],
                        item.originalData[14],
                        item.originalData[18],
                        item.originalData[22],
                        item.originalData[24],
                    ];
                } else {
                    item.chart.data.labels = hours;
                    item.chart.data.datasets[0].data = item.originalData;
                }
                item.chart.update('none');
            });
        });
    });
})();