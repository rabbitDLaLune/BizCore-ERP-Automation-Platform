import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;

Alpine.start();

function renderSalesChart() {
    const canvas = document.getElementById('salesChart');

    if (!canvas || !window.salesReportChartData) {
        return;
    }

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: window.salesReportChartData.labels,
            datasets: [
                {
                    label: 'Sales Amount (RM)',
                    data: window.salesReportChartData.values,
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return 'RM ' + Number(context.raw).toFixed(2);
                        },
                    },
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function (value) {
                            return 'RM ' + value;
                        },
                    },
                },
            },
        },
    });
}

function renderInventoryChart() {
    const canvas = document.getElementById('inventoryChart');

    if (!canvas || !window.inventoryReportChartData) {
        return;
    }

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: window.inventoryReportChartData.labels,
            datasets: [
                {
                    label: 'Stock Quantity',
                    data: window.inventoryReportChartData.values,
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: true,
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                },
            },
        },
    });
}

function renderPurchaseRequestChart() {
    const canvas = document.getElementById('purchaseRequestChart');

    if (!canvas || !window.purchaseRequestReportChartData) {
        return;
    }

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: window.purchaseRequestReportChartData.labels,
            datasets: [
                {
                    label: 'Requests',
                    data: window.purchaseRequestReportChartData.values,
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
            },
        },
    });
}

document.addEventListener('DOMContentLoaded', function () {
    renderSalesChart();
    renderInventoryChart();
    renderPurchaseRequestChart();
});
