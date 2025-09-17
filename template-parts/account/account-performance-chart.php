<?php

if (!defined('ABSPATH')) exit;

if (! isset($args) || ! is_array($args) || empty($args)) { return; }

$chart_title = isset($args['title']) ? $args['title'] : 'Performance Chart';
$chart_title_tooltip = isset($args['title_tooltip']) && is_array($args['title_tooltip'])? $args['title_tooltip'] : [];

$periods = [
    [
        'value' => 7,
        'text'  => 'LAST 7 DAYS'
    ],
    [
        'value' => 14,
        'text'  => 'LAST 14 DAYS'
    ],
    [
        'value' => 30,
        'text'  => 'LAST 30 DAYS'
    ]
];


?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="account-performance-chart mt-card">
    <div class="account-performance-chart__header">
        <div class="d-flex flex-column flex-md-row align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 w-100">
                <div style="color: white; font-size: 20px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word"><?= $chart_title ?></div>
                <?php if(isset($chart_title_tooltip) && !empty($chart_title_tooltip)): ?>
                    <span class="mt-tooltip">
                        <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0"
                            aria-label="Daily Loss Limit information"></i>
                        <span class="mt-tooltip__panel" role="tooltip">
                            <?php if(isset($chart_title_tooltip['title'])): ?>
                                <div class="mt-tooltip__title"><?= $chart_title_tooltip['title'] ?></div>
                            <?  endif; ?>
                            <?php if(isset($chart_title_tooltip['description'])): ?>
                                <div class="mt-tooltip__body">
                                    <?= $chart_title_tooltip['description'] ?>
                                </div>
                            <?  endif; ?>
                        </span>
                    </span>
                <?  endif; ?>

            </div>
            <select id="lastDaysSelect" class="d-block form-select" name="last-days-select">
                <?php foreach ($periods as $period): ?>
                    <option value="last_<?php echo $period['value']; ?>_days">
                        <?php echo htmlspecialchars($period['text']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="account-performance-chart__header">
        <div id="account-performance-chart"></div>
    </div>
</div>

<script>

const toMoney = (value) => {
    return `\$ ${value}`
}

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
            show: true,
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
                formatter: toMoney,
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
                formatter: (value) => toMoney(value.toFixed(2)),
            },
        },
    }
};

const formatDaysSelectText = (value) => {
    return `LAST ${value} DAYS`
}

const periods = {
    'last_7_days': {
        value: 7,
        text: 'LAST 7 DAYS'
    },
    'last_14_days': {
        value: 14,
        text: 'LAST 14 DAYS'
    },
    'last_30_days': {
        value: 30,
        text: 'LAST 30 DAYS'
    }
}

const days = Object.values(periods)[0].value;


const dataChart = {
    id: (new Date()).getTime(), //TODO: is ID needed?
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

function filterLastDaysHandler({event}){
    console.log(event.target.value);
}

const lastDaysSelect = document.getElementById("lastDaysSelect")
lastDaysSelect.addEventListener('change', filterLastDaysHandler)
var chart = new ApexCharts(document.getElementById("account-performance-chart"), options);

chart.render();
</script>