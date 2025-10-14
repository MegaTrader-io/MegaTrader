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

  private static function normalize_subscription_value($raw): string
  {
    if ($raw === null)
      return '';
    if (is_string($raw)) {
      $s = trim($raw);
      return ($s === '' || strtolower($s) === 'null') ? '' : $s;
    }
    if (is_array($raw) && isset($raw['data']) && is_array($raw['data'])) {
      $hex = '';
      foreach ($raw['data'] as $b) {
        $b = intval($b);
        if ($b < 0)
          $b = 256 + ($b % 256);
        $hex .= str_pad(dechex($b & 0xff), 2, '0', STR_PAD_LEFT);
      }
      return $hex;
    }
    return '';
  }

  private static function has_subscription($raw): bool
  {
    return self::normalize_subscription_value($raw) !== '';
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
    $DEFAULT_LOGO = '/wp-content/uploads/2025/07/Stylecolor-Sizelg.svg';
    $PLATFORM_LOGOS = [
      self::norm('MegaTrader') => $DEFAULT_LOGO,
      self::norm('NinjaTrader') => '/wp-content/uploads/2025/02/icon_ninjatrader.svg',
      self::norm('Tradovate') => '/wp-content/uploads/2025/02/icon_tradovate.svg',
      self::norm('Quantower') => '/wp-content/uploads/2025/02/icon_quantower.svg',
    ];

    // ===== Normaliza forma de respuesta de la API (data|results|items|asociativo) =====
    if (isset($accounts['data'])) {
      $accounts = is_array($accounts['data']) ? $accounts['data'] : [];
    } elseif (isset($accounts['results']) && is_array($accounts['results'])) {
      $accounts = $accounts['results'];
    } elseif (isset($accounts['items']) && is_array($accounts['items'])) {
      $accounts = $accounts['items'];
    } elseif (isset($accounts['id'])) {
      // Caso objeto único tipo ['id'=>...]
      $accounts = [$accounts];
    }


    // Si viene asociativo (keyed por id), conviértelo a lista
    if (!empty($accounts) && array_keys($accounts) !== range(0, count($accounts) - 1)) {
      $accounts = array_values($accounts);
    }

    if (defined('WP_DEBUG') && WP_DEBUG) {
      error_log('[MT][prepare_ui] in=' . (is_array($accounts) ? count($accounts) : 0));
    }

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
      $id = (string) ($acc['id'] ?? '');

      // ---- Program/label → size + name + badge
      $ptypeLabel = '';
      $ptypeClass = '';
      $plabel = (string) ($acc['program']['label'] ?? ($acc['program']['description'] ?? 'Account'));

      if ($plabel !== '') {
        $parts = array_map('trim', explode('|', $plabel));
        $last = $parts ? trim(end($parts)) : '';
        $ptypeLabel = $last;

        $key = strtolower(preg_replace('/\s+/', '-', $last));
        if ($key === 'evaluation') {
          $ptypeClass = 'badge-mega-evaluation';
        } elseif ($key === 'funded') {
          $ptypeClass = 'badge-mega-funded';
        } else {
          $ptypeClass = 'badge-mega-default';
        }
      }

      $sb = $acc['program']['startingBalance'] ?? null;
      [$size, $name] = self::parse_program_label($plabel, $sb);

      // ---- Platform texto/logo (del listado si viene)
      $platformRaw = '';
      if (isset($acc['platform'])) {
        if (is_array($acc['platform'])) {
          $platformRaw = (string) ($acc['platform']['platform'] ?? $acc['platform']['name'] ?? '');
        } else {
          $platformRaw = (string) $acc['platform'];
        }
      }
      if ($platformRaw === '' && isset($acc['program']['platform'])) {
        $platformRaw = (string) $acc['program']['platform'];
      }
      $platformKey = self::norm($platformRaw);
      $logo = $PLATFORM_LOGOS[$platformKey] ?? $DEFAULT_LOGO;

      // ---- Lo que trae el listado
      $status = (string) ($acc['status'] ?? '');
      $rules = is_array($acc['rules'] ?? null) ? $acc['rules'] : [];
      $plat = is_array($acc['platform'] ?? null) ? $acc['platform'] : [];
      $platAccountId = (string) ($plat['accountId'] ?? ($acc['accountId'] ?? ''));
      $order = (string) ($acc['order'] ?? '');
      $subscription = $acc['subscription'] ?? null;


      // === SIEMPRE: resolver byId para obtener 'order' (y plataforma si faltara)
      if ($id !== '' && function_exists('mt_accounts_resolve_account_by_id')) {
        try {
          $full = mt_accounts_resolve_account_by_id($id);
          if (is_array($full)) {

            $order = (string) ($full['order'] ?? $order);
            if ($subscription === null && array_key_exists('subscription', $full)) {
              $subscription = $full['subscription'];
            }

            // completar plataforma solo si faltaba
            if ($platformRaw === '' && isset($full['platform']) && is_array($full['platform'])) {
              $platformRaw = (string) ($full['platform']['platform'] ?? $full['platform']['name'] ?? $platformRaw);
              $platformKey = self::norm($platformRaw);
              $logo = $PLATFORM_LOGOS[$platformKey] ?? $logo;
            }
            if ($platAccountId === '') {
              $platAccountId = (string) ($full['platform']['accountId'] ?? $full['accountId'] ?? $platAccountId);
            }
          }
        } catch (\Throwable $e) { /* silencio */
        }
      }

      $subscriptionNorm = self::normalize_subscription_value($subscription);
      $hasSubscription = ($subscriptionNorm !== '');

      return [
        'id' => $id,
        'status' => (string) $status,
        'badgeClass' => self::badge_class($status),
        'size' => $size,
        'name' => $name ?: 'Account',
        'platform' => $platformRaw,
        'logo' => $logo,
        'createdAt' => (string) ($acc['createdAt'] ?? ''),
        // rules vienen del listado (sin byId)
        'mainProductId' => (string) ($rules['mainProductId'] ?? ''),
        'resetProductId' => (string) ($rules['resetProductId'] ?? ''),
        'activationProductId' => (string) ($rules['activationProductId'] ?? ''),
        'accountId' => (string) $platAccountId,
        'order' => $order,
        'subscription' => $subscriptionNorm,
        'hasSubscription' => $hasSubscription,
        'programTypeText' => $ptypeLabel,
        'programTypeClass' => $ptypeClass,
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

/* ==== Helper: obtener etapa del programa (Funded|Evaluation) ==== */
if (!function_exists('mt_program_stage')) {
  /**
   * @param array|string $programOrLabel  
   * @param string $default 
   * @return string 'Funded' | 'Evaluation' | '' (o $default)
   */
  function mt_program_stage($programOrLabel, $default = '')
  {
    $label = '';
    if (is_array($programOrLabel)) {
      $label = (string) ($programOrLabel['label'] ?? $programOrLabel['description'] ?? '');
    } else {
      $label = (string) $programOrLabel;
    }
    $label = trim(preg_replace('/\s+/', ' ', $label));

    if ($label === '')
      return $default;

    // 1) Preferimos la última sección separada por '|'
    $parts = array_map('trim', explode('|', $label));
    $last = end($parts);
    $key = strtolower(preg_replace('/[^a-z]/i', '', $last)); // deja solo letras

    if ($key === 'funded')
      return 'Funded';
    if ($key === 'evaluation')
      return 'Evaluation';

    // 2) Fallback: buscar en todo el label
    if (stripos($label, 'Funded') !== false)
      return 'Funded';
    if (stripos($label, 'Evaluation') !== false)
      return 'Evaluation';

    return $default;
  }
}

if (!function_exists('mt_is_funded')) {
  function mt_is_funded($programOrLabel)
  {
    return mt_program_stage($programOrLabel) === 'Funded';
  }
}
if (!function_exists('mt_is_evaluation')) {
  function mt_is_evaluation($programOrLabel)
  {
    return mt_program_stage($programOrLabel) === 'Evaluation';
  }
}

/* ==== Helper: detectar plan (Funded|Elite|Growth) desde el label ==== */

if (!function_exists('mt_program_plan')) {
  function mt_program_plan($programOrLabel, $default = '')
  {
    $label = is_array($programOrLabel)
      ? (string) ($programOrLabel['label'] ?? $programOrLabel['description'] ?? '')
      : (string) $programOrLabel;
    $label = trim(preg_replace('/\s+/', ' ', $label));
    if ($label === '')
      return $default;

    if (stripos($label, 'Funded') !== false)
      return 'Funded';
    if (stripos($label, 'Elite') !== false)
      return 'Elite';
    if (stripos($label, 'Growth') !== false)
      return 'Growth';
    return $default;
  }
}

if (!function_exists('mt_program_rules_url')) {
  function mt_program_rules_url($programOrLabel)
  {
    $plan = mt_program_plan($programOrLabel, '');
    $map = Label::PLAN_RULES_URLS ?? [];
    return $map[$plan] ?? '';
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
      'accountId' => (string) ($account['id'] ?? ''),
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
      'label' => $program['label'] ?? $program['description'] ?? mt__get($metrics, ['label']),
      'consistency' => mt__get($account, ['rules', 'consistency']),
      'targetAmount' => mt__get($account, ['payout', 'payoutCycle', 'targetAmount']),
      'consistencyCurrentBestWorstDayProfit' => $metrics['consistencyCurrentBestWorstDayProfit'] ?? null,

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
    $accountId = $m['accountId'];
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
        'accountId' => (string) $accountId,
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

if (!function_exists('mt_format_percent_compact')) {
  /**
   * Imprime porcentaje sin signo, sin separador de miles y recortando ceros:
   * 20     -> "20%"
   * 20.50  -> "20.5%"
   * 20.00  -> "20%"
   * -3.40  -> "-3.4%"
   */
  function mt_format_percent_compact($value, $empty = '—')
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return $empty;

    $txt = number_format((float) $value, 2, '.', ''); // sin miles
    $txt = rtrim(rtrim($txt, '0'), '.');              // quita ceros y el punto
    return $txt . '%';
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
// - value == 0 target  => success (check verde)
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

    if (abs($v - $t) < 1e-9) {
      return $cls_pos;
    }

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


// === AJAX: devuelve el HTML de template-parts/account/account-data por accountId ===

add_action('wp_ajax_mt_accounts_data', 'mt_accounts_ajax_account_data');
add_action('wp_ajax_nopriv_mt_accounts_data', 'mt_accounts_ajax_account_data');
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



// === Performance Chart Payload ===


if (!function_exists('mt_accounts_build_performance_chart')) {
  function mt_accounts_build_performance_chart(array $account): array
  {
    $tz = new DateTimeZone('UTC');

    $todayDt = new DateTime('now', $tz);
    $todayDt->setTime(0, 0, 0);

    $firstRawTop = (string) ($account['firstTradeDate'] ?? '');
    $firstRawMetric = (string) ($account['metrics']['firstTradeDate'] ?? $account['metric']['firstTradeDate'] ?? '');
    $firstRawAlt1 = (string) ($account['createdAt'] ?? '');
    $firstRawAlt2 = (string) ($account['owner']['account']['createdAt'] ?? '');

    $firstRaw = $firstRawTop ?: ($firstRawMetric ?: ($firstRawAlt1 ?: $firstRawAlt2));
    $firstDt = $firstRaw ? new DateTime($firstRaw, $tz) : clone $todayDt;
    $firstDt->setTime(0, 0, 0);
    if ($firstDt > $todayDt) {
      $firstDt = clone $todayDt;
    }

    $accountId = (string) ($account['accountId'] ?? $account['id'] ?? '');


    $totalSinceStart = (int) $firstDt->diff($todayDt)->days + 1;
    $pointsToLoad = min(30, max(1, $totalSinceStart));

    $startDt = (clone $todayDt)->modify('-' . ($pointsToLoad - 1) . ' days');
    $dates = [];
    for ($i = 0; $i < $pointsToLoad; $i++) {
      $ymd = $startDt->format('Y-m-d');
      $dates[] = ['fromDate' => $ymd, 'toDate' => $ymd];
      $startDt->modify('+1 day');
    }

    $periods = [];
    $sinceTextDays = $totalSinceStart;
    $sinceValue = $pointsToLoad;
    if ($sinceTextDays < 7) {
      $periods[] = ['value' => $sinceValue, 'text' => "SINCE START ({$sinceTextDays} DAYS)"];
    } elseif ($sinceTextDays < 14) {
      $periods[] = ['value' => 7, 'text' => 'LAST 7 DAYS'];
      $periods[] = ['value' => $sinceValue, 'text' => "SINCE START ({$sinceTextDays} DAYS)"];
    } elseif ($sinceTextDays < 30) {
      $periods[] = ['value' => 7, 'text' => 'LAST 7 DAYS'];
      $periods[] = ['value' => 14, 'text' => 'LAST 14 DAYS'];
      $periods[] = ['value' => $sinceValue, 'text' => "SINCE START ({$sinceTextDays} DAYS)"];
    } else {
      $periods[] = ['value' => 7, 'text' => 'LAST 7 DAYS'];
      $periods[] = ['value' => 14, 'text' => 'LAST 14 DAYS'];
      $periods[] = ['value' => 30, 'text' => 'LAST 30 DAYS'];
      $periods[] = ['value' => $sinceValue, 'text' => "SINCE START ({$sinceTextDays} DAYS)"];
    }
    error_log('[MT][PERF] periods=' . wp_json_encode($periods));

    $m = $account['metrics'] ?? $account['metric'] ?? [];
    $upper_bound = is_numeric($m['equityPassLevel'] ?? null) ? (float) $m['equityPassLevel'] : null;
    $lower_bound = is_numeric($m['maxLossLimitEquityLevel'] ?? null) ? (float) $m['maxLossLimitEquityLevel'] : null;
    error_log('[MT][PERF] bounds: upper=' . var_export($upper_bound, true) . ' | lower=' . var_export($lower_bound, true));

    $series = [];
    if ($accountId) {
      foreach ($dates as $d) {
        $ymd = $d['fromDate'];

        $resp = null;

        if (function_exists('mega_api_get_metrics')) {
          try {
            $resp = mega_api_get_metrics($accountId, 1, 10, $ymd, $ymd);
          } catch (\Throwable $e) {
            error_log('[MT][PERF][err] mega_api_get_metrics: ' . $e->getMessage());
          }
        }

        if ((!is_array($resp) || empty($resp['data'])) && function_exists('mt_metrics_fetch_by_shortcode')) {
          try {
            $resp = mt_metrics_fetch_by_shortcode($accountId, ['from' => $ymd, 'to' => $ymd]);
          } catch (\Throwable $e) {
            error_log('[MT][PERF][err] mt_metrics_fetch_by_shortcode(array): ' . $e->getMessage());
          }
          if (!is_array($resp) || !isset($resp['data'])) {
            try {
              $resp = mt_metrics_fetch_by_shortcode($accountId, $ymd, $ymd);
            } catch (\Throwable $e) {
              error_log('[MT][PERF][err] mt_metrics_fetch_by_shortcode(scalar): ' . $e->getMessage());
            }
          }
        }

        $count = (is_array($resp) && isset($resp['data']) && is_array($resp['data'])) ? count($resp['data']) : 0;

        $balance = null;
        $bestTs = -1;
        if ($count > 0) {
          foreach ($resp['data'] as $row) {
            $metrics = (isset($row['metrics']) && is_array($row['metrics'])) ? $row['metrics'] : [];
            $cb = $metrics['currentBalance'] ?? null;
            if (!is_numeric($cb))
              continue;
            $ts = strtotime($row['updatedAt'] ?? $row['createdAt'] ?? '');
            if ($ts === false)
              $ts = 0;
            if ($ts >= $bestTs) {
              $bestTs = $ts;
              $balance = (float) $cb;
            }
          }
        }

        if (is_numeric($balance)) {
          $series[] = ['date' => $ymd, 'value' => (float) $balance];
        }
      }
    }

    if (empty($series)) {
      $cb = is_numeric($m['currentBalance'] ?? null) ? (float) $m['currentBalance'] : null;
      if ($cb !== null)
        $series[] = ['date' => $todayDt->format('Y-m-d'), 'value' => $cb];
    }


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
      'accountId' => $accountId,
      'title' => $title,
      'plan_revenue' => $series,   // [{date, value}]
      'series' => $series,
      'upper_bound' => $upper_bound,
      'lower_bound' => $lower_bound,
      'periods' => $periods,
    ];
  }
}


// === Account Data (payload) ===
if (!function_exists('mt_accounts_build_account_data')) {
  function mt_accounts_build_account_data(array $account): array
  {
    $plat = $account['platform'] ?? [];
    if (!is_array($plat))
      $plat = [];

    $platformName = (string) ($plat['platform'] ?? $account['platformName'] ?? $account['platform_label'] ?? '');
    $server = (string) ($plat['server'] ?? $account['server'] ?? '');
    $login = (string) ($plat['login'] ?? $account['login'] ?? '');
    $password = (string) ($plat['password'] ?? $account['password'] ?? '');
    $accountId = (string) ($plat['accountId'] ?? $account['accountId'] ?? '');

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
      'accountId' => $accountId
    ];
  }
}

/**
 * Find a product ID by category slugs.
 *
 * @param array $slugs
 * @return int|null
 */
if (!function_exists('mt_find_product_by_category_slugs')) {
  function mt_find_product_by_category_slugs(array $slugs)
  {
    if (!function_exists('wc_get_products'))
      return null;
    $args = [
      'status' => 'publish',
      'limit' => 1,
      'category' => array_map('sanitize_title', $slugs),
    ];
    $products = wc_get_products($args);
    if (!empty($products)) {
      $prod = $products[0];
      if (is_object($prod) && method_exists($prod, 'get_id')) {
        return (int) $prod->get_id();
      }
    }
    return null;
  }
}

/**
 * Build a checkout URL that adds a product to cart.
 *
 * @param int $product_id
 * @return string
 */
if (!function_exists('mt_checkout_add_to_cart_url')) {
  function mt_checkout_add_to_cart_url($product_id)
  {
    if (!$product_id || !function_exists('wc_get_checkout_url'))
      return '';
    return wc_get_checkout_url() . '?add-to-cart=' . intval($product_id);
  }
}

if (!function_exists('mt_reset_checkout_url')) {
  function mt_reset_checkout_url($product_id = null)
  {
    if (is_numeric($product_id) && (int) $product_id > 0) {
      return mt_checkout_add_to_cart_url((int) $product_id);
    }
    // Fallback legacy por categoría
    $fallback_id = mt_find_product_by_category_slugs(['reset-fee']);
    return $fallback_id ? mt_checkout_add_to_cart_url($fallback_id) : '';
  }
}


if (!function_exists('mt_get_agreement_status_by_email')) {

  /**
   * Obtiene estado de acuerdo por email (URL-encoded) desde el shortcode.
   * Devuelve:
   *  - agreementURL (string|null)
   *  - agreementSigned (bool|null)
   *  - agreementStatus (string|null)           // valor original (p.ej., "ACTIVE")
   *  - agreementStatusBool (bool|null)         // derivado de status string (p.ej., ACTIVE => true)
   */
  function mt_get_agreement_status_by_email(string $email_encoded, int $ttl = 120): array
  {
    $email_encoded = is_string($email_encoded) ? trim($email_encoded) : '';
    if ($email_encoded === '') {
      if (defined('WP_DEBUG') && WP_DEBUG)
        error_log('[MT Agreement] empty email');
      return [
        'agreementURL' => null,
        'agreementSigned' => null,
        'agreementStatus' => null,
        'agreementStatusBool' => null,
      ];
    }
    if (strpos($email_encoded, '%') === false && strpos($email_encoded, '@') !== false) {
      $email_encoded = rawurlencode(strtolower($email_encoded));
      if (defined('WP_DEBUG') && WP_DEBUG)
        error_log('[MT Agreement][normalized_email]=' . $email_encoded);
    }

    if (defined('WP_DEBUG') && WP_DEBUG) {
      error_log('[MT Agreement][in] email=' . $email_encoded . ' ttl=' . max(0, $ttl));
    }

    // Ejecutar shortcode
    $sc = sprintf(
      '[mega_subscriptions_data email="%s" output="json" ttl="%d"]',
      esc_attr($email_encoded),
      max(0, $ttl)
    );
    if (defined('WP_DEBUG') && WP_DEBUG)
      error_log('[MT Agreement][sc]=' . $sc);

    $raw = do_shortcode($sc);
    $raw = is_string($raw) ? trim(wp_unslash($raw)) : '';

    // Quitar BOM si existe y decodificar entidades HTML
    if ($raw !== '' && substr($raw, 0, 3) === "\xEF\xBB\xBF")
      $raw = substr($raw, 3);
    if ($raw !== '')
      $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    if (defined('WP_DEBUG') && WP_DEBUG) {
      $preview = substr((string) $raw, 0, 800);
      error_log('[MT Agreement][raw(len)]= ' . strlen((string) $raw));
      error_log('[MT Agreement][raw(preview)]= ' . $preview);
    }

    // Intento de parseo JSON (con fallback limpiando tags)
    $data = json_decode($raw, true);
    if (!is_array($data))
      $data = json_decode(trim(wp_strip_all_tags($raw)), true);

    if (defined('WP_DEBUG') && WP_DEBUG) {
      $jsonErr = function_exists('json_last_error_msg') ? json_last_error_msg() : 'N/A';
      error_log('[MT Agreement][json_error]= ' . $jsonErr);
      error_log('[MT Agreement][parsed]=' . (is_array($data) ? wp_json_encode($data) : 'null'));
    }

    if (!is_array($data)) {
      return [
        'agreementURL' => null,
        'agreementSigned' => null,
        'agreementStatus' => null,
        'agreementStatusBool' => null,
      ];
    }

    if (isset($data['data']) && is_array($data['data']))
      $data = $data['data'];

    $agreement_url = null;
    foreach (['agreementURL', 'agreementUrl', 'agreement_url', 'url'] as $k) {
      if (!empty($data[$k]) && is_string($data[$k])) {
        $agreement_url = $data[$k];
        break;
      }
    }

    $signed_raw = $data['agreementSigned'] ?? ($data['signed'] ?? null);
    $signed = filter_var($signed_raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

    $status_raw = $data['agreementStatus'] ?? ($data['status'] ?? null);
    $status_str = is_string($status_raw) ? trim($status_raw) : (is_bool($status_raw) ? ($status_raw ? 'true' : 'false') : null);
    $status_lc = is_string($status_str) ? strtolower($status_str) : null;

    $status_true_set = ['active', 'signed', 'approved', 'enabled', 'complete', 'completed', 'ok'];
    $status_false_set = ['required', 'pending', 'waiting', 'needed', 'unsigned', 'declined', 'rejected'];

    $status_bool = null;
    if ($status_lc !== null) {
      if (in_array($status_lc, $status_true_set, true))
        $status_bool = true;
      if (in_array($status_lc, $status_false_set, true))
        $status_bool = false;
      if ($status_bool === null) {
        $tmp = filter_var($status_lc, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($tmp !== null)
          $status_bool = $tmp;
      }
    }

    if ($signed === null && $status_bool !== null)
      $signed = $status_bool;

    if ($signed === false && $status_bool === true)
      $signed = true;

    if (defined('WP_DEBUG') && WP_DEBUG) {
      error_log('[MT Agreement][final] signed_raw=' . var_export($signed_raw, true)
        . ' signed=' . var_export($signed, true)
        . ' status_raw=' . var_export($status_str, true)
        . ' status_bool=' . var_export($status_bool, true)
        . ' url=' . ($agreement_url ?? ''));
    }

    return [
      'agreementURL' => $agreement_url,
      'agreementSigned' => $signed,
      'agreementStatus' => $status_str,
      'agreementStatusBool' => $status_bool,
    ];
  }
}

// === AJAX: devolver STATUS por accountId ===
add_action('wp_ajax_mt_accounts_status', 'mt_accounts_ajax_status');
add_action('wp_ajax_nopriv_mt_accounts_status', 'mt_accounts_ajax_status');


function mt_accounts_ajax_status()
{
  check_ajax_referer('mt-acc-nonce', 'nonce');

  $accountId = sanitize_text_field((string) ($_POST['accountId'] ?? $_POST['account_id'] ?? ''));
  if ($accountId === '') {
    wp_send_json_error(['message' => 'Missing accountId']);
  }

  $found = null;

  if (!$found && function_exists('mt_accounts_resolve_account_by_id')) {
    try {
      $found = mt_accounts_resolve_account_by_id($accountId);
    } catch (Throwable $e) {
    }
  }

  if (!$found && class_exists('MT_Accounts') && method_exists('MT_Accounts', 'get_account_by_id')) {
    try {
      $found = MT_Accounts::get_account_by_id($accountId);
    } catch (Throwable $e) {
    }
  }

  if (!$found && class_exists('MT_Accounts') && method_exists('MT_Accounts', 'get_accounts')) {
    try {
      $all = MT_Accounts::get_accounts();
      if (is_array($all)) {
        foreach ($all as $row) {
          $rid = (string) ($row['id'] ?? $row['accountId'] ?? $row['account_id'] ?? '');
          if ($rid === (string) $accountId) {
            $found = $row;
            break;
          }
        }
      }
    } catch (Throwable $e) {
    }
  }

  if (!$found || !is_array($found)) {
    wp_send_json_error(['message' => 'Account not found']);
  }

  $status = '';
  if (class_exists('MT_Accounts') && method_exists('MT_Accounts', 'prepare_ui')) {
    try {
      $ui = MT_Accounts::prepare_ui([$found]);
      if (is_array($ui))
        $status = (string) ($ui['current']['status'] ?? '');
    } catch (Throwable $e) {
    }
  }
  if ($status === '')
    $status = (string) ($found['status'] ?? '');
  $status = trim($status);

  if ($status === '') {
    wp_send_json_error(['message' => 'Status not found']);
  }

  wp_send_json_success(['status' => $status]);
}


/* === MÉTRICS vía shortcode (JSON) === */
if (!function_exists('mt_metrics_fetch_by_shortcode')) {
  function mt_metrics_fetch_by_shortcode($accountId, $page = 1, $perPage = 30, $ttl = 15)
  {
    $accountId = trim((string) $accountId);
    if ($accountId === '')
      return null;

    $sc = sprintf(
      '[mega_metrics_data id="%s" page="%d" perpage="%d" output="json" ttl="%d"]',
      esc_attr($accountId),
      (int) $page,
      (int) $perPage,
      (int) $ttl
    );

    $raw = do_shortcode($sc);
    if (!is_string($raw) || $raw === '')
      return null;
    $raw = trim(wp_strip_all_tags($raw));
    $data = json_decode($raw, true);
    return is_array($data) ? $data : null;
  }
}

/* === TRADES vía shortcode (JSON) === */
if (!function_exists('mt_trades_fetch_by_shortcode')) {
  function mt_trades_fetch_by_shortcode(string $accountId, string $type = 'CLOSED', int $page = 1, int $perPage = 500)
  {
    $accountId = trim((string) $accountId);
    if ($accountId === '')
      return null;

    $sc = sprintf(
      '[mega_trades_data id="%s" type="%s" page="%d" perpage="%d" output="json"]',
      esc_attr($accountId),
      esc_attr($type),
      (int) $page,
      (int) $perPage
    );

    $raw = do_shortcode($sc);
    $raw = is_string($raw) ? trim(wp_unslash($raw)) : '';
    if ($raw !== '' && substr($raw, 0, 3) === "\xEF\xBB\xBF")
      $raw = substr($raw, 3);
    if ($raw !== '')
      $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    $json = json_decode($raw, true);
    if (!is_array($json))
      $json = json_decode(trim(wp_strip_all_tags($raw)), true);
    if (!is_array($json))
      return null;

    if (isset($json['data']) && is_array($json['data']))
      return $json['data'];
    if (isset($json[0]) && is_array($json[0]))
      return $json;
    return null;
  }
}

/* === TRADES: streaks + duraciones por día (YYYY-MM-DD) === */
if (!function_exists('mt_trades_day_stats')) {
  function mt_trades_day_stats(string $accountId, string $day_iso): array
  {
    static $cache = [];
    $day_iso = substr((string) $day_iso, 0, 10);
    if ($day_iso === '') {
      return ['maxConsecWins' => '-', 'maxConsecLosses' => '-', 'avgWinDuration' => '-', 'avgLossDuration' => '-'];
    }
    if (!isset($cache[$accountId])) {
      $trades = mt_trades_fetch_by_shortcode($accountId, 'CLOSED', 1, 500);
      $cache[$accountId] = is_array($trades) ? $trades : [];
    }
    $trades = $cache[$accountId];

    $dayTrades = array_values(array_filter($trades, function ($t) use ($day_iso) {
      if (!isset($t['closeTime']))
        return false;
      return substr((string) $t['closeTime'], 0, 10) === $day_iso;
    }));
    if (empty($dayTrades)) {
      // SIN TRADES ESE DÍA → todo en '-'
      return ['maxConsecWins' => '-', 'maxConsecLosses' => '-', 'avgWinDuration' => '-', 'avgLossDuration' => '-'];
    }

    usort($dayTrades, function ($a, $b) {
      $ta = strtotime((string) ($a['closeTime'] ?? '')) ?: 0;
      $tb = strtotime((string) ($b['closeTime'] ?? '')) ?: 0;
      return $ta <=> $tb;
    });

    $maxW = 0;
    $maxL = 0;
    $runW = 0;
    $runL = 0;
    $sumW = 0;
    $cntW = 0;
    $sumL = 0;
    $cntL = 0;

    foreach ($dayTrades as $t) {
      if (!array_key_exists('pnl', $t)) {
        $runW = 0;
        $runL = 0;
        continue;
      }
      $pnl = $t['pnl'];

      $durSec = null;
      if (isset($t['openTime'], $t['closeTime'])) {
        $o = strtotime((string) $t['openTime']);
        $c = strtotime((string) $t['closeTime']);
        if ($o && $c && $c >= $o)
          $durSec = $c - $o;
      }

      if (is_numeric($pnl) && $pnl > 0) {
        $runW++;
        $runL = 0;
        $maxW = max($maxW, $runW);
        if ($durSec !== null) {
          $sumW += $durSec;
          $cntW++;
        }
      } elseif (is_numeric($pnl) && $pnl < 0) {
        $runL++;
        $runW = 0;
        $maxL = max($maxL, $runL);
        if ($durSec !== null) {
          $sumL += $durSec;
          $cntL++;
        }
      } else {
        $runW = 0;
        $runL = 0;
      }
    }

    $fmt = function ($s) {
      $s = (int) round($s);
      return sprintf('%02d:%02d:%02d', floor($s / 3600), floor(($s % 3600) / 60), $s % 60);
    };

    // CON TRADES ESE DÍA:
    // - Si faltan pérdidas/ganancias → 0 y 00:00:00 (no '-')
    return [
      'maxConsecWins' => ($maxW > 0) ? $maxW : 0,
      'maxConsecLosses' => ($maxL > 0) ? $maxL : 0,
      'avgWinDuration' => ($cntW > 0) ? $fmt($sumW / $cntW) : '00:00:00',
      'avgLossDuration' => ($cntL > 0) ? $fmt($sumL / $cntL) : '00:00:00',
    ];
  }
}

/* === UTC -> Eastern (US/Eastern) a 'YYYY-MM-DD' === */
if (!function_exists('mt_utc_to_eastern_ymd')) {
  /**
   * Convierte una fecha/hora UTC (string) al día 'YYYY-MM-DD' en America/New_York.
   * Admite 'YYYY-MM-DD' o timestamps ISO (con o sin 'Z').
   * Devuelve '' si no puede parsear.
   */
  function mt_utc_to_eastern_ymd($utcString)
  {
    $src = is_string($utcString) ? trim($utcString) : '';
    if ($src === '')
      return '';
    try {
      // Si viene solo YYYY-MM-DD, asumir 00:00:00 UTC de ese día
      if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $src)) {
        $src .= ' 00:00:00';
      }
      $utc = new DateTimeZone('UTC');
      $ny = new DateTimeZone('America/New_York');
      $dt = new DateTime($src, $utc);
      $dt->setTimezone($ny);
      return $dt->format('Y-m-d');
    } catch (\Throwable $e) {
      return '';
    }
  }
}

/* === Sumatoria de commission por día (Eastern) === */
if (!function_exists('mt_sum_commissions_for_day')) {
  /**
   * Suma 'commission' de todos los trades CERRADOS cuyo openTime y closeTime,
   * ambos convertidos a Eastern (US/Eastern), caen en el MISMO día y coinciden
   * con $day_iso_eastern (YYYY-MM-DD).
   * Retorna float (negativo generalmente) o '-' si no hay ningún trade que cumpla.
   */
  function mt_sum_commissions_for_day(string $accountId, string $day_iso_eastern)
  {
    static $cache = [];
    $accountId = trim((string) $accountId);
    $day_iso_eastern = substr((string) $day_iso_eastern, 0, 10);
    if ($accountId === '' || $day_iso_eastern === '')
      return '-';

    if (!isset($cache[$accountId])) {
      $trades = mt_trades_fetch_by_shortcode($accountId, 'CLOSED', 1, 500);
      $cache[$accountId] = is_array($trades) ? $trades : [];
    }
    $trades = $cache[$accountId];
    if (empty($trades))
      return '-';

    $sum = 0.0;
    $found = false;
    foreach ($trades as $t) {
      $ot = isset($t['openTime']) ? (string) $t['openTime'] : '';
      $ct = isset($t['closeTime']) ? (string) $t['closeTime'] : '';
      if ($ot === '' || $ct === '')
        continue;

      $ot_ymd = function_exists('mt_utc_to_eastern_ymd') ? mt_utc_to_eastern_ymd($ot) : substr($ot, 0, 10);
      $ct_ymd = function_exists('mt_utc_to_eastern_ymd') ? mt_utc_to_eastern_ymd($ct) : substr($ct, 0, 10);

      if ($ot_ymd === $day_iso_eastern && $ct_ymd === $day_iso_eastern) {
        if (isset($t['commission']) && is_numeric($t['commission'])) {
          $sum += (float) $t['commission'];
          $found = true;
        }
      }
    }
    return $found ? $sum : '-';
  }
}

/* === Conteo de trades por día (Eastern) === */
if (!function_exists('mt_count_trades_for_day')) {
  /**
   * Cuenta trades CERRADOS cuyo openTime y closeTime, convertidos a Eastern,
   * caen el mismo día y coinciden con $day_iso_eastern (YYYY-MM-DD).
   */
  function mt_count_trades_for_day(string $accountId, string $day_iso_eastern): int
  {
    static $cache = [];
    $accountId = trim((string) $accountId);
    $day_iso_eastern = substr((string) $day_iso_eastern, 0, 10);
    if ($accountId === '' || $day_iso_eastern === '')
      return 0;

    if (!isset($cache[$accountId])) {
      $trades = mt_trades_fetch_by_shortcode($accountId, 'CLOSED', 1, 500);
      $cache[$accountId] = is_array($trades) ? $trades : [];
    }
    $trades = $cache[$accountId];
    if (empty($trades))
      return 0;

    $cnt = 0;
    foreach ($trades as $t) {
      $ot = isset($t['openTime']) ? (string) $t['openTime'] : '';
      $ct = isset($t['closeTime']) ? (string) $t['closeTime'] : '';
      if ($ot === '' || $ct === '')
        continue;

      $ot_ymd = function_exists('mt_utc_to_eastern_ymd') ? mt_utc_to_eastern_ymd($ot) : substr($ot, 0, 10);
      $ct_ymd = function_exists('mt_utc_to_eastern_ymd') ? mt_utc_to_eastern_ymd($ct) : substr($ct, 0, 10);

      if ($ot_ymd === $day_iso_eastern && $ct_ymd === $day_iso_eastern) {
        $cnt++;
      }
    }
    return $cnt;
  }
}


/* === DAILY JOURNAL: construir payload (con mapeos nuevos y fees desde trades) === */
if (!function_exists('mt_accounts_build_daily_journal')) {
  function mt_accounts_build_daily_journal($accountId, $page = 1, $perPage = 30)
  {
    $json = mt_metrics_fetch_by_shortcode($accountId, $page, $perPage);
    $items = [];
    if (is_array($json)) {
      if (isset($json['data']) && is_array($json['data']))
        $items = $json['data'];
      elseif (isset($json[0]) && is_array($json[0]))
        $items = $json;
    }

    $rows = [];
    foreach ($items as $it) {
      $m = (isset($it['metrics']) && is_array($it['metrics'])) ? $it['metrics'] : [];

      // Día base desde metrics (UTC) → Eastern (solo día)
      $tradeDateRaw = $m['tradeDate'] ?? ($m['tradeDateTimeUTC'] ?? null);
      $day_iso_eastern = function_exists('mt_utc_to_eastern_ymd')
        ? mt_utc_to_eastern_ymd($tradeDateRaw)
        : (is_string($tradeDateRaw) ? substr($tradeDateRaw, 0, 10) : '');

      // 1) Si NO pudimos determinar el día Eastern → NO pintes fila
      if ($day_iso_eastern === '')
        continue;

      // 2) Si NO hubo trades ese día en Eastern → NO pintes fila
      if (function_exists('mt_count_trades_for_day')) {
        $countTrades = mt_count_trades_for_day((string) $accountId, $day_iso_eastern);
        if ($countTrades <= 0)
          continue;
      }

      // openTime será siempre el día Eastern (evitamos fallback a 'now' en mt_parse_open_time)
      $openTime = $day_iso_eastern;

      // === Mapeos nuevos ===
      $net = array_key_exists('netPnL', $m) ? (float) $m['netPnL']
        : (array_key_exists('dailyTotalRealizedPnL', $m) ? (float) $m['dailyTotalRealizedPnL'] : '-');

      $hi = array_key_exists('bestTrade', $m) ? (float) $m['bestTrade'] : '-';
      $lo = array_key_exists('worstTrade', $m) ? (float) $m['worstTrade'] : '-';
      $ct = array_key_exists('totalClosedVolume', $m) ? (int) $m['totalClosedVolume'] : '-';
      $trades = array_key_exists('tradesPlaced', $m) ? (int) $m['tradesPlaced'] : '-';
      $awin = array_key_exists('averageWinningTrade', $m) ? (float) $m['averageWinningTrade'] : '-';
      $aloss = array_key_exists('averageLosingTrade', $m) ? (float) $m['averageLosingTrade'] : '-';
      $winPct = array_key_exists('winRate', $m) ? (float) $m['winRate'] : '-';
      $lossPct = array_key_exists('lossRate', $m) ? (float) $m['lossRate'] : '-';

      // Comisiones: si no hay trades ni comisiones, ya filtramos arriba; aquí calculamos monto
      $fees = '-';
      if ((string) $accountId !== '' && function_exists('mt_sum_commissions_for_day')) {
        $fees = mt_sum_commissions_for_day((string) $accountId, $day_iso_eastern);
      }

      // Streaks/duraciones (usando el mismo día Eastern)
      $stats = ($accountId && function_exists('mt_trades_day_stats'))
        ? mt_trades_day_stats((string) $accountId, $day_iso_eastern)
        : ['maxConsecWins' => '-', 'maxConsecLosses' => '-', 'avgWinDuration' => '-', 'avgLossDuration' => '-'];

      $maxW = $stats['maxConsecWins'] ?? '-';
      $maxL = $stats['maxConsecLosses'] ?? '-';
      $dWin = $stats['avgWinDuration'] ?? '-';
      $dLos = $stats['avgLossDuration'] ?? '-';

      $max = ($maxW === '-' && $maxL === '-') ? '-' : ($maxW . '/' . $maxL);
      $dur = ($dWin === '-' && $dLos === '-') ? '-' : ($dWin . ' / ' . $dLos);

      $rows[] = [
        'openTime' => $openTime,   // ← SIEMPRE Eastern YYYY-MM-DD
        'net' => $net,
        'hi' => $hi,
        'lo' => $lo,
        'ct' => $ct,
        'fees' => $fees,
        'trades' => $trades,
        'awin' => $awin,
        'aloss' => $aloss,
        'win' => $winPct,
        'loss' => $lossPct,
        'max' => $max,
        'dur' => $dur,
      ];
    }


    // Orden descendente por fecha (usa 'openTime', que ahora es YYYY-MM-DD (Eastern) o string)
    usort($rows, function ($a, $b) {
      $ta = strtotime((string) ($a['openTime'] ?? '')) ?: 0;
      $tb = strtotime((string) ($b['openTime'] ?? '')) ?: 0;
      return $tb <=> $ta;
    });

    return ['rows' => $rows, 'per_page' => (int) $perPage];
  }
}



/* === DAILY JOURNAL: render SOLO filas (para AJAX) === */
if (!function_exists('mt_daily_journal_rows_html')) {
  function mt_daily_journal_rows_html(array $rows, int $per_page, $acc_id): string
  {
    $user_id = get_current_user_id();

    $fmt_money = function ($v) {
      if ($v === '-' || $v === null || $v === '')
        return '-';
      if (!is_numeric($v))
        return '-';
      $n = (float) $v;
      $sign = $n < 0 ? '-' : '';
      $abs = abs($n);
      return $sign . '$' . number_format($abs, 2, '.', ',');
    };
    $fmt_int = function ($v) {
      return ($v === '-' ? '-' : number_format((int) $v));
    };
    $fmt_pct = function ($v) {
      return ($v === '-' ? '-' : (number_format((float) $v, 2) . '%'));
    };

    ob_start();
    foreach ($rows as $i => $r) {
      $page = (int) floor($i / max(1, $per_page)) + 1;

      $ts = isset($r['openTime']) ? strtotime((string) $r['openTime']) : 0;
      $day_iso = $ts ? gmdate('Y-m-d', $ts) : '';
      $day_label = $ts ? gmdate('m/d/Y', $ts) : '-';

      $fb = ($acc_id && $day_iso && function_exists('mt_get_daily_feedback'))
        ? mt_get_daily_feedback($user_id, (int) $acc_id, $day_iso) : null;
      $has_fb = !empty($fb);
      $mood = $has_fb ? (int) ($fb['mood'] ?? 0) : 0;
      $follow = $has_fb ? ((int) ($fb['followed_plan'] ?? 0) ? 1 : 0) : 0;
      $note = $has_fb ? (string) ($fb['note'] ?? '') : '';

      $net = $r['net'] ?? '-';
      $net_class = (is_numeric($net) ? ($net > 0 ? 'text-success' : ($net < 0 ? 'text-danger' : '')) : '');

      ?>
      <div class="dj-grid dj-row" id="dj-row-<?php echo esc_attr($day_iso); ?>" data-page="<?php echo esc_attr($page); ?>"
        data-trade-date="<?php echo esc_attr($day_iso); ?>" data-has-fb="<?php echo $has_fb ? '1' : '0'; ?>"
        data-mood="<?php echo $has_fb ? (int) $mood : ''; ?>" data-followed="<?php echo $has_fb ? (int) $follow : ''; ?>"
        data-note="<?php echo $has_fb ? esc_attr($note) : ''; ?>" style="<?php echo $page === 1 ? '' : 'display:none'; ?>">
        <div class="dj-cell is-left">
          <span class="mt-dj-visibility" role="button" tabindex="0" aria-label="Add daily feedback" title="Daily feedback">
            <i class="mt-icon mt-icon-white <?php echo $has_fb ? 'mt-icon_visibility' : 'mt-icon_pencil'; ?>"
              aria-hidden="true"></i>
          </span>
        </div>

        <div class="dj-cell is-right"><?php echo esc_html($day_label); ?></div>
        <div class="dj-cell is-right <?php echo esc_attr($net_class); ?>"><?php echo esc_html($fmt_money($net)); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html($fmt_money($r['hi'] ?? '-')); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html($fmt_money($r['lo'] ?? '-')); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html($fmt_int($r['ct'] ?? '-')); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html($fmt_money($r['fees'] ?? '-')); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html($fmt_int($r['trades'] ?? '-')); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html($fmt_money($r['awin'] ?? '-')); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html($fmt_money($r['aloss'] ?? '-')); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html($fmt_pct($r['win'] ?? '-')); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html($fmt_pct($r['loss'] ?? '-')); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html((string) ($r['max'] ?? '-')); ?></div>
        <div class="dj-cell is-right"><?php echo esc_html((string) ($r['dur'] ?? '-')); ?></div>
      </div>
      <?php
    }
    return trim(ob_get_clean());
  }
}



// === Profile (My Profile modal) ============================================
// Helpers + AJAX para cargar/guardar BILLING del usuario logueado.
// No colisiona con nada existente.

if (!function_exists('mt_get_current_user_profile')) {
  function mt_get_current_user_profile(): array
  {
    if (!is_user_logged_in())
      return array('ok' => false, 'msg' => 'Not logged in');
    $uid = get_current_user_id();
    $u = wp_get_current_user();

    $data = array(
      'first_name' => get_user_meta($uid, 'first_name', true),
      'last_name' => get_user_meta($uid, 'last_name', true),
      'email' => $u ? $u->user_email : '',
      'billing_address_1' => get_user_meta($uid, 'billing_address_1', true),
      'billing_city' => get_user_meta($uid, 'billing_city', true),
      'billing_state' => get_user_meta($uid, 'billing_state', true),
      'billing_postcode' => get_user_meta($uid, 'billing_postcode', true),
      'billing_country' => get_user_meta($uid, 'billing_country', true),
      'billing_phone' => get_user_meta($uid, 'billing_phone', true),
    );
    return array('ok' => true, 'data' => $data);
  }
}

if (!function_exists('mt_update_current_user_billing')) {
  function mt_update_current_user_billing(array $in): array
  {
    if (!is_user_logged_in())
      return array('ok' => false, 'msg' => 'Not logged in');
    $uid = get_current_user_id();

    $fields = array(
      'billing_address_1',
      'billing_city',
      'billing_state',
      'billing_postcode',
      'billing_country',
      'billing_phone'
    );
    foreach ($fields as $k) {
      if (array_key_exists($k, $in)) {
        $v = is_string($in[$k]) ? wp_strip_all_tags($in[$k]) : '';
        update_user_meta($uid, $k, $v);
      }
    }
    return array('ok' => true);
  }
}

// Obtener perfil (si luego quieres refrescar dinámicamente desde el front)
add_action('wp_ajax_mt_get_profile', function () {
  check_ajax_referer('mt_profile_nonce', 'nonce');
  $res = mt_get_current_user_profile();
  if (!$res['ok'])
    wp_send_json_error(array('msg' => $res['msg']), 401);
  wp_send_json_success($res['data']);
});

// Guardar solo BILLING
add_action('wp_ajax_mt_save_billing_profile', function () {
  check_ajax_referer('mt_profile_nonce', 'nonce');
  if (!is_user_logged_in())
    wp_send_json_error(array('msg' => 'Not logged in'), 401);

  $payload = array(
    'billing_address_1' => $_POST['billing_address_1'] ?? '',
    'billing_city' => $_POST['billing_city'] ?? '',
    'billing_state' => $_POST['billing_state'] ?? '',
    'billing_postcode' => $_POST['billing_postcode'] ?? '',
    'billing_country' => $_POST['billing_country'] ?? '',
    'billing_phone' => $_POST['billing_phone'] ?? '',
  );

  // Validación mínima server
  foreach (array_keys($payload) as $k) {
    if (empty(trim((string) $payload[$k]))) {
      wp_send_json_error(array('msg' => "Missing field: $k"), 400);
    }
  }

  $r = mt_update_current_user_billing($payload);
  if (!$r['ok'])
    wp_send_json_error(array('msg' => $r['msg'] ?? 'Error'), 500);

  wp_send_json_success(array('msg' => 'Saved'));
});

// inc/mt-accounts-helpers.php
if (!function_exists('mt_money_fmt')) {
  function mt_money_fmt($n)
  {
    $s = ($n < 0) ? '-' : '';
    return $s . '$' . number_format(abs((float) $n), 2);
  }
}

if (!function_exists('mt_parse_open_time')) {
  function mt_parse_open_time($openTime)
  {
    try {
      $dt = !empty($openTime)
        ? new DateTime($openTime, new DateTimeZone('UTC'))
        : new DateTime('now', new DateTimeZone('UTC'));
    } catch (Exception $e) {
      $dt = new DateTime('now', new DateTimeZone('UTC'));
    }
    return [
      'iso' => $dt->format('Y-m-d'),
      'label' => $dt->format('m/d/Y'),
    ];
  }
}