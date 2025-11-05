<?php
// File: public_html/wp-content/themes/megatrader-addons/inc/mt-accounts-helpers.php
defined('ABSPATH') || exit;

// ==== Cache utils (transients + memo) ====
if (!function_exists('mt_cache_key')) {
  function mt_cache_key(string $ns, array $parts): string {
    return $ns . ':' . md5(implode('|', array_map('strval', $parts)));
  }
}
if (!function_exists('mt_cache_get')) {
  function mt_cache_get(string $key) { return get_transient($key); }
}
if (!function_exists('mt_cache_set')) {
  function mt_cache_set(string $key, $value, int $ttl = 120) { return set_transient($key, $value, $ttl); }
}


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
      $accounts = [$accounts];
    }


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


      // === SIEMPRE: resolver byId para obtener 'order' (y plataforma si faltara)
      if ($id !== '' && function_exists('mt_accounts_resolve_account_by_id')) {
        try {
          $full = mt_accounts_resolve_account_by_id($id);
          if (is_array($full)) {

            $order = (string) ($full['order'] ?? $order);


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
        } catch (\Throwable $e) {
        }
      }
      $subscriptionId = '';
      $user_id = get_current_user_id();
      if (is_numeric($order) && (int) $order > 0 && function_exists('mt_subscription_id_for_order')) {
        $subscriptionId = (string) mt_subscription_id_for_order((int) $order, (int) $user_id);
      }
      $hasSubscription = ($subscriptionId !== '');


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
        'subscriptionId' => $subscriptionId,
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
    $map = [];
    if (class_exists('Label') && defined('Label::PLAN_RULES_URLS')) {
      $map = Label::PLAN_RULES_URLS;
    }
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
      'highestProfitDay' => $metrics['consistencyTopDayRealizedProfit'] ?? null,
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
      'targetAmount' => mt__get($account, ['payout', 'payoutCycle', 'targetAmountFromStartBalance']),
      'consistencyCurrentTopDayProfit' => $metrics['consistencyCurrentTopDayProfit'] ?? null,

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

    $raw = trim(wp_unslash($raw));
    if ($raw !== '' && substr($raw, 0, 3) === "\xEF\xBB\xBF")
      $raw = substr($raw, 3);
    if ($raw !== '')
      $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    $data = json_decode($raw, true);
    if (!is_array($data))
      $data = json_decode(trim(wp_strip_all_tags($raw)), true);

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
    static $memo = []; // petición-local
    $accountId = trim((string)$accountId);
    if ($accountId === '') return null;

    // 1) memo de request
    if (isset($memo[$accountId])) return $memo[$accountId];

    // 2) transient corto (reduce golpes a la API)
    $tkey = mt_cache_key('mt:acc_by_id', [$accountId]);
    $cached = mt_cache_get($tkey);
    if (is_array($cached)) { $memo[$accountId] = $cached; return $cached; }

    $acc = null;

    // 3) API directa
    if (class_exists('MT_Api') && method_exists('MT_Api','fetch_account_by_id')) {
      try { $acc = MT_Api::fetch_account_by_id($accountId); } catch (\Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('[MT][acc_resolve][by_id] '.$e->getMessage());
      }
    }

    // 4) Fallback: shortcode + pick
    if (!$acc && function_exists('mt_accounts_fetch_account_json_by_shortcode')) {
      try {
        $json = mt_accounts_fetch_account_json_by_shortcode($accountId, 1, 1);
        if (function_exists('mt_accounts_pick_account_from_json')) {
          $acc = mt_accounts_pick_account_from_json($json, $accountId);
        }
      } catch (\Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('[MT][acc_resolve][shortcode] '.$e->getMessage());
      }
    }

    $acc = (is_array($acc) && !empty($acc)) ? $acc : null;

    // 5) guarda 90s
    if ($acc) mt_cache_set($tkey, $acc, 90);
    $memo[$accountId] = $acc;
    return $acc;
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
      return $cls_pos; // Meta no numérica: siempre cumplido si V > 0

    $t = (float) $target;

    // Meta negativa/cero: siempre cumplido si V > 0
    if ($t <= 0)
      return $cls_pos;

    // Meta positiva:
    if ($v > $t)
      return $cls_pos; // cumplido

    if (abs($v - $t) < 1e-9)
      return $cls_pos; // igual (cumplido)

    return $cls_neutral; // V > 0, pero V < T
  }
}

// Construye links de plataforma (web/app/icono) desde un código (mt4|mt5|ctrader, etc.)
if (!function_exists('mt_platform_links_by_code')) {
  function mt_platform_links_by_code(string $code)
  {
    $code = strtolower(trim($code));
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
}

// Construye credenciales desde un objeto/array "cuenta" (JSON/API)
if (!function_exists('mt_accounts_build_credentials_from_account')) {
  function mt_accounts_build_credentials_from_account($account)
  {
    $a = is_object($account) ? json_decode(json_encode($account), true) : (array) $account;
    $login = $a['login'] ?? ($a['credentials']['login'] ?? ($a['accountNumber'] ?? ($a['tradingLogin'] ?? '')));
    $server = $a['server'] ?? ($a['credentials']['server'] ?? '');
    $pwd = $a['password'] ?? ($a['credentials']['password'] ?? '');
    $platform_code = strtolower($a['platform']['code'] ?? ($a['platform_code'] ?? ''));
    $platform_name = $a['platform']['name'] ?? ($a['platform_name'] ?? '');
    $links = mt_platform_links_by_code($platform_code);

    return [
      'login' => (string) $login,
      'password' => (string) $pwd,
      'server' => (string) $server,
      'links' => $links,
      'platform' => [
        'code' => $platform_code,
        'name' => $platform_name ?: ($links['name'] ?? 'Trading Platform'),
        'icon_class' => $links['icon_class'],
      ],
      // compatibilidad
      'platformName' => $platform_name ?: ($links['name'] ?? 'Trading Platform'),
      'iconClass' => $links['icon_class'],
      'accountId' => (string) ($a['id'] ?? $login),
    ];
  }
}

// Devuelve todas las credenciales para pintar en la UI (Modal de cuenta)
if (!function_exists('mt_accounts_prepare_credentials_ui')) {
  function mt_accounts_prepare_credentials_ui(string $account_id)
  {
    $account_id = trim($account_id);
    if ($account_id === '') {
      return [
        'login' => '', 'password' => '', 'server' => '',
        'links' => ['web' => '', 'appstore' => '', 'playstore' => ''],
        'platform' => ['code' => '', 'name' => 'Trading Platform', 'icon_class' => ''],
      ];
    }

    // 2) Consultar la API usando TU helper del shortcode (mismo flujo que performance)
    // IMPORTANT: pasar los "atts" como array, NO el id suelto.
    $json = mt_accounts_fetch_account_json_by_shortcode($account_id, 1, 10);

    // 3) Elegir la cuenta pedida y construir credenciales
    $acc = function_exists('mt_accounts_pick_account_from_json') ? mt_accounts_pick_account_from_json($json, $account_id) : null;
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
}

// === AJAX: devuelve el HTML de template-parts/account/account-data por accountId ===
if (!function_exists('mt_accounts_ajax_account_data')) {
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
}

// ================= Email: normalizar, validar y preparar para API ================
if (!function_exists('mt_normalize_email')) {
  /**
   * Trim + lowercase y normaliza espacios raros.
   */
  function mt_normalize_email(?string $raw): string
  {
    $raw = (string) $raw;
    $email = strtolower(trim($raw));
    // Reemplazar diferentes tipos de espacios con un espacio simple (opcionalmente)
    $email = preg_replace('/\s+/', ' ', $email);
    // Eliminar espacios de nuevo
    return trim($email);
  }
}

if (!function_exists('mt_validate_email')) {
  /**
   * Valida un email normalizado. Usa WordPress helper.
   */
  function mt_validate_email(?string $normalized): bool
  {
    return is_email($normalized);
  }
}

if (!function_exists('mt_email_for_api')) {
  /**
   * Codifica el email para pasar como parámetro de URL (slug/query param).
   * Solo codifica la parte local si contiene símbolos conflictivos.
   * @return string
   */
  function mt_email_for_api(?string $normalized): string
  {
    $normalized = (string) $normalized;
    if ($normalized === '')
      return '';
    $parts = explode('@', $normalized, 2);
    if (count($parts) !== 2)
      return urlencode($normalized); // No tiene '@', codificar todo

    [$local, $domain] = $parts;

    // Solo codificar la parte local si existen esos símbolos (evita doble encode)
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
    // $ascii = function_exists('idn_to_ascii') ? idn_to_ascii($normalized, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46) : $normalized;
    // $normalized = (string) $ascii;

    $apiSafe = mt_email_for_api($normalized);

    return [
      'ok' => true,
      'email' => $normalized,
      'api' => $apiSafe,
      'error' => '',
    ];
  }
}


// ============== MISC WOOCOMMERCE/PRODUCT HELPERS ======================
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
   * - agreementURL (string|null)
   * - agreementSigned (bool|null)
   * - agreementStatus (string|null) // valor original (p.ej., "ACTIVE")
   * - agreementStatusBool (bool|null) // derivado de status string (p.ej., true si "ACTIVE")
   *
   * @param string $emailUrlEncoded
   * @return array
   */
  function mt_get_agreement_status_by_email(string $emailUrlEncoded): array
  {
    $emailUrlEncoded = trim((string) $emailUrlEncoded);
    if ($emailUrlEncoded === '' || !function_exists('do_shortcode')) {
      return [
        'agreementURL' => null,
        'agreementSigned' => null,
        'agreementStatus' => null,
        'agreementStatusBool' => null,
      ];
    }
    $shortcode = sprintf(
      '[mega_agreement_status email="%s" output="json"]',
      esc_attr($emailUrlEncoded)
    );
    $raw = do_shortcode($shortcode);

    if (!is_string($raw) || $raw === '') {
      return [
        'agreementURL' => null,
        'agreementSigned' => null,
        'agreementStatus' => null,
        'agreementStatusBool' => null,
      ];
    }
    $raw = trim(wp_unslash($raw));
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
    }

    return [
      'agreementURL' => $agreement_url,
      'agreementSigned' => $signed,
      'agreementStatus' => $status_str,
      'agreementStatusBool' => $status_bool,
    ];

  }
}

// === AJAX: devuelve el status de la cuenta (usado en el Account Picker) ===
if (!function_exists('mt_accounts_ajax_status')) {
  add_action('wp_ajax_mt_accounts_status', 'mt_accounts_ajax_status');
  add_action('wp_ajax_nopriv_mt_accounts_status', 'mt_accounts_ajax_status');
  function mt_accounts_ajax_status()
  {
    check_ajax_referer('mt-acc-nonce', 'nonce');
    $accountId = isset($_POST['accountId']) ? sanitize_text_field((string) $_POST['accountId']) : '';
    if ($accountId === '') {
      wp_send_json_error(['message' => 'Missing accountId']);
    }
    $found = null;
    if (function_exists('mt_accounts_resolve_account_by_id')) {
      try {
        $found = mt_accounts_resolve_account_by_id($accountId);
      } catch (\Throwable $e) {
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
      } catch (\Throwable $e) {
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
      } catch (\Throwable $e) {
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
}


// ============== DAILY JOURNAL HELPERS ======================

/* === DAILY JOURNAL: obtener el conteo de trades para un día EST (con cutoff) === */
if (!function_exists('mt_get_trades_count_for_day')) {
  /**
   * @param string $accountId
   * @param string $day_iso_eastern YYYY-MM-DD del día EST a contar (con corte a las 18:00 UTC)
   * @param array $allTradesCache Cache de todos los trades [accountId => trades]
   * @return int
   */
  function mt_get_trades_count_for_day(string $accountId, string $day_iso_eastern, array $allTradesCache = []): int
  {
    if ($accountId === '')
      return 0;
    $cache = $allTradesCache;
    if (!isset($cache[$accountId])) {
      // Intenta obtener los trades del shortcode
      if (!function_exists('mt_trades_fetch_by_shortcode'))
        return 0;
      $chunk = mt_trades_fetch_by_shortcode($accountId, 1, 10000); // Max fetch
      $cache[$accountId] = is_array($chunk['data'] ?? null) ? $chunk['data'] : [];
    }

    if (!isset($cache[$accountId])) {
      return 0;
    }
    $trades = $cache[$accountId];
    if (empty($trades))
      return 0;

    $cnt = 0;
    foreach ($trades as $t) {
      $ct = isset($t['closeTime']) ? (string) $t['closeTime'] : '';
      if ($ct === '')
        continue;
      $ct_ymd = function_exists('mt_utc_to_eastern_ymd_cutoff') ? mt_utc_to_eastern_ymd_cutoff($ct, 18) : (function_exists('mt_utc_to_eastern_ymd') ? mt_utc_to_eastern_ymd($ct) : substr($ct, 0, 10));
      if ($ct_ymd === $day_iso_eastern) {
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
    $accountId = (string) $accountId;
    $rows = [];
    if ($accountId === '' || !function_exists('mt_trades_fetch_by_shortcode')) {
      if (defined('WP_DEBUG') && WP_DEBUG)
        error_log('[DJ] early-exit: missing accountId or mt_trades_fetch_by_shortcode');
      return ['rows' => $rows, 'per_page' => (int) $perPage];
    }

    // ==== Utilidades ====
    $tzUTC = new DateTimeZone('UTC');
    $tzNY = new DateTimeZone('America/New_York');
    $toNY = function (?string $iso) use ($tzUTC, $tzNY): ?DateTime {
      if (!$iso)
        return null;
      try {
        $dt = new DateTime($iso, $tzUTC);
        $dt->setTimezone($tzNY);
        return $dt;
      } catch (\Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG)
          error_log('[DJ] toNY error: ' . $e->getMessage());
        return null;
      }
    };


    // ==== 1) Fetch (trae todos, asumiendo max 10000 en 10 páginas) ====
    $all = [];
    $pageFetch = 1;
    $perPageFetch = 1000; // max por llamada

    // Cuidado con bucle infinito, max 10 calls
    for ($i = 0; $i < 10; $i++) {
      $chunk = mt_trades_fetch_by_shortcode($accountId, $pageFetch, $perPageFetch);
      $items = is_array($chunk['data'] ?? null) ? $chunk['data'] : [];
      foreach ($items as $it) {
        if (is_array($it))
          $all[] = $it;
      }
      $pagesCount = isset($chunk['meta']['pagesCount']) ? (int) $chunk['meta']['pagesCount'] : null;
      if ($pagesCount && $pageFetch >= $pagesCount)
        break;
      if (count($items) < $perPageFetch)
        break;
      $pageFetch++;
    }
    if (defined('WP_DEBUG') && WP_DEBUG)
      error_log('[DJ] total trades fetched=' . count($all));

    if (empty($all)) {
      return ['rows' => $rows, 'per_page' => (int) $perPage];
    }

    // ==== 2) Agrupar por día EST usando closeTime ====
    $byDay = [];
    foreach ($all as $t) {
      $cIso = isset($t['closeTime']) ? (string) $t['closeTime'] : null;
      $oIso = isset($t['openTime']) ? (string) $t['openTime'] : null;
      $closeNY = $toNY($cIso);
      $openNY = $toNY($oIso);

      if (!$closeNY || !$openNY) {
        if (defined('WP_DEBUG') && WP_DEBUG)
          error_log('[DJ] skip trade (bad times) open=' . $oIso . ' close=' . $cIso);
        continue;
      }

      // Día clave = closeTime en EST (YYYY-MM-DD)
      // Antes: $day = $closeNY->format('Y-m-d');
      $day = function_exists('mt_utc_to_eastern_ymd_cutoff') ? mt_utc_to_eastern_ymd_cutoff((string) $t['closeTime'], 18) : $closeNY->format('Y-m-d'); // fallback sin cutoff

      $pnl = (float) ($t['pnl'] ?? 0);
      $lots = (int) ($t['lots'] ?? 0);
      $fees = (float) ($t['fees'] ?? 0);
      $durationSecs = $closeNY->getTimestamp() - $openNY->getTimestamp();

      if (!isset($byDay[$day])) {
        $byDay[$day] = [
          'date' => $day,
          'net' => 0.0,
          'fees' => 0.0,
          'hi' => $pnl, // max pnl
          'lo' => $pnl, // min pnl
          'trades' => 0,
          'ct' => 0, // contract count
          'wins' => 0,
          'losses' => 0,
          'sumWin' => 0.0,
          'sumLoss' => 0.0,
          'durWinSecs' => 0,
          'durLossSecs' => 0,
          '_seq' => [], // para max consecutivos
        ];
      }

      $D = &$byDay[$day];
      $D['net'] += $pnl;
      $D['fees'] += $fees;
      $D['trades'] += 1;
      $D['ct'] += $lots;
      $D['hi'] = max($D['hi'], $pnl);
      $D['lo'] = min($D['lo'], $pnl);

      if ($pnl > 0) {
        $D['wins'] += 1;
        $D['sumWin'] += $pnl;
        $D['durWinSecs'] += $durationSecs;
      } elseif ($pnl < 0) {
        $D['losses'] += 1;
        $D['sumLoss'] += $pnl;
        $D['durLossSecs'] += $durationSecs;
      }

      // Para max consecutivos:
      $D['_seq'][] = [
        'pnl' => $pnl,
        'openTs' => $openNY->getTimestamp(),
      ];
      unset($D);
    }

    // ==== 3) Reducir a filas (usar $agg para no reusar $D por referencia) ====
    foreach ($byDay as $day => $agg) {
      usort($agg['_seq'], function ($a, $b) {
        return $a['openTs'] <=> $b['openTs'];
      });

      $curW = $curL = $maxW = $maxL = 0;
      foreach ($agg['_seq'] as $e) {
        if ($e['pnl'] > 0) {
          $curW += 1;
          $curL = 0;
        } elseif ($e['pnl'] < 0) {
          $curL += 1;
          $curW = 0;
        } else {
          $curW = 0;
          $curL = 0;
        }
        $maxW = max($maxW, $curW);
        $maxL = max($maxL, $curL);
      }

      $wins = (int) $agg['wins'];
      $loss = (int) $agg['losses'];
      $tot = max(1, (int) $agg['trades']);
      $awin = $wins > 0 ? ($agg['sumWin'] / $wins) : '-';
      $aloss = $loss > 0 ? ($agg['sumLoss'] / $loss) : '-';

      $winPct = round(($wins * 100.0) / $tot, 2);
      $losPct = round(100.0 - $winPct, 2);

      $avgWinDur = $wins > 0 ? (int) floor($agg['durWinSecs'] / $wins) : 0;
      $avgLosDur = $loss > 0 ? (int) floor($agg['durLossSecs'] / $loss) : 0;

      $rows[] = [
        'date' => $day,
        'openTime' => $day,
        'net' => (float) $agg['net'],
        'fees' => (float) $agg['fees'],
        'hi' => (float) $agg['hi'],
        'lo' => (float) $agg['lo'],
        'ct' => (int) $agg['ct'],
        'trades' => $tot,
        'awin' => is_numeric($awin) ? (float) $awin : $awin,
        'aloss' => is_numeric($aloss) ? (float) $aloss : $aloss,
        'win' => $winPct,
        'loss' => $losPct,
        'max' => "W{$maxW}/L{$maxL}",
        'dur' => mt_format_duration($avgWinDur + $avgLosDur),
        'durWin' => mt_format_duration($avgWinDur),
        'durLoss' => mt_format_duration($avgLosDur),
      ];
    }

    // ==== 4) Paginación (si aplica) ====
    $offset = ($page - 1) * $perPage;
    $total = count($rows);
    $rows = array_slice($rows, $offset, $perPage);
    $totalPages = (int) ceil($total / $perPage);

    return [
      'rows' => $rows,
      'per_page' => (int) $perPage,
      'total_rows' => $total,
      'total_pages' => $totalPages,
      'current_page' => (int) $page,
    ];
  }
}

// Formato HH:MM:SS para duraciones
if (!function_exists('mt_format_duration')) {
  function mt_format_duration(int $seconds): string
  {
    if ($seconds < 0)
      return '—';
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $h, $m, $s);
  }
}

/* === DAILY JOURNAL: Generar el HTML de las filas para la tabla === */
if (!function_exists('mt_accounts_daily_journal_html')) {
  function mt_accounts_daily_journal_html(array $rows, int $per_page, $acc_id): string
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
    $fmt_duration = function ($v) {
      return (is_numeric($v) && (int) $v >= 0) ? mt_format_duration((int) $v) : (string) $v;
    };
    ob_start();

    foreach ($rows as $i => $r) {
      $page = (int) floor($i / max(1, $per_page)) + 1;

      // --- NUEVO: la fecha ya viene "cerrada" con cutoff en $r['openTime'] (o 'date').
      // No dependemos del TZ del servidor para formatear; usamos el string YYYY-MM-DD tal cual.
      $ymd = (string) ($r['openTime'] ?? $r['date'] ?? '');
      $day_iso = substr($ymd, 0, 10);

      // Formato label MM/DD/YYYY de forma estable (UTC, sin efectos del TZ del server)
      if ($day_iso !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $day_iso)) {
        // gmdate sobre timestamp "naive" 00:00:00, suficiente para formatear la etiqueta
        $day_ts = strtotime($day_iso . ' 00:00:00 UTC');
        $day_label = $day_ts ? gmdate('m/d/Y', $day_ts) : '-';
      } else {
        $day_label = '-';
      }

      $fb = ($acc_id && $day_iso && function_exists('mt_get_daily_feedback')) ? mt_get_daily_feedback($user_id, (int) $acc_id, $day_iso) : null;
      $has_fb = !empty($fb);
      $mood = $has_fb ? (int) ($fb['mood'] ?? 0) : 0;
      $notes = $has_fb ? (string) ($fb['notes'] ?? '') : '';

      $net = $r['net'] ?? null;
      $net_class = mt_value_color_class($net, 'text-default');

      $mood_icon = ($mood === 1) ? 'mt-icon_happy' : (($mood === 2) ? 'mt-icon_neutral' : (($mood === 3) ? 'mt-icon_sad' : 'mt-icon_notes'));
      $mood_class = ($mood === 1) ? 'text-success' : (($mood === 3) ? 'text-error' : (($mood === 2) ? 'text-default' : 'text-gray'));

      ?>
            <div class="dj-row" data-page="<?php echo esc_attr($page); ?>" data-date="<?php echo esc_attr($day_iso); ?>">
                <div class="dj-cell">
                    <span class="dj-feedback-btn <?php echo esc_attr($mood > 0 ? 'is-active' : ''); ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#daily-journal-modal"
                        data-account-id="<?php echo esc_attr($acc_id); ?>"
                        data-date-iso="<?php echo esc_attr($day_iso); ?>"
                        data-mood="<?php echo esc_attr($mood); ?>"
                        data-notes="<?php echo esc_attr($notes); ?>"
                        role="button"
                        title="<?php echo esc_attr($notes ?: 'Add feedback'); ?>">
                        <i class="<?php echo esc_attr($mood_icon); ?> <?php echo esc_attr($mood_class); ?>" aria-hidden="true"></i>
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
                <div class="dj-cell is-right"><?php echo esc_html($fmt_duration($r['dur'] ?? '-')); ?></div>
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
      'billing_address_1' => get_user_meta($uid, 'billing_address_1', true),
      'billing_address_2' => get_user_meta($uid, 'billing_address_2', true),
      'billing_city' => get_user_meta($uid, 'billing_city', true),
      'billing_state' => get_user_meta($uid, 'billing_state', true),
      'billing_postcode' => get_user_meta($uid, 'billing_postcode', true),
      'billing_country' => get_user_meta($uid, 'billing_country', true),
      'billing_phone' => get_user_meta($uid, 'billing_phone', true),
      'billing_email' => get_user_meta($uid, 'billing_email', true),
      'email' => $u->user_email,
    );
    return array('ok' => true, 'data' => $data);
  }
}

if (!function_exists('mt_update_current_user_billing')) {
  function mt_update_current_user_billing(array $data): array
  {
    if (!is_user_logged_in())
      return array('ok' => false, 'msg' => 'Not logged in');
    $uid = get_current_user_id();

    $fields = [
      'first_name',
      'last_name',
      'billing_address_1',
      'billing_address_2',
      'billing_city',
      'billing_state',
      'billing_postcode',
      'billing_country',
      'billing_phone',
      'billing_email'
    ];
    // Saneamiento de datos
    $sane = [];
    foreach ($fields as $field) {
      $value = $data[$field] ?? null;
      if ($value !== null) {
        $sane[$field] = sanitize_text_field($value);
      }
    }

    // Validación mínima
    if (empty($sane['first_name']))
      return array('ok' => false, 'msg' => 'Missing first name');
    if (empty($sane['last_name']))
      return array('ok' => false, 'msg' => 'Missing last name');
    if (!is_email($sane['billing_email'] ?? ''))
      return array('ok' => false, 'msg' => 'Invalid billing email');

    // Actualizar nombre (no se usa billing_first/last en el perfil, sino first/last_name)
    update_user_meta($uid, 'first_name', $sane['first_name']);
    update_user_meta($uid, 'last_name', $sane['last_name']);

    // Actualizar campos de facturación
    foreach ($sane as $key => $value) {
      if (strpos($key, 'billing_') === 0) {
        update_user_meta($uid, $key, $value);
      }
    }

    return array('ok' => true);
  }
}

if (!function_exists('mt_accounts_ajax_profile_get')) {
  add_action('wp_ajax_mt_accounts_profile_get', 'mt_accounts_ajax_profile_get');
  function mt_accounts_ajax_profile_get()
  {
    check_ajax_referer('mt-acc-nonce', 'nonce');
    $r = mt_get_current_user_profile();
    if (!$r['ok'])
      wp_send_json_error(array('msg' => $r['msg'] ?? 'Error'), 401);
    wp_send_json_success($r['data']);
  }
}

if (!function_exists('mt_accounts_ajax_profile_save')) {
  add_action('wp_ajax_mt_accounts_profile_save', function () {
    check_ajax_referer('mt-acc-nonce', 'nonce');
    $payload = array(
      'first_name' => $_POST['first_name'] ?? '',
      'last_name' => $_POST['last_name'] ?? '',
      'billing_address_1' => $_POST['billing_address_1'] ?? '',
      'billing_address_2' => $_POST['billing_address_2'] ?? '',
      'billing_city' => $_POST['billing_city'] ?? '',
      'billing_state' => $_POST['billing_state'] ?? '',
      'billing_postcode' => $_POST['billing_postcode'] ?? '',
      'billing_country' => $_POST['billing_country'] ?? '',
      'billing_phone' => $_POST['billing_phone'] ?? '',
      'billing_email' => $_POST['billing_email'] ?? '',
    );
    // Validación mínima server
    foreach (array_keys($payload) as $k) {
      if (strpos($k, 'address_2') === false && empty(trim((string) $payload[$k]))) {
        wp_send_json_error(array('msg' => "Missing field: $k"), 400);
      }
    }
    $r = mt_update_current_user_billing($payload);
    if (!$r['ok'])
      wp_send_json_error(array('msg' => $r['msg'] ?? 'Error'), 500);
    wp_send_json_success(array('msg' => 'Saved'));
  });
}

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
      $dt = !empty($openTime) ? new DateTime($openTime, new DateTimeZone('UTC')) : new DateTime('now', new DateTimeZone('UTC'));
    } catch (Exception $e) {
      $dt = new DateTime('now', new DateTimeZone('UTC'));
    }
    return [
      'iso' => $dt->format('Y-m-d'),
      'label' => $dt->format('m/d/Y'),
    ];
  }
}

// --- Dada una orden y el usuario actual, devuelve el ID de suscripción asociada (o '' si no hay)
if (!function_exists('mt_subscription_id_for_order')) {
  function mt_subscription_id_for_order(int $order_id, int $user_id = 0): string
  {
    if ($order_id <= 0)
      return '';

    // Preferir validar que la orden sea del usuario si $user_id viene
    if ($user_id > 0) {
      $order = function_exists('wc_get_order') ? wc_get_order($order_id) : null;
      if (!$order)
        return '';
      $belongs = ((int) $order->get_user_id() === (int) $user_id);
      if (!$belongs)
        return '';
    }

    // WooCommerce Subscriptions
    if (function_exists('wcs_get_subscriptions_for_order')) {
      $subscriptions = wcs_get_subscriptions_for_order($order_id, array('order_type' => 'any'));
      if (!empty($subscriptions)) {
        // Devuelve el primer ID encontrado
        foreach ($subscriptions as $subscription) {
          return (string) $subscription->get_id();
        }
      }
    }
    return '';
  }
}

// ============== SHORTCODE UTILS (desde el final de v3.x) ======================

if (!function_exists('mt_shortcode_extract_json_data')) {
  /**
   * Ejecuta un shortcode y trata de extraer el JSON de forma robusta.
   * Elimina BOM, decode de entidades, y soporta JSON crudo o JSON limpiado de tags.
   * @param string $shortcode
   * @return array|null
   */
  function mt_shortcode_extract_json_data(string $shortcode): ?array
  {
    if (!function_exists('do_shortcode'))
      return null;

    $raw = do_shortcode($shortcode);
    if (!is_string($raw) || $raw === '')
      return null;

    $raw = trim(wp_unslash($raw));
    if ($raw !== '' && substr($raw, 0, 3) === "\xEF\xBB\xBF")
      $raw = substr($raw, 3);
    if ($raw !== '')
      $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // 1. Intento con JSON crudo
    $data = json_decode($raw, true);
    if (is_array($data))
      return $data;

    // 2. Fallback limpiando tags (por si el shortcode envuelve el JSON en p/divs)
    $data = json_decode(trim(wp_strip_all_tags($raw)), true);
    if (is_array($data))
      return $data;

    return null;
  }
}