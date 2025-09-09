<?php
/**
 * Template Part: Account Performance (datos)
 * Ruta: template-parts/account/account-performance.php
 *
 * Espera recibir un array en $args['performance'] (WP >= 5.5)
 * o, en WP antiguos, vía set_query_var('mt_performance', $array).
 */

defined('ABSPATH') || exit;

// Recibe args de forma compatible
$performance = [];
if ( isset($args) && is_array($args) && isset($args['performance']) ) {
  $performance = $args['performance'];
} else {
  $maybe = get_query_var('mt_performance');
  if ( ! empty($maybe) && is_array($maybe) ) {
    $performance = $maybe;
  }
}

if ( empty($performance) ) {
  // Nada que mostrar aún (no maquetamos aquí)
  return;
}

// Normaliza claves (con default) para evitar notices
$defaults = [
  'currentBalance'           => null,
  'currentEquity'            => null,
  'currentProfit'            => null,
  'currentProfitPercent'     => null,
  'activeTradingDays'        => null,
  'dailyTotalPnL'            => null,
  'minTradingDays'           => null,
  'maxLossLimitEquityLevel'  => null,
  'target'                   => null,
];
$performance = array_merge($defaults, $performance);

// >>> A partir de aquí ya puedes maquetar libremente.
// Por ahora, solo dejo un contenedor con los valores disponibles.

?>
<div class="mt-account-performance" data-component="account-performance">
  <!-- TODO: reemplazar por tu HTML real -->
  <pre style="display:none;"><?php echo esc_html( wp_json_encode( $performance ) ); ?></pre>

  <?php /* Ejemplo de variables listas para usar:
  $balance      = $performance['currentBalance'];
  $equity       = $performance['currentEquity'];
  $profit       = $performance['currentProfit'];
  $profitPct    = $performance['currentProfitPercent'];
  $days         = $performance['activeTradingDays'];
  $dailyPnL     = $performance['dailyTotalPnL'];
  $minDays      = $performance['minTradingDays'];
  $maxLossEq    = $performance['maxLossLimitEquityLevel'];
  $target       = $performance['target'];
  */ ?>
</div>
