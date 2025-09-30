<?php
if (!defined('ABSPATH'))
    exit;

if (!isset($args) || !is_array($args) || empty($args)) {
    return;
}

$chart = isset($args['chart']) && is_array($args['chart']) ? $args['chart'] : [];
$chart_title = $chart['title'] ?? (($args['title'] ?? '') ?: 'Account');
$plan_revenue = $chart['plan_revenue'] ?? ($chart['series'] ?? []); // [{date,value}]
$upper_bound = $chart['upper_bound'] ?? null;
$lower_bound = $chart['lower_bound'] ?? null;
$periods = $chart['periods'] ?? [
    ['value' => 7, 'text' => 'LAST 7 DAYS'],
    ['value' => 14, 'text' => 'LAST 14 DAYS'],
    ['value' => 30, 'text' => 'LAST 30 DAYS'],
];

// Adaptar a {x, y} para el JS
$js_series = [];
foreach ($plan_revenue as $r) {
    $d = substr((string) ($r['date'] ?? $r['x'] ?? ''), 0, 10);
    $v = isset($r['value']) ? (float) $r['value'] : (isset($r['y']) ? (float) $r['y'] : null);
    if ($d && $v !== null)
        $js_series[] = ['x' => $d, 'y' => $v];
}
?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="account-performance-chart mt-card">
    <div class="account-performance-chart__header">
        <div class="d-flex flex-column flex-md-row align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 w-100">
                <div class="mt-card__title__text fw-medium text-uppercase">
                    <?= esc_html($chart_title) ?>
                </div>
                <?php if (isset($chart_title_tooltip) && !empty($chart_title_tooltip)): ?>
                    <span class="mt-tooltip">
                        <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Chart information"></i>
                        <span class="mt-tooltip__panel" role="tooltip">
                            <?php if (isset($chart_title_tooltip['title'])): ?>
                                <div class="mt-tooltip__title"><?= $chart_title_tooltip['title'] ?></div>
                            <?php endif; ?>
                            <?php if (isset($chart_title_tooltip['description'])): ?>
                                <div class="mt-tooltip__body"><?= $chart_title_tooltip['description'] ?></div>
                            <?php endif; ?>
                        </span>
                    </span>
                <?php endif; ?>
            </div>

            <select id="lastDaysSelect" class="form-select w-fit w-sm-100" name="last-days-select">
                <?php foreach ($periods as $period): ?>
                    <option value="<?= (int) $period['value']; ?>">
                        <?= esc_html($period['text']); ?>
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
    // ===== Datos base desde PHP =====
    const RAW_SERIES = <?php echo json_encode($js_series, JSON_UNESCAPED_SLASHES); ?>; // [{x:'YYYY-MM-DD', y:Number}]

    console.log('[MT][Chart] dates:', RAW_SERIES.map(p => p.x));
    console.log('[MT][Chart] values:', RAW_SERIES.map(p => p.y));


    const PERIODS = <?php echo json_encode($periods); ?>;
    const UPPER_BOUND = <?php echo ($upper_bound !== null) ? json_encode((float) $upper_bound) : 'null'; ?>;
    const LOWER_BOUND = <?php echo ($lower_bound !== null) ? json_encode((float) $lower_bound) : 'null'; ?>;
    const chartTitle = <?php echo json_encode($chart_title); ?>;

    // Logs temporales (quítalos cuando termines de probar)
    console.log('[MT][Chart] title:', chartTitle);
    console.log('[MT][Chart] periods:', PERIODS);
    console.log('[MT][Chart] raw points:', RAW_SERIES.length, RAW_SERIES);

    // ===== Utilidades =====
    const constLine = (val, len) => (Number.isFinite(val) ? Array(len).fill(val) : Array(len).fill(null));
    const toMoney = (v) => `$ ${Number(v).toFixed(2)}`;

    function sliceData(days) {
        const n = Math.max(1, parseInt(days));
        const slice = RAW_SERIES.slice(-n);
        const dateLabels = slice.map(p => p.x);                  // fechas reales para tooltip
        const categories = Array.from({ length: slice.length }, (_, i) => i + 1); // 1..N
        const main = slice.map(p => p.y);
        return {
            categories, dateLabels, main,
            upper: constLine(UPPER_BOUND, slice.length),
            lower: constLine(LOWER_BOUND, slice.length)
        };
    }

    function getChartConfig(len) {
        return {
            height: '100%',
            chart: { toolbar: { show: false } },
            dataLabels: { enabled: false },
            colors: ["#FFE7B8", "#24b8a6", "#FF4D4D"],
            stroke: { lineCap: "round", curve: "smooth", width: [2, 2, 2] },
            markers: { size: [0, 5, 5], colors: ["#FF4D4D", "#24b8a6"], strokeColors: 'transparent', strokeWidth: 0 },
            legend: { show: false },
            xaxis: {
                categories: [], // se setea con labels
                axisTicks: { show: false }, axisBorder: { show: false },
                labels: { style: { colors: "#A8A29E", fontSize: "12px", fontFamily: "inherit", fontWeight: 400 } }
            },
            yaxis: {
                labels: { formatter: (v) => toMoney(v), style: { colors: "#A8A29E", fontSize: "12px", fontFamily: "inherit", fontWeight: 400 } }
            },
            grid: { show: true, borderColor: "#374151", strokeDashArray: 5 },
            fill: { opacity: 0.8 },
            tooltip: {
                followCursor: true, theme: "dark",
                x: { formatter: (value, ctx) => ctx?.w?.globals?.categoryLabels?.[ctx?.dataPointIndex ?? 0] ?? value },
                y: { formatter: (value) => toMoney(value) }
            }
        };
    }

    function getChartOptions(days) {
        const s = sliceData(days);
        const cfg = getChartConfig(s.categories.length);
        cfg.xaxis.categories = s.categories; // 1..N
        cfg.tooltip = cfg.tooltip || {};
        cfg.tooltip.x = { formatter: (_val, { dataPointIndex }) => s.dateLabels[dataPointIndex] || _val };
        cfg.series = [
            { name: "Equity (Balance)", data: s.main },
            { name: "Upper Bound", data: s.upper },
            { name: "Lower Bound", data: s.lower },
        ];
        return cfg;
    }

    // ===== Init =====
    const lastDaysSelect = document.getElementById("lastDaysSelect");
    if (lastDaysSelect && lastDaysSelect.options.length === 1) lastDaysSelect.disabled = true;

    const initialDays = lastDaysSelect ? lastDaysSelect.value : (PERIODS[0]?.value || 7);
    console.log('[MT][Chart] initialDays:', initialDays);

    let chart = new ApexCharts(document.getElementById("account-performance-chart"), getChartOptions(initialDays));
    chart.render();

    lastDaysSelect?.addEventListener('change', function (e) {
        console.log('[MT][Chart] change period ->', e.target.value);
        chart.updateOptions(getChartOptions(e.target.value));
    });
</script>