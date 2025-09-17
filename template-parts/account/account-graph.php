<?php

if (!defined('ABSPATH')) exit;

?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="account-graph mt-card">
    <div class="account-graph__header">
        <div style="width: 100%; height: 100%; justify-content: space-between; align-items: center; display: inline-flex">
            <div style="flex: 1 1 0; justify-content: flex-start; align-items: center; gap: 8px; display: flex">
                <div style="color: white; font-size: 20px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">100K Growth Plan</div>
                <div style="width: 24px; height: 24px; position: relative">
                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                    <div style="width: 20px; height: 20px; left: 2px; top: 2px; position: absolute; background: var(--Basic-White, white)"></div>
                </div>
            </div>
            <div data-show-helpertext="false" data-showicon="false" data-status="filled" style="flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                <div style="width: 250px; padding-left: 16px; padding-right: 16px; padding-top: 12px; padding-bottom: 12px; background: rgba(30, 30, 30, 0.70); border-radius: 12px; outline: 1px var(--Colors-Gray-700, #404040) solid; outline-offset: -1px; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                    <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">LAST 10 DAYS</div>
                    <div style="width: 24px; height: 24px; position: relative">
                        <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                        <div style="width: 10px; height: 5px; left: 7px; top: 10px; position: absolute; background: var(--White, white)"></div>
                    </div>
                </div>
            </div>
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