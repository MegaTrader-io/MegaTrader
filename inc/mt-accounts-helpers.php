<?php
// File: public_html/wp-content/themes/megatrader-addons/inc/mt-accounts-helpers.php
defined('ABSPATH') || exit;

class MT_Accounts
{
  public static function is_active_status($status): bool
  {
    $s = strtolower(trim((string) $status));
    return in_array($s, ['active', 'approved', 'open', 'enabled', 'running', 'live', 'activated'], true);
  }
  public static function badge_class(string $status): string
  {
    $s = strtolower(trim($status));
    if (in_array($s, ['active', 'approved', 'open', 'enabled', 'running', 'live', 'activated'], true))
      return 'badge-mega-active';
    if (in_array($s, ['pending', 'pending-cancel', 'paused', 'submitted'], true))
      return 'badge-mega-pending';
    if (in_array($s, ['rejected', 'closed', 'disabled'], true))
      return 'badge-mega-danger';
    return 'badge-mega-default';
  }
  public static function dot_class(string $status): string
  {
    $s = strtolower(trim($status));
    if (in_array($s, ['active', 'approved', 'open', 'enabled', 'running', 'live', 'activated'], true))
      return 'dot-status-active';
    if (in_array($s, ['pending', 'pending-cancel', 'paused', 'submitted', 'on-hold', 'refunded'], true))
      return 'dot-status-pending';
    if (in_array($s, ['rejected', 'closed', 'disabled', 'cancelled', 'failed', 'expired'], true))
      return 'dot-status-danger';
    return 'dot-status-default';
  }
  private static function norm($s): string
  {
    return preg_replace('/[^a-z0-9]+/', '', strtolower((string) $s));
  }
  public static function parse_program_label(string $label, $startingBalance = null): array
  {
    $head = $label;
    $pos = strpos($label, '|');
    if ($pos !== false)
      $head = substr($label, 0, $pos);
    $head = trim($head);
    $size = '';
    $name = $head;
    if (preg_match('/^\s*([0-9]+k)\b/i', $head, $m)) {
      $size = strtoupper($m[1]);
      $name = trim(substr($head, strlen($m[1])));
    } elseif (is_numeric($startingBalance)) {
      $k = round(((int) $startingBalance) / 1000);
      if ($k > 0)
        $size = strtoupper($k . 'k');
    }
    if ($name === '')
      $name = 'Account';
    return [$size, $name];
  }
  public static function extract_platform(array $acc): string
  {
    $p = trim((string) ($acc['program']['platform'] ?? ''));
    if ($p !== '')
      return $p;
    $label = (string) ($acc['program']['label'] ?? ($acc['program']['description'] ?? ''));
    $parts = array_map('trim', explode('|', $label));
    return $parts[1] ?? '';
  }
  public static function prepare_ui(array $accounts): array
  {
    // ===== Config logos (mantener/ajustar según tu código actual) =====
    $DEFAULT_LOGO = 'https://subscriptions.megatrader.io/wp-content/uploads/2025/07/Stylecolor-Sizelg.svg';
    $PLATFORM_LOGOS = [
      self::norm('MegaTrader') => $DEFAULT_LOGO,
      self::norm('NinjaTrader') => 'https://subscriptions.megatrader.io/wp-content/uploads/2025/02/icon_ninjatrader.svg',
      self::norm('Tradovate') => 'https://subscriptions.megatrader.io/wp-content/uploads/2025/02/icon_tradovate.svg',
      self::norm('Quantower') => 'https://subscriptions.megatrader.io/wp-content/uploads/2025/02/icon_quantower.svg',
    ];

    // ===== 1) USAR TODAS LAS CUENTAS (sin filtrar por estado) =====
    $pool = array_values(array_filter((array) $accounts, fn($a) => is_array($a)));

    // Orden por fecha desc (más nuevas primero)
    usort($pool, function ($a, $b) {
      $ta = strtotime((string) ($a['createdAt'] ?? '')) ?: 0;
      $tb = strtotime((string) ($b['createdAt'] ?? '')) ?: 0;
      return $tb <=> $ta;
    });

    if (empty($pool)) {
      return ['current' => null, 'accounts' => []];
    }

    // ===== 2) Elegir "current": prioriza Active más reciente; si no hay, la más reciente general =====
    $cur = $pool[0];
    foreach ($pool as $row) {
      if (self::is_active_status($row['status'] ?? '')) {
        $cur = $row;
        break;
      }
    }

    // ===== 3) Helpers de mapeo (NO tocar la firma de tus métodos) =====
    $mapAccount = function (array $acc) use ($PLATFORM_LOGOS, $DEFAULT_LOGO) {
      $plabel = (string) ($acc['program']['label'] ?? ($acc['program']['description'] ?? 'Account'));
      $sb = $acc['program']['startingBalance'] ?? null;
      [$size, $name] = self::parse_program_label($plabel, $sb);

      $platformRaw = (string) ($acc['platform'] ?? ($acc['program']['platform'] ?? ''));
      $platformKey = self::norm($platformRaw);
      $logo = $PLATFORM_LOGOS[$platformKey] ?? $DEFAULT_LOGO;

      return [
        'id' => (string) ($acc['id'] ?? ''),
        'status' => (string) ($acc['status'] ?? ''),
        'badgeClass' => self::badge_class($acc['status'] ?? ''),
        'size' => $size,
        'name' => $name,
        'platform' => $platformRaw,
        'logo' => $logo,
        'createdAt' => (string) ($acc['createdAt'] ?? ''),
      ];
    };

    // ===== 4) Construir payload UI (conservar estructura) =====
    $current = $mapAccount($cur);
    $modal = array_map($mapAccount, $pool);

    return [
      'current' => $current,
      'accounts' => $modal,
    ];
  }

}

if (!function_exists('mt_accounts_find_active_account')) {
  function mt_accounts_find_active_account($accounts)
  {
    if (empty($accounts))
      return null;
    if (is_array($accounts) && isset($accounts['data']) && is_array($accounts['data'])) {
      $list = $accounts['data'];
    } else {
      $list = is_array($accounts) ? $accounts : [];
    }
    foreach ($list as $acc) {
      $status = $acc['status'] ?? $acc['accountStatus'] ?? null;
      if (is_string($status) && strtolower($status) === 'active') {
        return $acc;
      }
    }
    return null;
  }
}

if (!function_exists('mt__get')) {
  function mt__get($arr, array $path, $default = null)
  {
    $ref = $arr;
    foreach ($path as $key) {
      if (is_array($ref) && array_key_exists($key, $ref)) {
        $ref = $ref[$key];
      } else {
        return $default;
      }
    }
    return $ref;
  }
}

if (!function_exists('mt_accounts_build_performance')) {
  function mt_accounts_build_performance(array $account)
  {
    $metrics = $account['metrics'] ?? $account['metric'] ?? [];
    $program = $account['program'] ?? null;
    if (!$program && !empty($account['programs']) && is_array($account['programs'])) {
      $program = null;
      foreach ($account['programs'] as $p) {
        $pStatus = $p['status'] ?? $p['state'] ?? null;
        if (is_string($pStatus) && strtolower($pStatus) === 'active') {
          $program = $p;
          break;
        }
      }
      if (!$program)
        $program = $account['programs'][0] ?? null;
    }
    $payload = [
      'currentBalance' => $metrics['currentBalance'] ?? mt__get($metrics, ['balance']),
      'currentEquity' => $metrics['currentEquity'] ?? null,
      'currentProfit' => $metrics['currentProfit'] ?? $metrics['profit'] ?? null,
      'currentProfitPercent' => $metrics['currentProfitPercent'] ?? $metrics['profitPercent'] ?? null,
      'activeTradingDays' => $metrics['activeTradingDays'] ?? $metrics['tradingDays'] ?? null,
      'dailyTotalPnL' => $metrics['dailyTotalPnL'] ?? $metrics['dailyPnL'] ?? null,
      'minTradingDays' => $metrics['minTradingDays'] ?? null,
      'maxLossLimitEquityLevel' => $metrics['maxLossLimitEquityLevel'] ?? $metrics['maxLossLimit'] ?? null,
      'target' => $program['target'] ?? $program['profitTarget'] ?? mt__get($metrics, ['target']),
      'maxDailyLossLimitPnLLevel' => $metrics['maxDailyLossLimitPnLLevel'] ?? null,

    ];
    foreach ($payload as $k => $v) {
      if (is_string($v) && is_numeric($v))
        $payload[$k] = $v + 0;
    }
    return $payload;
  }
}

if (!function_exists('mt_accounts_prepare_performance_from_accounts')) {
  function mt_accounts_prepare_performance_from_accounts($accounts)
  {
    $active = mt_accounts_find_active_account($accounts);
    if (!$active)
      return [];
    return mt_accounts_build_performance($active);
  }
}


// Build desde UNA cuenta (metrics|metric) → payload para feature-content
if (!function_exists('mt_accounts_build_feature_content')) {
  function mt_accounts_build_feature_content(array $account): array
  {
    $m = $account['metrics'] ?? $account['metric'] ?? [];

    // Crudos (API: win/loss en 0..100)
    $avgWin = $m['averageWin'] ?? 0;
    $avgLoss = $m['averageLoss'] ?? 0;
    $winRate = $m['winRate'] ?? 0; // 66.67
    $lossRate = $m['lossRate'] ?? 0; // 33.33

    // Cast numérico
    $avgWin = is_numeric($avgWin) ? (float) $avgWin : 0.0;
    $avgLoss = is_numeric($avgLoss) ? (float) $avgLoss : 0.0;
    $winRate = is_numeric($winRate) ? (float) $winRate : 0.0;
    $lossRate = is_numeric($lossRate) ? (float) $lossRate : 0.0;

    // Normalizar a fracción 0..1 (para la UI)
    if ($winRate > 1)
      $winRate /= 100;
    if ($lossRate > 1)
      $lossRate /= 100;
    $winRate = max(0.0, min(1.0, $winRate));
    $lossRate = max(0.0, min(1.0, $lossRate));

    $payload = [
      'overview' => [
        'averageWin' => $avgWin,
        'averageLoss' => $avgLoss,
        'winRate' => $winRate,   // 0..1
        'lossRate' => $lossRate,  // 0..1
      ],
    ];

    // Cast finales por si algo vino string
    foreach ($payload['overview'] as $k => $v) {
      if (is_string($v) && is_numeric($v))
        $payload['overview'][$k] = $v + 0;
    }
    return $payload;
  }
}

// Build desde LISTA de cuentas → elige la activa y arma payload (igual que performance)
if (!function_exists('mt_accounts_prepare_feature_from_accounts')) {
  function mt_accounts_prepare_feature_from_accounts($accounts): array
  {
    $active = function_exists('mt_accounts_find_active_account')
      ? mt_accounts_find_active_account($accounts)
      : null;
    if (!$active)
      return [];
    return mt_accounts_build_feature_content($active);
  }
}


if (!function_exists('mt_accounts_fetch_account_json_by_shortcode')) {
  function mt_accounts_fetch_account_json_by_shortcode($accountId, $page = 1, $perPage = 10)
  {
    $accountId = trim((string) $accountId);
    if ($accountId === '')
      return null;
    $shortcode = sprintf(
      '[mega_account_data id="%s" page="%d" perpage="%d" output="json"]',
      esc_attr($accountId),
      (int) $page,
      (int) $perPage
    );
    $raw = do_shortcode($shortcode);
    if (!is_string($raw) || $raw === '')
      return null;
    $raw = trim(wp_strip_all_tags($raw));
    $data = json_decode($raw, true);
    return is_array($data) ? $data : null;
  }
}

if (!function_exists('mt_accounts_pick_account_from_json')) {
  function mt_accounts_pick_account_from_json($json, $accountId)
  {
    if (empty($json))
      return null;
    $accountId = (string) $accountId;
    if (isset($json['data']) && is_array($json['data'])) {
      foreach ($json['data'] as $row) {
        if ((string) ($row['id'] ?? '') === $accountId)
          return $row;
      }
      if (count($json['data']) === 1)
        return $json['data'][0];
    }
    if (isset($json[0]) && is_array($json[0])) {
      foreach ($json as $row) {
        if ((string) ($row['id'] ?? '') === $accountId)
          return $row;
      }
      if (count($json) === 1)
        return $json[0];
    }
    if (isset($json['id']) && (string) $json['id'] === $accountId) {
      return $json;
    }
    return $json['data'][0] ?? ($json[0] ?? null);
  }
}

if (!function_exists('mt_accounts_ajax_performance')) {
  add_action('wp_ajax_mt_accounts_performance', 'mt_accounts_ajax_performance');
  add_action('wp_ajax_nopriv_mt_accounts_performance', 'mt_accounts_ajax_performance');
  function mt_accounts_ajax_performance()
  {
    check_ajax_referer('mt-acc-nonce', 'nonce');
    $accountId = isset($_POST['accountId']) ? sanitize_text_field((string) $_POST['accountId']) : '';
    if ($accountId === '') {
      wp_send_json_error(['message' => 'Missing accountId']);
    }
    if (
      !function_exists('mt_accounts_fetch_account_json_by_shortcode') ||
      !function_exists('mt_accounts_pick_account_from_json') ||
      !function_exists('mt_accounts_build_performance')
    ) {
      wp_send_json_error(['message' => 'Helpers not available']);
    }
    $json = mt_accounts_fetch_account_json_by_shortcode($accountId, 1, 10);
    $account = mt_accounts_pick_account_from_json($json, $accountId);
    if (!$account) {
      wp_send_json_error(['message' => 'Account not found']);
    }
    $perf = mt_accounts_build_performance($account);
    ob_start();
    get_template_part('template-parts/account/account-performance', null, [
      'performance' => $perf,
      'meta' => ['accountId' => $accountId],
    ]);
    $html = ob_get_clean();
    wp_send_json_success([
      'html' => $html,
      'performance' => $perf,
      'accountId' => $accountId,
    ]);
  }
}

if (!function_exists('mt_accounts_resolve_account_by_id')) {
  function mt_accounts_resolve_account_by_id(string $accountId)
  {
    $acc = null;

    // 1) API directa
    if (class_exists('MT_Api') && method_exists('MT_Api', 'fetch_account_by_id')) {
      try {
        $acc = MT_Api::fetch_account_by_id($accountId);
      } catch (Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG)
          error_log('[MT][acc_resolve][by_id] ' . $e->getMessage());
      }
    }

    // 2) Fallback: shortcode + pick
    if (!$acc && function_exists('mt_accounts_fetch_account_json_by_shortcode')) {
      try {
        $json = mt_accounts_fetch_account_json_by_shortcode($accountId, 1, 1);
        if (function_exists('mt_accounts_pick_account_from_json')) {
          $acc = mt_accounts_pick_account_from_json($json, $accountId);
        }
      } catch (Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG)
          error_log('[MT][acc_resolve][shortcode] ' . $e->getMessage());
      }
    }

    return (is_array($acc) && !empty($acc)) ? $acc : null;
  }
}


// ===== MONEY / PERCENT HELPERS =====
if (!function_exists('mt_money_symbol')) {
  /**
   * Permite sobreescribir el símbolo vía filtro WP si algún día lo necesitas.
   * apply_filters('mt_money_currency_symbol', '$', $context)
   */
  function mt_money_symbol($fallback = '$', $context = null)
  {
    return apply_filters('mt_money_currency_symbol', $fallback, $context);
  }
}

if (!function_exists('mt_format_money')) {
  /**
   * $47,850.30   ó   -$1,234.56
   */
  function mt_format_money($value, $currency = null, $context = null)
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return '—';
    if ($currency === null)
      $currency = mt_money_symbol('$', $context);
    $num = (float) $value;
    $neg = $num < 0;
    $abs = abs($num);
    $formatted = number_format($abs, 2, '.', ',');
    return ($neg ? '-' : '') . $currency . $formatted;
  }
}

if (!function_exists('mt_format_money_no_cents')) {
  /**
   * $47,850   ó   -$1,235
   * Redondea al entero más cercano y no muestra centavos.
   */
  function mt_format_money_no_cents($value, $currency = null, $context = null)
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return '—';
    if ($currency === null)
      $currency = mt_money_symbol('$', $context);

    $num = (float) $value;
    $neg = $num < 0;
    $abs = abs($num);

    // Redondeo al entero más cercano
    $rounded = round($abs, 0);
    $formatted = number_format($rounded, 0, '.', ',');

    return ($neg ? '-' : '') . $currency . $formatted;
  }
}


if (!function_exists('mt_format_signed_money')) {
  /**
   * +$47,850.30 si > 0
   * -$1,234.56 si < 0
   * $0.00      si == 0 (sin signo)
   */
  function mt_format_signed_money($value, $currency = null, $context = null)
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return '—';
    if ($currency === null)
      $currency = mt_money_symbol('$', $context);

    $num = (float) $value;
    $abs = abs($num);

    // Si quieres tratar -0.00 como 0:
    if (abs($num) < 1e-9)
      $num = 0.0;

    $formatted = number_format($abs, 2, '.', ',');

    // Signo: + si >0, - si <0, vacío si ==0
    $sign = ($num > 0) ? '+' : (($num < 0) ? '-' : '');

    return $sign . $currency . $formatted;
  }
}


if (!function_exists('mt_format_percent')) {
  /**
   * 4.30%  ó  +4.30% / -4.30% si $signed = true
   */
  function mt_format_percent($value, $signed = false)
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return '—';
    $num = (float) $value;
    $abs = abs($num);
    $formatted = number_format($abs, 2, '.', ',') . '%';
    if (!$signed)
      return $formatted;
    $sign = $num >= 0 ? '+' : '-';
    return $sign . $formatted;
  }
}

// Color para cualquier valor numérico: neg -> text-error, pos -> text-success, cero/NaN -> text-white
if (!function_exists('mt_value_color_class')) {
  function mt_value_color_class($value, $zero = 'text-white', $pos = 'text-success', $neg = 'text-error')
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return $zero;
    $n = (float) $value;
    return ($n > 0) ? $pos : (($n < 0) ? $neg : $zero);
  }
}

// ========= Percent UI helpers =========
if (!function_exists('mt_percent_badge_class')) {
  function mt_percent_badge_class($value)
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return 'mt-badge-light';
    $n = (float) $value;
    return $n > 0 ? 'mt-badge-secondary' : ($n < 0 ? 'mt-badge-error' : 'mt-badge-light');
  }
}

if (!function_exists('mt_percent_icon_class')) {
  function mt_percent_icon_class($value)
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return '';
    $n = (float) $value;
    return $n > 0 ? 'mt-icon_arrow-up mt-icon-success'
      : ($n < 0 ? 'mt-icon_arrow-down mt-icon-error' : '');
  }
}

if (!function_exists('mt_percent_text')) {
  // Devuelve "—" si no hay dato; con signo +/– cuando procede
  function mt_percent_text($value)
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return '—';
    $n = (float) $value;
    // 0 sin signo
    return $n == 0.0 ? mt_format_percent(0, false) : mt_format_percent($n, true);
  }
}

if (!function_exists('mt_profit_ui_from_percent')) {
  /**
   * Paquete para pintar % de profit en UI.
   * Return: ['badgeClass' => ..., 'iconClass' => ..., 'text' => ...]
   */
  function mt_profit_ui_from_percent($value)
  {
    return [
      'badgeClass' => mt_percent_badge_class($value),
      'iconClass' => mt_percent_icon_class($value),
      'text' => mt_percent_text($value),
    ];
  }
}
// Icono por signo de valor: pos -> success, neg -> error, cero/NaN -> sin icono
if (!function_exists('mt_value_icon_classes')) {
  function mt_value_icon_classes($value, $opts = [])
  {
    // permite personalizar clases
    $pos = $opts['pos'] ?? 'mt-icon-success mt-icon_checkmark-solid';
    $neg = $opts['neg'] ?? 'mt-icon-error mt-icon_cancel';
    $zero = $opts['zero'] ?? '';      // cero = no mostrar
    $neutral = $opts['neutral'] ?? 'mt-icon-gray mt-icon_checkmark-solid'; // no numérico = no mostrar

    if ($value === null || $value === '' || !is_numeric($value))
      return $neutral;
    $n = (float) $value;
    if (abs($n) < 1e-9)
      return $zero;
    return $n > 0 ? $pos : $neg;
  }
}
// Icono por comparación (value vs target) – SIEMPRE devuelve un icono:
// - value < 0                 => error (cancel)
// - value == 0                => neutral (check gris)
// - value > 0 && target <= 0  => success (tratamos meta no válida como cumplida)
// - value > 0 && value > tgt  => success (check verde)
// - value > 0 && value <= tgt => neutral (check gris)
// - value no numérico         => neutral
if (!function_exists('mt_value_compare_icon_classes')) {
  function mt_value_compare_icon_classes($value, $target, $opts = [])
  {
    $cls_pos = $opts['pos'] ?? 'mt-icon-success mt-icon_checkmark-solid';
    $cls_neg = $opts['neg'] ?? 'mt-icon-error mt-icon_cancel';
    $cls_neutral = $opts['neutral'] ?? 'mt-icon-gray mt-icon_checkmark-solid';

    if ($value === null || $value === '' || !is_numeric($value)) {
      return $cls_neutral;
    }
    $v = (float) $value;

    if ($v < 0)
      return $cls_neg;
    if (abs($v) < 1e-9)
      return $cls_neutral; // 0 => neutral

    // positivo:
    if ($target === null || $target === '' || !is_numeric($target))
      return $cls_neutral;
    $t = (float) $target;

    if ($t <= 0)
      return $cls_pos;           // meta no válida => consideramos cumplida
    return ($v > $t) ? $cls_pos : $cls_neutral;
  }
}

// Mapea links por plataforma
function mt_accounts_default_platform_links($code)
{
  $code = strtolower((string) $code);
  $map = [
    'ctrader' => [
      'web' => 'https://app.spotware.com',
      'appstore' => 'https://apps.apple.com/app/ctrader/id767428811',
      'playstore' => 'https://play.google.com/store/apps/details?id=com.spotware.ct',
      'icon_class' => 'mt-icon-ctrader',
      'name' => 'cTrader',
    ],
    'mt4' => [
      'web' => '',
      'appstore' => 'https://apps.apple.com/app/metatrader-4/id496212596',
      'playstore' => 'https://play.google.com/store/apps/details?id=net.metaquotes.metatrader4',
      'icon_class' => 'mt-icon-mt4',
      'name' => 'MetaTrader 4',
    ],
    'mt5' => [
      'web' => '',
      'appstore' => 'https://apps.apple.com/app/metatrader-5/id413251709',
      'playstore' => 'https://play.google.com/store/apps/details?id=net.metaquotes.metatrader5',
      'icon_class' => 'mt-icon-mt5',
      'name' => 'MetaTrader 5',
    ],
  ];
  $base = ['web' => '', 'appstore' => '', 'playstore' => '', 'icon_class' => '', 'name' => 'Trading Platform'];
  return $map[$code] ?? $base;
}

// Construye credenciales desde un objeto/array "cuenta" (JSON/API)
function mt_accounts_build_credentials_from_account($account)
{
  $a = is_object($account) ? json_decode(json_encode($account), true) : (array) $account;

  $login = $a['login'] ?? ($a['credentials']['login'] ?? ($a['accountNumber'] ?? ($a['tradingLogin'] ?? '')));
  $server = $a['server'] ?? ($a['credentials']['server'] ?? '');
  $pwd = $a['password'] ?? ($a['credentials']['password'] ?? '');

  $platform_code = strtolower($a['platform']['code'] ?? ($a['platform_code'] ?? ''));
  $platform_name = $a['platform']['name'] ?? ($a['platform_name'] ?? '');
  $links = [
    'web' => $a['links']['web'] ?? '',
    'appstore' => $a['links']['appstore'] ?? '',
    'playstore' => $a['links']['playstore'] ?? '',
  ];

  // Defaults por plataforma
  if (!$links['web'] && !$links['appstore'] && !$links['playstore']) {
    $links = array_intersect_key(mt_accounts_default_platform_links($platform_code), $links + ['x' => 1]);
  }

  $platform_defaults = mt_accounts_default_platform_links($platform_code);
  return [
    'login' => (string) $login,
    'password' => (string) $pwd,
    'server' => (string) $server,
    'links' => $links,
    'platform' => [
      'code' => $platform_code,
      'name' => $platform_name ?: $platform_defaults['name'],
      'icon_class' => $platform_defaults['icon_class'],
    ],
  ];
}

// Fallback a post_meta si no vino nada por JSON/API
function mt_accounts_build_credentials_from_meta($account_id)
{
  return [
    'login' => get_post_meta($account_id, 'mt_login', true),
    'password' => get_post_meta($account_id, 'mt_password', true),
    'server' => get_post_meta($account_id, 'mt_server', true),
    'links' => [
      'web' => get_post_meta($account_id, 'mt_link_web', true),
      'appstore' => get_post_meta($account_id, 'mt_link_appstore', true),
      'playstore' => get_post_meta($account_id, 'mt_link_playstore', true),
    ],
    'platform' => [
      'code' => strtolower(get_post_meta($account_id, 'mt_platform_code', true)),
      'name' => get_post_meta($account_id, 'mt_platform_name', true),
      'icon_class' => '',
    ],
  ];
}

// PUBLIC: obtiene credenciales por ID unificando fuentes
// === Credenciales por accountId (usa el mismo fetch del shortcode que performance) ===
function mt_accounts_get_credentials($account_id)
{
  // 1) Sanitizar como STRING (Mongo ObjectId de 24 hex)
  $account_id = sanitize_text_field($account_id ?? '');
  if (!preg_match('/^[a-f0-9]{24}$/i', $account_id)) {
    // Retorno seguro si el id no es válido
    return [
      'login' => '',
      'password' => '',
      'server' => '',
      'links' => ['web' => '', 'appstore' => '', 'playstore' => ''],
      'platform' => ['code' => '', 'name' => 'Trading Platform', 'icon_class' => ''],
    ];
  }

  // 2) Consultar la API usando TU helper del shortcode (mismo flujo que performance)
  //    IMPORTANT: pasar los "atts" como array, NO el id suelto.
  $atts = [
    'id' => $account_id,
    'page' => 1,
    'perpage' => 10,
    'output' => 'json',
  ];
  // Asegúrate que la firma de esta función acepte $atts = []
  $json = mt_accounts_fetch_account_json_by_shortcode($atts);

  // 3) Elegir la cuenta pedida y construir credenciales
  $acc = function_exists('mt_accounts_pick_account_from_json')
    ? mt_accounts_pick_account_from_json($json, $account_id)
    : null;

  $creds = mt_accounts_build_credentials_from_account($acc);

  // 4) Defaults + retorno con shape estable
  $defaults = [
    'login' => '',
    'password' => '',
    'server' => '',
    'links' => ['web' => '', 'appstore' => '', 'playstore' => ''],
    'platform' => ['code' => '', 'name' => 'Trading Platform', 'icon_class' => ''],
  ];
  return array_replace_recursive($defaults, is_array($creds) ? $creds : []);
}


// AJAX: devuelve el HTML del template account-data
// === AJAX: devuelve el HTML de template-parts/account/account-data por accountId ===
function mt_accounts_ajax_account_data()
{
  check_ajax_referer('mt-acc-nonce', 'nonce');

  $account_id = isset($_POST['account_id']) ? sanitize_text_field($_POST['account_id']) : '';

  ob_start();
  get_template_part('template-parts/account/account-data', null, [
    'meta' => ['accountId' => $account_id],
  ]);
  $html = ob_get_clean();

  wp_send_json_success(['html' => $html]);
}

// === Construye credenciales desde el JSON de una cuenta ===
if (!function_exists('mt_accounts_build_credentials_from_account')) {
  function mt_accounts_build_credentials_from_account($account)
  {
    if (empty($account)) {
      return [
        'login' => '',
        'password' => '',
        'server' => '',
        'links' => ['web' => '', 'appstore' => '', 'playstore' => ''],
        'platform' => ['code' => '', 'name' => 'Trading Platform', 'icon_class' => ''],
      ];
    }
    $a = is_object($account) ? json_decode(json_encode($account), true) : (array) $account;

    $login = $a['login'] ?? ($a['credentials']['login'] ?? ($a['accountNumber'] ?? ($a['tradingLogin'] ?? '')));
    $password = $a['password'] ?? ($a['credentials']['password'] ?? '');
    $server = $a['server'] ?? ($a['credentials']['server'] ?? '');

    $platform_code = strtolower($a['platform']['code'] ?? ($a['platform_code'] ?? ''));
    $platform_name = $a['platform']['name'] ?? ($a['platform_name'] ?? 'Trading Platform');

    $links = [
      'web' => $a['links']['web'] ?? '',
      'appstore' => $a['links']['appstore'] ?? '',
      'playstore' => $a['links']['playstore'] ?? '',
    ];

    return [
      'login' => (string) $login,
      'password' => (string) $password,
      'server' => (string) $server,
      'links' => $links,
      'platform' => [
        'code' => $platform_code,
        'name' => $platform_name,
        'icon_class' => '',
      ],
    ];
  }
}


// ================= Email: normalizar, validar y preparar para API ================

if (!function_exists('mt_normalize_email')) {
  /**
   * Trim + lowercase y normaliza espacios raros.
   */
  function mt_normalize_email(?string $email): string
  {
    if (!is_string($email))
      return '';
    // Quita espacios invisibles/UTF y normaliza
    $email = trim(preg_replace('/\s+/u', '', $email));
    return mb_strtolower($email, 'UTF-8');
  }
}

if (!function_exists('mt_validate_email')) {
  /**
   * Valida formato de email estándar.
   */
  function mt_validate_email(string $email): bool
  {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
  }
}

if (!function_exists('mt_email_for_api')) {
  /**
   * Prepara el email para enviarlo en consultas GET de la API.
   * - Mantiene el dominio intacto (no codifica '@' ni nada después).
   * - Solo codifica en el local-part los caracteres: ? / # & = + ( ) , : y espacio.
   * - No hace doble encoding.
   */
  function mt_email_for_api(string $email): string
  {
    $parts = explode('@', $email, 2);
    if (count($parts) !== 2)
      return '';

    [$local, $domain] = $parts;

    // Mapa de reemplazo SOLO para el local-part
    $replacements = [
      ' ' => '%20',
      '?' => '%3F',
      '/' => '%2F',
      '#' => '%23',
      '&' => '%26',
      '=' => '%3D',
      '+' => '%2B',
      '(' => '%28',
      ')' => '%29',
      ',' => '%2C',
      ':' => '%3A',
    ];

    // Reemplaza solo si existen esos símbolos (evita doble encode)
    $needsEncoding = strpbrk($local, " ?/#&=+(),:") !== false;
    if ($needsEncoding) {
      $local = strtr($local, $replacements);
    }

    return $local . '@' . $domain;
  }
}


if (!function_exists('mt_sanitize_email')) {
  /**
   * Flujo completo: normaliza, valida y prepara para API.
   * Retorna array con 'ok', 'email' (normalizado), 'api' (codificado) y 'error' (si aplica).
   */
  function mt_sanitize_email(?string $raw): array
  {
    $normalized = mt_normalize_email($raw);

    if ($normalized === '') {
      return ['ok' => false, 'email' => '', 'api' => '', 'error' => 'Email vacío.'];
    }

    if (!mt_validate_email($normalized)) {
      return ['ok' => false, 'email' => $normalized, 'api' => '', 'error' => 'Email inválido.'];
    }

    // Opcional: limitar a ASCII básico si tu API lo requiere estrictamente
    // $ascii = idn_to_ascii($normalized, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) ?: $normalized;

    $apiSafe = mt_email_for_api($normalized);

    return ['ok' => true, 'email' => $normalized, 'api' => $apiSafe, 'error' => ''];
  }
}



add_action('wp_ajax_mt_accounts_data', 'mt_accounts_ajax_account_data');
add_action('wp_ajax_nopriv_mt_accounts_data', 'mt_accounts_ajax_account_data');

// === Performance Chart Payload ===

if (!function_exists('mt_accounts_build_performance_chart')) {
  function mt_accounts_build_performance_chart(array $account): array
  {
    $m = $account['metrics'] ?? $account['metric'] ?? [];

    $upper_bound = is_numeric($m['equityPassLevel'] ?? null) ? (float) $m['equityPassLevel'] : null;
    $lower_bound = is_numeric($m['maxLossLimitEquityLevel'] ?? null) ? (float) $m['maxLossLimitEquityLevel'] : null;

    // === Serie diaria de currentBalance (línea amarilla) ===
    $candidates = [
      $m['dailyBalances'] ?? null, // [{date, currentBalance}]
      $m['balanceDaily'] ?? null,
      $m['balanceSeries'] ?? null,
      $account['balances'] ?? null,
      $account['history']['dailyBalance'] ?? null,
    ];
    $series = [];
    foreach ($candidates as $cand) {
      if (!is_array($cand) || empty($cand))
        continue;
      foreach ($cand as $row) {
        $date = (string) ($row['date'] ?? $row['day'] ?? $row['d'] ?? '');
        $balRaw = $row['currentBalance'] ?? $row['balance'] ?? $row['y'] ?? null;
        if (!$date || !is_numeric($balRaw))
          continue;
        $series[] = ['date' => substr($date, 0, 10), 'value' => (float) $balRaw];
      }
      if (!empty($series))
        break;
    }
    if (empty($series)) {
      $cb = is_numeric($m['currentBalance'] ?? null) ? (float) $m['currentBalance'] : null;
      if ($cb !== null)
        $series[] = ['date' => gmdate('Y-m-d'), 'value' => $cb];
    }

    // Título "<size> <name>"
    $program = $account['program'] ?? null;
    $plabel = (string) ($program['label'] ?? $program['description'] ?? 'Account');
    $sb = $program['startingBalance'] ?? null;
    $size = '';
    $name = $plabel ?: 'Account';
    if (class_exists('MT_Accounts') && method_exists('MT_Accounts', 'parse_program_label')) {
      [$size, $name] = MT_Accounts::parse_program_label($plabel, $sb);
    } elseif (is_numeric($sb) && $sb > 0) {
      $k = (int) round($sb / 1000);
      $size = $k > 0 ? ($k . 'k') : (string) $sb;
    }
    $title = trim(($size ? $size . ' ' : '') . $name);

    return [
      'title' => $title,
      'plan_revenue' => $series,  // ← línea amarilla: currentBalance por día
      'series' => $series,  // (compat)
      'upper_bound' => $upper_bound,   // equityPassLevel (línea constante)
      'lower_bound' => $lower_bound,     // maxLossLimitEquityLevel (línea constante)
    ];
  }
}

// === Account Data (payload) ===
if (!function_exists('mt_accounts_build_account_data')) {
  function mt_accounts_build_account_data(array $account): array
  {
    // La API puede venir como objeto "platform" con campos internos.
    $plat = $account['platform'] ?? [];
    if (!is_array($plat))
      $plat = [];

    $platformName = (string) ($plat['platform'] ?? $account['platformName'] ?? $account['platform_label'] ?? '');
    $server = (string) ($plat['server'] ?? $account['server'] ?? '');
    $login = (string) ($plat['login'] ?? $account['login'] ?? '');
    $password = (string) ($plat['password'] ?? $account['password'] ?? '');

    // Fallback de login al email del usuario por si la API no lo trae
    if ($login === '' && is_user_logged_in()) {
      $u = wp_get_current_user();
      if ($u && $u->exists())
        $login = strtolower(trim((string) $u->user_email));
    }

    return [
      'platform' => $platformName,
      'server' => $server,
      'login' => $login,
      'password' => $password,
    ];
  }
}














