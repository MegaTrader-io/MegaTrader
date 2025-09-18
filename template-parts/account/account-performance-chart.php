<?php

if (!defined('ABSPATH')) exit;

if (! isset($args) || ! is_array($args) || empty($args)) { return; }


$chart  = isset($args['chart']) && is_array($args['chart']) ? $args['chart'] : [];
$chart_title  = $chart['title'] ?? (($args['title'] ?? '') ?: 'Account');
$yellow = $chart['yellow'] ?? ($chart['series'] ?? []); // currentBalance diario
$green  = $chart['green']  ?? null; // equityPassLevel
$red    = $chart['red']    ?? null; // maxLossLimitEquityLevel


$chart_title_tooltip = [
    'title' => 'Tooltip Title',
    'description' => 'Description to be updated with real data.'
];


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
            <select id="lastDaysSelect" class="form-select w-fit w-sm-100" name="last-days-select">
                <?php foreach ($periods as $period): ?>
                    <option value="<?= $period['value']; ?>">
                        <?php echo htmlspecialchars($period['text']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="account-performance-chart__header">
        <div id="account-performance-chart" style="margin-left: -20px;"></div>
    </div>
</div>

<script>
/* Using ApexCharts 
 * docs: https://apexcharts.com/docs
 */
let chart;
const defaultDays = 7;

const toMoney = (value) => {
    return `\$ ${value}`
}

const getChartConfig = (days) => ({
    height: '100%',
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
        followCursor: true,
        theme: "dark",
        x: {
            show: true,
            formatter: (value) => `Day ${value}`,
        },
        y: {
            formatter: (value) => toMoney(value.toFixed(2)),
        },
    }
});

const getChartSeries = (days) => [
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
]

const getChartOptions = (_days = defaultDays) => {
    const days = parseInt(_days);
    return {
        ...getChartConfig(days),
        series: getChartSeries(days),
    };
}


function filterLastDaysHandler(event){
    console.log(event.target.value);
    chart.updateOptions(getChartOptions(event.target.value))
}

const lastDaysSelect = document.getElementById("lastDaysSelect")
lastDaysSelect.addEventListener('change', filterLastDaysHandler)

chart = new ApexCharts(document.getElementById("account-performance-chart"), getChartOptions(lastDaysSelect.value));

chart.render();
</script>