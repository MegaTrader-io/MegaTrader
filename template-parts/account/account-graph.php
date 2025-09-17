<?php

if (!defined('ABSPATH')) exit;

?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="account-graph mt-card">
    <div class="account-graph__header">
        <div class="d-flex flex-column flex-md-row align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 w-100">
                <div style="color: white; font-size: 20px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">100K Growth Plan</div>
                <span class="mt-tooltip">
                    <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0"
                        aria-label="Daily Loss Limit information"></i>
                    <span class="mt-tooltip__panel" role="tooltip">
                        <div class="mt-tooltip__title">Daily Loss Limit (DLL)</div>
                        <div class="mt-tooltip__body">
                            Reaching the DLL pauses trading for the day. It’s removed once a profit
                            milestone is
                            met.
                        </div>
                    </span>
                </span>
            </div>
            <select id="mega-navigation-select" class="mega-navigation-select d-block form-select">
                <option value="last_7_days">LAST 7 DAYS</option>
                <option value="last_14_days">LAST 14 DAYS</option>
                <option value="last_30_days">LAST 30 DAYS</option>
            </select>
        </div>
    </div>
    <div class="account-graph__header">
        <div id="account-graph"></div>
    </div>
</div>

<script>

const chartConfig = {
    type: "line",
    height: '100%',
    series: [
        {
            name: "Pro Plan Revenue",
            data: [23000, 23250, 23250, 23500, 23500, 23950, 24000, 24300, 24300, 24550, 24600, 24850],
        },
        {
            name: "Upper Bound",
            data: [24250, 24250, 24250, 24250, 24250, 24250, 24250, 24250, 24250, 24250, 24250, 24250],
        },
        {
            name: "Lower Bound",
            data: [23250, 23250, 23250, 23250, 23250, 23250, 23250, 23250, 23250, 23250, 23250, 23250],
        },
    ],
    options: {
        chart: {
            toolbar: {
                show: false,
            },
        },
        title: {
            show: false,
        },
        dataLabels: {
            enabled: false,
        },
        colors: ["#FFE7B8", "#24b8a6", "#FF4D4D"],
        stroke: {
            lineCap: "round",
            curve: "smooth",
            width: [2, 2, 2],
        },
        markers: {
            size: [0, 5, 5],
            colors: ["#FF4D4D", "#24b8a6"],
            strokeColors: 'transparent',
            strokeWidth: 0
        },
        legend: {
            show: false
        },
        xaxis: {
            axisTicks: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
            labels: {
                style: {
                    colors: "#A8A29E",
                    fontSize: "12px",
                    fontFamily: "inherit",
                    fontWeight: 400,
                },
            },
            categories: [0, 2, 4, 6, 8, 10, 12, 14, 16, 18],
        },
        yaxis: {
            labels: {
                formatter: (value) => `$ ${value}`,
                style: {
                    colors: "#A8A29E",
                    fontSize: "12px",
                    fontFamily: "inherit",
                    fontWeight: 400,
                },
            },
        },
        grid: {
            show: true,
            borderColor: "#374151",
            strokeDashArray: 5,
        },
        fill: {
            opacity: 0.8,
        },
        tooltip: {
            theme: "dark",
            x: {
                show: true,
            },
            y: {
                formatter: (value) => `$ ${value.toFixed(2)}`,
            },
        },
    }
};

const periods = [
    {id: 'last_7_days', text: 'LAST 7 DAYS'},
    {id: 'last_14_days', text: 'LAST 14 DAYS'},
    {id: 'last_30_days', text: 'LAST 30 DAYS'},
]

const selectPeriod = periods[0];

const days = {
    'last_30_days': 30,
    'last_14_days': 14,
    'last_7_days': 7,
}[selectPeriod.id];

const dataChart = {
    id: (new Date()).getTime(),
    series: [
        {
            name: "Pro Plan Revenue",
            data: Array(days).fill('').map(() => Math.floor(Math.random() * 35000 + 1)),
        },
        {
            name: "Upper Bound",
            data: Array(days).fill('').map(() => 20000),
        },
        {
            name: "Lower Bound",
            data: Array(days).fill('').map(() => 10000),
        },
    ],
    options: {
        ...chartConfig.options,
        xaxis: {
            ...chartConfig.options.xaxis,
            categories: Array(days).fill('').map((_, index) => (index + 1)),
        },
    }
};


var options = {
  series: dataChart.series,
  ...dataChart.options
}


var chart = new ApexCharts(document.getElementById("account-graph"), options);

chart.render();
</script>