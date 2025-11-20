<?php
defined('ABSPATH') || exit;

/* ==== Resolver accountId ==== */
$acc_id = '';
if (!empty($args['meta']['accountId'])) {
  $acc_id = (string) $args['meta']['accountId'];
} elseif (!empty($args['account']['accountId'])) {
  $acc_id = (string) $args['account']['accountId'];
}

/* ==== Data real Card 1 + métricas de trades ==== */
$real_net_pnl = 0.0;
$real_trades_ct = 0;
$equity_series = []; // [{date,value}]

// métricas win/loss (por trade)
$wins_count = 0;
$loss_count = 0;
$gross_win = 0.0; // suma de net > 0
$gross_loss = 0.0; // suma de abs(net < 0)

// métricas por día (para streak y gráfico)
$daily_pnls = []; // 'YYYY-MM-DD' => ['pnl' => float, 'label' => 'MM/DD/YYYY']

/* ==== Card 5 / 6: valores base ==== */
$current_day_streak_value = '—';
$current_day_streak_w = 0;   // días con PnL diario > 0
$current_day_streak_l = 0;   // días con PnL diario < 0
$current_day_streak_dir = 'down'; // 'up' | 'down'

$current_trades_value = '0'; // streak de trades (cantidad consecutiva)
$current_w_total = 0;   // trades ganadores (histórico)
$current_l_total = 0;   // trades perdedores (histórico)
$current_trades_dir = 'down'; // 'up' | 'down'

if ($acc_id !== '' && function_exists('mt_trades_history_fetch')) {
  // leemos hasta 500 trades cerrados (mismo helper)
  $resp = mt_trades_history_fetch($acc_id, 'CLOSED', 1, 500);

  $records = isset($resp['records']) && is_array($resp['records']) ? $resp['records'] : [];
  $real_trades_ct = (int) ($resp['total'] ?? count($records));

  foreach ($records as $r) {
    if (!isset($r['net'])) {
      continue;
    }

    $n = (float) $r['net'];
    $real_net_pnl += $n;

    // contar wins / losses por trade (histórico)
    if ($n > 0) {
      $wins_count++;
      $gross_win += $n;
    } elseif ($n < 0) {
      $loss_count++;
      $gross_loss += abs($n);
    }

    // === Determinar el "día" del trade (para equity y streak por días) ===
    // usamos closeDate / openDate que ya vienen con mt_cutoff_date en el helper (MM/DD/YYYY)
    $day_label = '-';
    if (!empty($r['closeDate']) && $r['closeDate'] !== '-') {
      $day_label = (string) $r['closeDate'];
    } elseif (!empty($r['openDate']) && $r['openDate'] !== '-') {
      $day_label = (string) $r['openDate'];
    }

    if ($day_label !== '-' && $day_label !== '') {
      $dt = DateTime::createFromFormat('m/d/Y', $day_label);
      if ($dt instanceof DateTime) {
        $iso = $dt->format('Y-m-d');
        if (!isset($daily_pnls[$iso])) {
          $daily_pnls[$iso] = [
            'pnl' => 0.0,
            'label' => $day_label,
          ];
        }
        $daily_pnls[$iso]['pnl'] += $n;
      }
    }
  }

  // === Construir equity_series para el chart del Card 1 ===
  if (!empty($daily_pnls)) {
    ksort($daily_pnls); // orden cronológico por fecha ISO YYYY-MM-DD

    foreach ($daily_pnls as $date_iso => $row) {
      $pnl_day = (float) $row['pnl'];

      // Para el baseline: PnL de ese día, no acumulado
      $equity_series[] = [
        'date' => $date_iso,
        'value' => $pnl_day,
      ];
    }


    // === Contar días ganadores y perdedores (para badges W/L del Card 5) ===
    foreach ($daily_pnls as $date_iso => $row) {
      $p = (float) $row['pnl'];
      if ($p > 0) {
        $current_day_streak_w++;
      } elseif ($p < 0) {
        $current_day_streak_l++;
      }
    }

    // === Calcular streak de días consecutivos (últimos días) ===
    $dates = array_keys($daily_pnls);
    $streak = 0;
    $dir = null; // 'up' (ganador) | 'down' (perdedor)

    for ($i = count($dates) - 1; $i >= 0; $i--) {
      $p = (float) $daily_pnls[$dates[$i]]['pnl'];
      if ($p > 0) {
        if ($dir === null || $dir === 'up') {
          $streak++;
          $dir = 'up';
        } else {
          break;
        }
      } elseif ($p < 0) {
        if ($dir === null || $dir === 'down') {
          $streak++;
          $dir = 'down';
        } else {
          break;
        }
      } else {
        // día con PnL == 0 corta el streak
        break;
      }
    }

    if ($streak > 0 && ($dir === 'up' || $dir === 'down')) {
      $current_day_streak_value = (string) $streak;
      $current_day_streak_dir = $dir;
    } else {
      $current_day_streak_value = '—';
      $current_day_streak_dir = 'down';
    }
  }

  // === Card 6: streak de trades (últimos trades consecutivos) + totales W/L ===

  // totales W/L históricos que ya tenías correctos
  $current_w_total = (int) $wins_count;
  $current_l_total = (int) $loss_count;

  // streak por trades: contamos desde el trade más reciente hacia atrás
  $trade_streak_len = 0;
  $trade_streak_dir = null; // 'up' (WIN) | 'down' (LOSS)

  foreach ($records as $idx => $r) {
    $n = isset($r['net']) ? (float) $r['net'] : 0.0;

    if ($n > 0) {
      $side = 'WIN';
    } elseif ($n < 0) {
      $side = 'LOSS';
    } else {
      $side = 'FLAT';
    }

    // Si el primer trade es FLAT, streak = 0 y paramos
    if ($side === 'FLAT') {
      if ($idx === 0) {
        $trade_streak_len = 0;
        $trade_streak_dir = null;
      }
      break;
    }

    if ($trade_streak_dir === null) {
      // primer trade de la racha
      $trade_streak_dir = ($side === 'WIN') ? 'up' : 'down';
      $trade_streak_len = 1;
    } else {
      // si sigue el mismo lado, aumentamos streak
      if (
        ($trade_streak_dir === 'up' && $side === 'WIN') ||
        ($trade_streak_dir === 'down' && $side === 'LOSS')
      ) {
        $trade_streak_len++;
      } else {
        // cambió de lado (de win a loss o viceversa) -> fin de la racha
        break;
      }
    }
  }

  if ($trade_streak_len > 0 && $trade_streak_dir !== null) {
    $current_trades_value = (string) $trade_streak_len; // ej: "4"
    $current_trades_dir = $trade_streak_dir;          // 'up' o 'down'
  } else {
    $current_trades_value = '0';
    $current_trades_dir = 'down';
  }
}

/* ==== Helpers ==== */
function mt_money_compact($n)
{
  $s = $n < 0 ? '-' : '';
  $a = abs($n);
  if ($a >= 1000) {
    $k = $a / 1000;
    $num = floor($k) == $k
      ? number_format($k, 0)
      : rtrim(rtrim(number_format($k, 2, '.', ''), '0'), '.');

    return $s . '$' . $num . 'k';
  }
  $num = rtrim(rtrim(number_format($a, 2, '.', ''), '0'), '.');
  return $s . '$' . $num;
}

/* ==== Card 1 (solo REAL) ==== */
$net_pnl_source = $real_net_pnl;
$trades_count_src = $real_trades_ct;

$net_pnl_str = mt_money_compact($net_pnl_source);
$chart_series = $equity_series;
$chart_json = wp_json_encode($chart_series, JSON_UNESCAPED_SLASHES);

/* ==== Cards 2–4: métricas derivadas de los mismos trades (base) ==== */

// Totales para win% y promedios
$total_for_pct = $wins_count + $loss_count;

// Win %
$trade_win_pct = 0.0;
if ($total_for_pct > 0) {
  $trade_win_pct = ($wins_count / $total_for_pct) * 100.0;
}

// Avg win / loss / net (cálculo local, por si no hay metrics de API)
$avg_win = $wins_count > 0 ? ($gross_win / $wins_count) : 0.0;
$avg_loss = $loss_count > 0 ? ($gross_loss / $loss_count) : 0.0;
// net por trade (incluyendo 0 / breakeven si existen)
$avg_net = $real_trades_ct > 0 ? ($real_net_pnl / $real_trades_ct) : 0.0;

// Para la barrita de Card 2 usamos número de trades ganadores/perdedores
$trades_win = $wins_count;
$trades_loss = $loss_count;

// Profit factor: gross_win / gross_loss
$pf = 0.0;
if ($gross_loss > 0) {
  $pf = $gross_win / $gross_loss;
}

/* ==== Override con metrics del endpoint account_by_id (si existen) + TRANSIENT ==== */
$metrics_data = null;

if ($acc_id !== '' && function_exists('mt_accounts_resolve_account_by_id')) {
  $cache_key = 'mt_acc_metrics_' . md5($acc_id);
  $metrics_data = get_transient($cache_key);

  if ($metrics_data === false) {
    try {
      $full_acc = mt_accounts_resolve_account_by_id($acc_id);
      if (is_array($full_acc) && isset($full_acc['metrics']) && is_array($full_acc['metrics'])) {
        $metrics_data = $full_acc['metrics'];
        // TTL 60 segundos (ajusta si quieres)
        set_transient($cache_key, $metrics_data, 60);
      }
    } catch (\Throwable $e) {
      if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[MT][adg][metrics_by_id] ' . $e->getMessage());
      }
      $metrics_data = null;
    }
  }
}

if (is_array($metrics_data)) {
  if (isset($metrics_data['averageWin']) && is_numeric($metrics_data['averageWin'])) {
    $avg_win = (float) $metrics_data['averageWin'];
  }
  if (isset($metrics_data['averageLoss']) && is_numeric($metrics_data['averageLoss'])) {
    // viene negativa, para el UI mostramos valor absoluto
    $avg_loss = abs((float) $metrics_data['averageLoss']);
  }
  if (isset($metrics_data['expectancy']) && is_numeric($metrics_data['expectancy'])) {
    // valor grande del Card 2
    $avg_net = (float) $metrics_data['expectancy'];
  }
  if (isset($metrics_data['winRate']) && is_numeric($metrics_data['winRate'])) {
    // porcentaje principal del Card 3
    $trade_win_pct = (float) $metrics_data['winRate'];
  }
}

/* ==== Clases de iconos para Card 5 y 6 (flechas) ==== */
$streak_icon_classes = 'mt-icon ';
if ($current_day_streak_dir === 'up') {
  $streak_icon_classes .= 'mt-icon-success mt-icon_arrow-up-solid';
} else {
  $streak_icon_classes .= 'mt-icon-error mt-icon_arrow-down-solid';
}

$trades_icon_classes = 'mt-icon ';
if ($current_trades_dir === 'up') {
  $trades_icon_classes .= 'mt-icon-success mt-icon_arrow-up-solid';
} else {
  $trades_icon_classes .= 'mt-icon-error mt-icon_arrow-down-solid';
}
?>
<div class="mt-grid mt-grid--3x2" data-role="adg-root">
  <!-- ========================================================= -->
  <!-- CARD 1 — NET PNL + CHART -->
  <!-- ========================================================= -->
  <div class="mt-card mt-card-chart">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <div class="mt-card__title__journal">
          <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['netPLTitle']); ?>
        </div>

        <span class="mt-tooltip">
          <i class="mt-icon mt-icon-base mt-icon_info-solid"></i>
          <span class="mt-tooltip__panel" role="tooltip">
            <div class="mt-tooltip__title">
              <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['netPLTitle']); ?>
            </div>
            <div class="mt-tooltip__body">
              <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['netPLBody']); ?>
            </div>
          </span>
        </span>
      </div>

      <div data-shape="Pill" data-size="sm" data-type="onHold"
        class="align-items-center bg-a8a29e d-flex px-2 py-1 rounded-12">
        <div data-role="adg-trades-count" class="text-black text-14px leading-5 fw-bold">
          <?php echo (int) $trades_count_src; ?>
        </div>
      </div>
    </div>

    <div class="mt-card__kpi" data-role="adg-netpnl-kpi"
      style="margin-top:8px;color:var(--Text-Headings,#fff);font-size:20px;line-height:32px;">
      <?php echo esc_html($net_pnl_str); ?>
    </div>

    <div class="mt-card__chart" style="margin-top:16px;">
      <div data-js="adg-chart" style="height:80px;width:100%;pointer-events:none;border:none;outline:none;"
        data-series='<?php echo esc_attr($chart_json); ?>'></div>
    </div>
  </div>

  <!-- ========== CARD 2: Avg. win/loss trade (barra apilada) ========== -->
  <div class="mt-card">
    <div class="mt-card__wrapper">
      <div class="mt-card__header mt-card__header-col">
        <div class="mt-card__title">
          <div class="mt-card__title__journal">Avg. win/loss trade</div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Avg win/loss info"></i>
            <span class="mt-tooltip__panel" role="tooltip">
              <div class="mt-tooltip__title">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['avgWinLostTradeTitle']); ?>
              </div>
              <div class="mt-tooltip__body">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['avgWinLostTradeBody']); ?>
              </div>
            </span>
          </span>
        </div>
        <div class="mt-card__header-info">
          <?php echo '$' . number_format($avg_net, 2); ?>
        </div>
      </div>

      <div class="mt-card__body">
        <div class="d-flex gap-1">
          <div style="flex:1;">
            <div style="color:var(--Text-Success,#2DD4BF);font-weight:700;">
              <?php echo '$' . number_format($avg_win, 2); ?>
            </div>
          </div>
          <div style="flex:1;">
            <div style="text-align:right;color:var(--Error-500,#F43F5E);font-weight:700;">
              <?php echo '$' . number_format($avg_loss, 2); ?>
            </div>
          </div>
        </div>

        <?php
        // % basado en número de trades ganadores/perdedores
        $den = max(1, (int) $trades_win + (int) $trades_loss);
        $rewardPct = (int) round(((int) $trades_win / $den) * 100);
        $riskPct = 100 - $rewardPct;

        $dualbarMods = [];
        if ($rewardPct === 100) {
          $dualbarMods[] = 'mt-dualbar--reward-full';
        }
        if ($riskPct === 100) {
          $dualbarMods[] = 'mt-dualbar--risk-full';
        }

        $isEmptyBars = ($rewardPct === 0 && $riskPct === 0);
        $dualbarClass = 'mt-dualbar'
          . ($isEmptyBars ? ' is-empty' : '')
          . (!empty($dualbarMods) ? ' ' . implode(' ', $dualbarMods) : '');
        ?>

        <div class="mt-rr" style="margin-top:8px;">
          <div class="<?php echo esc_attr($dualbarClass); ?>" data-reward="<?php echo (int) $rewardPct; ?>"
            data-risk="<?php echo (int) $riskPct; ?>">
            <span class="mt-dualbar__seg mt-dualbar__seg--reward"></span>
            <span class="mt-dualbar__seg mt-dualbar__seg--risk"></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== CARD 3: Trade Win % ========== -->
  <?php
  $tt2 = max(1, (int) $wins_count + (int) $loss_count);
  $rewardPct2 = (int) round(((int) $wins_count / $tt2) * 100);
  $riskPct2 = 100 - $rewardPct2;

  $dualbarMods2 = [];
  if ($rewardPct2 === 100) {
    $dualbarMods2[] = 'mt-dualbar--reward-full';
  }
  if ($riskPct2 === 100) {
    $dualbarMods2[] = 'mt-dualbar--risk-full';
  }

  $isEmptyBars2 = ($rewardPct2 === 0 && $riskPct2 === 0);
  $dualbarClass2 = 'mt-dualbar'
    . ($isEmptyBars2 ? ' is-empty' : '')
    . (!empty($dualbarMods2) ? ' ' . implode(' ', $dualbarMods2) : '');
  ?>
  <div class="mt-card">
    <div class="mt-card__wrapper">
      <div class="mt-card__header mt-card__header-col">
        <div class="mt-card__title">
          <div class="mt-card__title__journal">
            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['tradeWinTitle']); ?>
          </div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Trade Win info"></i>
            <span class="mt-tooltip__panel" role="tooltip">
              <div class="mt-tooltip__title">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['tradeWinTitle']); ?>
              </div>
              <div class="mt-tooltip__body">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['tradeWinBody']); ?>
              </div>
            </span>
          </span>
        </div>
        <div class="mt-card__header-info">
          <?php echo number_format($trade_win_pct, 2); ?>%
        </div>
      </div>

      <div class="mt-card__body">
        <div class="d-flex gap-1" style="display:flex;gap:4px;align-items:flex-end;">
          <div style="flex:1;">
            <div style="color:var(--Text-Success,#2DD4BF);font-weight:700;">
              <?php echo (int) $wins_count; ?>
            </div>
          </div>
          <div style="flex:1;">
            <div style="text-align:right;color:var(--Error-500,#F43F5E);font-weight:700;">
              <?php echo (int) $loss_count; ?>
            </div>
          </div>
        </div>

        <div class="mt-rr" style="margin-top:8px;">
          <div class="<?php echo esc_attr($dualbarClass2); ?>" data-reward="<?php echo (int) $rewardPct2; ?>"
            data-risk="<?php echo (int) $riskPct2; ?>">
            <span class="mt-dualbar__seg mt-dualbar__seg--reward"></span>
            <span class="mt-dualbar__seg mt-dualbar__seg--risk"></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== CARD 4: Profit Factor ========== -->
  <?php
  $f = max(0.0, min(1.0, $pf / (1.0 + $pf)));
  $R = 24;
  $C = 2 * M_PI * $R;
  $dashRed = $C * (1 - $f);
  $offsetRed = $C * $f;
  $epsilon = 0.05;
  $maskId = 'pfmask_' . wp_rand(1000, 9999);
  $green = 'var(--Success-400,#2DD4BF)';
  $red = 'var(--Error-500,#F43F5E)';
  ?>
  <div class="mt-card">
    <div class="mt-card__wrapper">
      <div class="mt-card__header mt-card__header-col">
        <div class="mt-card__title">
          <div class="mt-card__title__journal">
            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['profitFactorTitle']); ?></div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Profit Factor info"></i>
            <span class="mt-tooltip__panel" role="tooltip">
              <div class="mt-tooltip__title">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['profitFactorTitle']); ?>
              </div>
              <div class="mt-tooltip__body">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['profitFactorBody']); ?>
              </div>
            </span>
          </span>
        </div>
        <div class="mt-card__header-info">
          <?php echo number_format($pf, 2); ?>
        </div>
      </div>

      <div class="mt-card__body">
        <div class="pf-wrap">
          <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none"
            shape-rendering="geometricPrecision">
            <path
              d="M60 30C60 46.5685 46.5685 60 30 60C13.4315 60 0 46.5685 0 30C0 13.4315 13.4315 0 30 0C46.5685 0 60 13.4315 60 30ZM6 30C6 43.2548 16.7452 54 30 54C43.2548 54 54 43.2548 54 30C54 16.7452 43.2548 6 30 6C16.7452 6 6 16.7452 6 30Z"
              fill="<?php echo esc_attr($green); ?>" />
            <mask id="<?php echo esc_attr($maskId); ?>" maskUnits="userSpaceOnUse" x="0" y="0" width="60" height="60">
              <path
                d="M60 30C60 46.5685 46.5685 60 30 60C13.4315 60 0 46.5685 0 30C0 13.4315 13.4315 0 30 0C46.5685 0 60 13.4315 60 30ZM6 30C6 43.2548 16.7452 54 30 54C43.2548 54 54 43.2548 54 30C54 16.7452 43.2548 6 30 6C16.7452 6 6 16.7452 6 30Z"
                fill="#fff" />
            </mask>
            <g mask="url(#<?php echo esc_attr($maskId); ?>">
              <circle cx="30" cy="30" r="<?php echo $R; ?>" fill="none" stroke="<?php echo esc_attr($red); ?>"
                stroke-width="12" stroke-linecap="butt" transform="rotate(-90 30 30)"
                stroke-dasharray="<?php echo ($dashRed + $epsilon) . ' ' . $C; ?>"
                stroke-dashoffset="<?php echo $offsetRed; ?>" />
            </g>
          </svg>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== CARD 5: Current Day Streak (por días) ========== -->
  <div class="mt-card">
    <div class="mt-card__wrapper">
      <div class="mt-card__header mt-card__header-col">
        <div class="mt-card__title">
          <div class="mt-card__title__journal">
            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['currentDayStreakTitle']); ?></div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Streak info"></i>
            <span class="mt-tooltip__panel" role="tooltip">
              <div class="mt-tooltip__title">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['currentDayStreakTitleTooltip']); ?>
              </div>
              <div class="mt-tooltip__body">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['currentDayStreakBody']); ?>
              </div>
            </span>
          </span>
        </div>
        <div class="mt-card__header-info d-flex align-items-center gap-2">
          <?php echo esc_html($current_day_streak_value); ?>
          <span class="<?php echo esc_attr($streak_icon_classes); ?>" tabindex="0" aria-label="Streak direction"></span>
        </div>
      </div>

      <div class="mt-card__body">
        <div class="d-flex gap-2">
          <div class="mt-badge mt-badge-xs mt-badge-calendar mt-badge-secondary-800">
            <?php echo (int) $current_day_streak_w; ?>W
          </div>
          <div class="mt-badge mt-badge-xs mt-badge-calendar mt-badge-error-900">
            <?php echo (int) $current_day_streak_l; ?>L
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========== CARD 6: Current Trades Streak (por trades) ========== -->
  <div class="mt-card">
    <div class="mt-card__wrapper">
      <div class="mt-card__header mt-card__header-col">
        <div class="mt-card__title">
          <div class="mt-card__title__journal">
            <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['currentDayTradesTitle']); ?></div>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Current trades info"></i>
            <span class="mt-tooltip__panel" role="tooltip">
              <div class="mt-tooltip__title">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['currentDayTradesTitleTolltip']); ?>
              </div>
              <div class="mt-tooltip__body">
                <?php echo esc_html(Label::META_ACCOUNT_OVERVIEW_TOOLTIP['currentDayTradesBody']); ?>
              </div>
            </span>
          </span>
        </div>
        <div class="mt-card__header-info d-flex align-items-center gap-2">
          <?php echo esc_html($current_trades_value); ?>
          <span class="<?php echo esc_attr($trades_icon_classes); ?>" tabindex="0"
            aria-label="Trades streak direction"></span>
        </div>
      </div>

      <div class="mt-card__body">
        <div class="d-flex gap-2">
          <div class="mt-badge mt-badge-xs mt-badge-calendar mt-badge-secondary-800">
            <?php echo (int) $current_w_total; ?>W
          </div>
          <div class="mt-badge mt-badge-xs mt-badge-calendar mt-badge-error-900">
            <?php echo (int) $current_l_total; ?>L
          </div>
        </div>
      </div>
    </div>
  </div>
</div>