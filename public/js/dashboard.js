(function () {
    'use strict';

    const canvas = document.getElementById('chartTrenTelemetri');
    if (!canvas) return;

    const trendData = window.TELEMETRI_TREND || {
        labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', 'Sekarang'],
        datasets: [
            { id: 'ph', label: 'pH (8,48)', data: [6.2, 6.4, 6.4, 6.5, 6.6, 6.1, 6.3], borderColor: '#10b981' },
            { id: 'do', label: 'DO (5,40 mg/L)', data: [6.9, 7.3, 7.5, 7.4, 7.3, 7.6, 7.7], borderColor: '#0284c7' },
            { id: 'salinitas', label: 'Salinitas (26,4 ppt)', data: [7.5, 7.4, 7.3, 7.2, 7.1, 7.3, 7.6], borderColor: '#8b5cf6' },
            { id: 'suhu', label: 'Suhu (32,4 °C)', data: [7.7, 7.8, 7.7, 7.5, 8.4, 7.2, 8.2], borderColor: '#f97316' },
        ]
    };

    const allDatasets = trendData.datasets.map(function (ds) {
        return {
            label: ds.label,
            data: ds.data,
            borderColor: ds.borderColor,
            backgroundColor: 'transparent',
            borderWidth: 2.8,
            tension: 0.45,
            pointRadius: 0,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: ds.borderColor,
            pointHoverBorderColor: '#ffffff',
            pointHoverBorderWidth: 2,
            id: ds.id,
        };
    });

    const chart = new Chart(canvas, {
        type: 'line',
        data: {
            labels: trendData.labels,
            datasets: allDatasets,
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { family: 'Poppins', size: 12, weight: '600' },
                    bodyFont: { family: 'Poppins', size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { color: '#e2e8f0' },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Poppins', size: 11.5 },
                    },
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    border: { display: false },
                    ticks: {
                        color: '#64748b',
                        font: { family: 'Poppins', size: 11.5 },
                        padding: 8,
                    },
                },
            },
        },
    });

    // Filter Buttons [Semua] [pH] [DO] [Salinitas] [Suhu]
    const filterBtns = document.querySelectorAll('#chartFilterTabs .cct-filter-btn');
    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterBtns.forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');
            if (filter === 'all') {
                chart.data.datasets = allDatasets;
            } else {
                chart.data.datasets = allDatasets.filter(function (ds) { return ds.id === filter; });
            }
            chart.update();
        });
    });
})();
