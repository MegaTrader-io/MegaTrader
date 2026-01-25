<?php
// File: public_html/wp-content/themes/megatrader-addons/inc/mt-accounts-helpers.php
defined('ABSPATH') || exit;

/* ======================= Cache utils ======================= */
if (!function_exists('mt_cache_key')) {
  function mt_cache_key(string $ns, array $parts): string
  {
    return $ns . ':' . md5(implode('|', array_map('strval', $parts)));
  }
}
if (!function_exists('mt_cache_get')) {
  function mt_cache_get(string $key)
  {
    return get_transient($key);
  }
}
if (!function_exists('mt_cache_set')) {
  function mt_cache_set(string $key, $value, int $ttl = 120)
  {
    return set_transient($key, $value, $ttl);
  }
}

/* ======================= Core: Accounts ======================= */
class MT_Accounts
{
  /* ---- ÚNICA FUENTE DE LOGOS ---- */
  private const DEFAULT_LOGO = '/wp-content/uploads/2025/07/Stylecolor-Sizelg.svg';
  private const PLATFORM_LOGOS = [
    'megatrader' => self::DEFAULT_LOGO,
    'ninjatrader' => '/wp-content/uploads/2025/02/icon_ninjatrader.svg',
    'tradovate' => '/wp-content/uploads/2025/02/icon_tradovate.svg',
    'quantower' => '/wp-content/uploads/2025/02/icon_quantower.svg',
  ];

  /* ---- Helpers de estado/normalización ---- */
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
    if (in_array($s, ['rejected', 'closed', 'disabled', 'cancelled', 'failed', 'expired'], true))
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

  /* ---- Logos centralizados ---- */
public static function platform_logo($platformRaw): string
{
  $raw = trim((string) $platformRaw);

  // 1) Intento #1: resolver por API key vía taxonomy/term (lo que tú quieres)
  if (
    $raw !== ''
    && function_exists('mt_platform_term_by_api_key')
    && function_exists('mt_platform_icon_url_from_term')
  ) {
    $t = mt_platform_term_by_api_key($raw);
    if ($t && !empty($t->term_id)) {
      $u = (string) mt_platform_icon_url_from_term($t);
      if ($u !== '') return $u;
    }
  }

  // 2) Intento #2: compatibilidad con datos viejos que traen nombre “NinjaTrader”, etc.
  $key = self::norm($raw);
  if ($key !== '' && isset(self::PLATFORM_LOGOS[$key])) {
    return self::PLATFORM_LOGOS[$key];
  }

  // 3) Último fallback
  return self::DEFAULT_LOGO;
}


  /* ---- Etiquetas de programa ---- */
  public static function parse_program_label(string $label, $startingBalance = null): array
  {
    $head = ($p = strpos($label, '|')) !== false ? substr($label, 0, $p) : $label;
    $head = trim($head);
    $size = '';
    $name = $head !== '' ? $head : 'Account';
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

  /* ---- Builder de UI (sin mapas locales duplicados) ---- */
  public static function prepare_ui(array $accounts, $forceFullData = false, $cookie_selected_id = null): array
  {
    if (isset($accounts['data']))
      $accounts = is_array($accounts['data']) ? $accounts['data'] : [];
    elseif (isset($accounts['results']))
      $accounts = $accounts['results'];
    elseif (isset($accounts['items']))
      $accounts = $accounts['items'];
    elseif (isset($accounts['id']))
      $accounts = [$accounts];

    if (!empty($accounts) && array_keys($accounts) !== range(0, count($accounts) - 1))
      $accounts = array_values($accounts);

    $pool = array_values(array_filter((array) $accounts, 'is_array'));
    usort($pool, function ($a, $b) {
      $ta = strtotime((string) ($a['createdAt'] ?? '')) ?: 0;
      $tb = strtotime((string) ($b['createdAt'] ?? '')) ?: 0;
      return $tb <=> $ta;
    });
    if (empty($pool))
      return ['current' => null, 'accounts' => []];

    $cur = $pool[0];
    foreach ($pool as $row) {
      if (self::is_active_status($row['status'] ?? '')) {
        $cur = $row;
        break;
      }
    }

    if (!$cookie_selected_id) {
      $cookie_selected_id = $cur['id'];
    }

    $mapAccount = function (array $acc, bool $fullData = false) use ($cookie_selected_id, $forceFullData) {
      $id = (string) ($acc['id'] ?? '');
      $fullData = $forceFullData || ($cookie_selected_id && $id == $cookie_selected_id) || $fullData;
      $plabel = (string) ($acc['program']['label'] ?? ($acc['program']['description'] ?? 'Account'));
      $sb = $acc['program']['startingBalance'] ?? null;
      [$size, $name] = MT_Accounts::parse_program_label($plabel, $sb);

      // platform text
      $platformRaw = '';
      if (isset($acc['platform'])) {
        $platformRaw = is_array($acc['platform']) ? (string) ($acc['platform']['platform'] ?? $acc['platform']['name'] ?? '') : (string) $acc['platform'];
      }
      if ($platformRaw === '' && isset($acc['program']['platform']))
        $platformRaw = (string) $acc['program']['platform'];
      $logo = MT_Accounts::platform_logo($platformRaw);

      $status = (string) ($acc['status'] ?? '');
      $rules = is_array($acc['rules'] ?? null) ? $acc['rules'] : [];
      $platAccountId = $acc['accountNr'];
      $order = (string) ($acc['order'] ?? '');

      $needById = ($order === '' || $platformRaw === '');
      if ($fullData && $id !== '' && $needById && function_exists('mt_accounts_resolve_account_by_id')) {
        try {
          $full = mt_accounts_resolve_account_by_id($id);
          if (is_array($full)) {
            if ($order === '')
              $order = (string) ($full['order'] ?? $order);
            if ($platformRaw === '' && isset($full['platform']) && is_array($full['platform'])) {
              $platformRaw = (string) ($full['platform']['platform'] ?? $full['platform']['name'] ?? $platformRaw);
              $logo = MT_Accounts::platform_logo($platformRaw);
            }
          }
        } catch (\Throwable $e) {
        }
      }

      // programType badge
      // programType badge (usa accountType cuando viene del API)
      $ptypeLabel = '';
      $ptypeClass = 'badge-mega-default';

      $accountType = strtolower(trim((string) ($acc['accountType'] ?? '')));

      if ($accountType !== '') {
        // "evaluation" | "funded" → badge por accountType
        $ptypeLabel = ucfirst($accountType);
        $key = preg_replace('/\s+/', '-', $accountType);
        if ($key === 'evaluation') {
          $ptypeClass = 'badge-mega-evaluation';
        } elseif ($key === 'funded') {
          $ptypeClass = 'badge-mega-funded';
        }
      } elseif ($plabel !== '') {
        // fallback antiguo por si algún dato viejo no trae accountType
        $parts = array_map('trim', explode('|', $plabel));
        $last = $parts ? trim(end($parts)) : '';
        $ptypeLabel = $last;
        $key = strtolower(preg_replace('/\s+/', '-', $last));
        $ptypeClass = $key === 'evaluation' ? 'badge-mega-evaluation' : ($key === 'funded' ? 'badge-mega-funded' : 'badge-mega-default');
      }


      $subscriptionId = '';
      $user_id = get_current_user_id();
      if ($fullData && is_numeric($order) && (int) $order > 0 && function_exists('mt_subscription_id_for_order')) {
        $subscriptionId = (string) mt_subscription_id_for_order((int) $order, (int) $user_id);
      }

      return [
        'id' => $id,
        'status' => $status,
        'badgeClass' => MT_Accounts::badge_class($status),
        'size' => $size,
        'name' => $name ?: 'Account',
        'platform' => $platformRaw,
        'logo' => $logo,
        'createdAt' => (string) ($acc['createdAt'] ?? ''),
        'mainProductId' => (string) ($rules['mainProductId'] ?? ''),
        'resetProductId' => (string) ($rules['resetProductId'] ?? ''),
        'activationProductId' => (string) ($rules['activationProductId'] ?? ''),
        'accountId' => (string) $platAccountId,
        'order' => $order,
        'subscriptionId' => $subscriptionId,
        'hasSubscription' => $subscriptionId !== '',
        'accountType' => strtolower((string) ($acc['accountType'] ?? '')),
        'programTypeText' => $ptypeLabel,
        'programTypeClass' => $ptypeClass,
      ];
    };

    return ['current' => $mapAccount($cur, fullData: true), 'accounts' => array_map($mapAccount, $pool)];
  }

  public static function prepare_ui_v2(array $accounts): array
  {
    if (isset($accounts['data']))
      $accounts = is_array($accounts['data']) ? $accounts['data'] : [];
    elseif (isset($accounts['results']))
      $accounts = $accounts['results'];
    elseif (isset($accounts['items']))
      $accounts = $accounts['items'];
    elseif (isset($accounts['id']))
      $accounts = [$accounts];

    if (!empty($accounts) && array_keys($accounts) !== range(0, count($accounts) - 1))
      $accounts = array_values($accounts);

    $pool = array_values(array_filter((array) $accounts, 'is_array'));
    usort($pool, function ($a, $b) {
      $ta = strtotime((string) ($a['createdAt'] ?? '')) ?: 0;
      $tb = strtotime((string) ($b['createdAt'] ?? '')) ?: 0;
      return $tb <=> $ta;
    });
    if (empty($pool))
      return ['current' => null, 'accounts' => []];

    $cur = $pool[0];
    foreach ($pool as $row) {
      if (self::is_active_status($row['status'] ?? '')) {
        $cur = $row;
        break;
      }
    }

    $mapAccount = function (array $acc) {
      $id = (string) ($acc['id'] ?? '');
      $plabel = (string) ($acc['program']['label'] ?? ($acc['program']['description'] ?? 'Account'));
      $sb = $acc['program']['startingBalance'] ?? null;
      [$size, $name] = MT_Accounts::parse_program_label($plabel, $sb);

      // platform text
      $platformRaw = '';
      if (isset($acc['platform'])) {
        $platformRaw = is_array($acc['platform']) ? (string) ($acc['platform']['platform'] ?? $acc['platform']['name'] ?? '') : (string) $acc['platform'];
      }
      if ($platformRaw === '' && isset($acc['program']['platform']))
        $platformRaw = (string) $acc['program']['platform'];
      $logo = MT_Accounts::platform_logo($platformRaw);

      $status = (string) ($acc['status'] ?? '');
      $rules = is_array($acc['rules'] ?? null) ? $acc['rules'] : [];
      $platAccountId = $acc['accountNr'];
      $order = (string) ($acc['order'] ?? '');

      // programType badge
      // programType badge (usa accountType cuando viene del API)
      $ptypeLabel = '';
      $ptypeClass = 'badge-mega-default';

      $accountType = strtolower(trim((string) ($acc['accountType'] ?? '')));

      if ($accountType !== '') {
        $ptypeLabel = ucfirst($accountType);
        $key = preg_replace('/\s+/', '-', $accountType);
        if ($key === 'evaluation') {
          $ptypeClass = 'badge-mega-evaluation';
        } elseif ($key === 'funded') {
          $ptypeClass = 'badge-mega-funded';
        }
      } elseif ($plabel !== '') {
        // fallback antiguo por si algún dato viejo no trae accountType
        $parts = array_map('trim', explode('|', $plabel));
        $last = $parts ? trim(end($parts)) : '';
        $ptypeLabel = $last;
        $key = strtolower(preg_replace('/\s+/', '-', $last));
        $ptypeClass = $key === 'evaluation' ? 'badge-mega-evaluation' : ($key === 'funded' ? 'badge-mega-funded' : 'badge-mega-default');
      }


      $subscriptionId = '';
      $user_id = get_current_user_id();
      if (is_numeric($order) && (int) $order > 0 && function_exists('mt_subscription_id_for_order')) {
        $subscriptionId = (string) mt_subscription_id_for_order((int) $order, (int) $user_id);
      }

      return [
        'id' => $id,
        'status' => $status,
        'badgeClass' => MT_Accounts::badge_class($status),
        'size' => $size,
        'name' => $name ?: 'Account',
        'platform' => $platformRaw,
        'logo' => $logo,
        'createdAt' => (string) ($acc['createdAt'] ?? ''),
        'mainProductId' => (string) ($rules['mainProductId'] ?? ''),
        'resetProductId' => (string) ($rules['resetProductId'] ?? ''),
        'activationProductId' => (string) ($rules['activationProductId'] ?? ''),
        'accountId' => (string) $platAccountId,
        'order' => $order,
        'subscriptionId' => $subscriptionId,
        'hasSubscription' => $subscriptionId !== '',
        'accountType' => strtolower((string) ($acc['accountType'] ?? '')),
        'programTypeText' => $ptypeLabel,
        'programTypeClass' => $ptypeClass,
      ];
    };

    return ['current' => $mapAccount($cur), 'accounts' => array_map($mapAccount, $pool)];
  }
}

/* ======================= Resto de helpers ======================= */

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
  function mt_program_stage($programOrLabel, $default = '')
  {
    // 1) Intento directo por accountType cuando venga un array
    if (is_array($programOrLabel)) {
      $accountType = '';

      if (isset($programOrLabel['accountType'])) {
        $accountType = (string) $programOrLabel['accountType'];
      } elseif (
        isset($programOrLabel['account']) &&
        is_array($programOrLabel['account']) &&
        isset($programOrLabel['account']['accountType'])
      ) {
        // por si en algún sitio viene envuelto en ['account' => [...]]
        $accountType = (string) $programOrLabel['account']['accountType'];
      }

      $key = strtolower(trim($accountType));
      if ($key === 'funded') {
        return 'Funded';
      }
      if ($key === 'evaluation') {
        return 'Evaluation';
      }
    }

    // 2) Fallback antiguo: parsear label / description
    $label = '';
    if (is_array($programOrLabel)) {
      $label = (string) ($programOrLabel['label'] ?? $programOrLabel['description'] ?? '');
    } else {
      $label = (string) $programOrLabel;
    }

    $label = trim(preg_replace('/\s+/', ' ', $label));

    if ($label === '') {
      return $default;
    }

    $parts = array_map('trim', explode('|', $label));
    $last = end($parts);
    $key = strtolower(preg_replace('/[^a-z]/i', '', $last));

    if ($key === 'funded') {
      return 'Funded';
    }
    if ($key === 'evaluation') {
      return 'Evaluation';
    }

    if (stripos($label, 'Funded') !== false) {
      return 'Funded';
    }
    if (stripos($label, 'Evaluation') !== false) {
      return 'Evaluation';
    }

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

    if ($label === '') {
      return $default !== '' ? $default : 'Default';
    }

    if (stripos($label, 'Zero') !== false) {
      return 'Zero';
    }
    if (stripos($label, 'Funded') !== false) {
      return 'Funded';
    }
    if (stripos($label, 'Elite') !== false) {
      return 'Elite';
    }
    if (stripos($label, 'Growth') !== false) {
      return 'Growth';
    }

    return $default !== '' ? $default : 'Default';
  }
}

if (!function_exists('mt_program_rules_url')) {
  function mt_program_rules_url($programOrLabel)
  {
    $plan = mt_program_plan($programOrLabel, 'Default');

    if (!class_exists('Label') || !defined('Label::PLAN_RULES_URLS')) {
      return '';
    }

    $map = Label::PLAN_RULES_URLS;

    if (!empty($map[$plan])) {
      return $map[$plan];
    }

    return $map['Default'] ?? '';
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
      'activeTradingDaysSinceLastPayout' => $metrics['activeTradingDaysSinceLastPayout'] ?? null,
      'dailyTotalPnL' => $metrics['dailyTotalPnL'] ?? $metrics['dailyPnL'] ?? null,
      'minTradingDays' => $metrics['minTradingDays'] ?? null,
      'maxLossLimitEquityLevel' => $metrics['maxLossLimitEquityLevel'] ?? $metrics['maxLossLimit'] ?? null,
      'target' => $program['target'] ?? $program['profitTarget'] ?? mt__get($metrics, ['target']),
      'maxDailyLossLimitPnLLevel' => $metrics['maxDailyLossLimitPnLLevel'] ?? null,
      'label' => $program['label'] ?? $program['description'] ?? mt__get($metrics, ['label']),
      'consistency' => mt__get($account, ['rules', 'consistency']),
      'targetAmount' => mt__get($account, ['payout', 'payoutCycle', 'targetAmountFromStartBalance']),
      'consistencyCurrentTopDayProfit' => $metrics['consistencyCurrentTopDayProfit'] ?? null,
      'consistencyResetBalanceMark' => $metrics['consistencyResetBalanceMark'] ?? null,
      'currentCycle' => mt__get($account, ['payout', 'payoutCycle', 'currentCycle']),
      'accountType' => isset($account['accountType']) ? strtolower((string) $account['accountType']) : null,
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

/* === Feature content === */
if (!function_exists('mt_accounts_build_feature_content')) {
  function mt_accounts_build_feature_content(array $account): array
  {
    $m = $account['metrics'] ?? $account['metric'] ?? [];

    $accountId = isset($m['accountId']) && $m['accountId'] ? $m['accountId'] : '';
    $avgWin = $m['averageWin'] ?? 0;
    $avgLoss = $m['averageLoss'] ?? 0;
    $winRate = $m['winRate'] ?? 0;
    $lossRate = $m['lossRate'] ?? 0;

    $avgWin = is_numeric($avgWin) ? (float) $avgWin : 0.0;
    $avgLoss = is_numeric($avgLoss) ? (float) $avgLoss : 0.0;
    $winRate = is_numeric($winRate) ? (float) $winRate : 0.0;
    $lossRate = is_numeric($lossRate) ? (float) $lossRate : 0.0;

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
        'winRate' => $winRate,
        'lossRate' => $lossRate,
      ],
    ];
    foreach ($payload['overview'] as $k => $v) {
      if (is_string($v) && is_numeric($v))
        $payload['overview'][$k] = $v + 0;
    }
    return $payload;
  }
}
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

/* === Fetch account JSON (shortcode) === */
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

/* === AJAX: performance === */
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

/* === Resolve by ID (memo + transients) === */
if (!function_exists('mt_accounts_resolve_account_by_id')) {
  function mt_accounts_resolve_account_by_id(string $accountId)
  {
    static $memo = [];
    $accountId = trim((string) $accountId);
    if ($accountId === '')
      return null;

    if (isset($memo[$accountId]))
      return $memo[$accountId];

    $tkey = mt_cache_key('mt:acc_by_id', [$accountId]);
    $cached = mt_cache_get($tkey);
    if (is_array($cached)) {
      $memo[$accountId] = $cached;
      return $cached;
    }

    $acc = null;

    if (class_exists('MT_Api') && method_exists('MT_Api', 'fetch_account_by_id')) {
      try {
        $acc = MT_Api::fetch_account_by_id($accountId);
      } catch (\Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG)
          error_log('[MT][acc_resolve][by_id] ' . $e->getMessage());
      }
    }

    if (!$acc && function_exists('mt_accounts_fetch_account_json_by_shortcode')) {
      try {
        $json = mt_accounts_fetch_account_json_by_shortcode($accountId, 1, 1);
        if (function_exists('mt_accounts_pick_account_from_json')) {
          $acc = mt_accounts_pick_account_from_json($json, $accountId);
        }
      } catch (\Throwable $e) {
        if (defined('WP_DEBUG') && WP_DEBUG)
          error_log('[MT][acc_resolve][shortcode] ' . $e->getMessage());
      }
    }

    $acc = (is_array($acc) && !empty($acc)) ? $acc : null;

    if ($acc)
      mt_cache_set($tkey, $acc, 90);
    $memo[$accountId] = $acc;
    return $acc;
  }
}

/* ===== MONEY / PERCENT HELPERS ===== */
if (!function_exists('mt_money_symbol')) {
  function mt_money_symbol($fallback = '$', $context = null)
  {
    return apply_filters('mt_money_currency_symbol', $fallback, $context);
  }
}
if (!function_exists('mt_format_money')) {
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
  function mt_format_money_no_cents($value, $currency = null, $context = null)
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return '—';
    if ($currency === null)
      $currency = mt_money_symbol('$', $context);
    $num = (float) $value;
    $neg = $num < 0;
    $abs = abs($num);
    $rounded = round($abs, 0);
    $formatted = number_format($rounded, 0, '.', ',');
    return ($neg ? '-' : '') . $currency . $formatted;
  }
}
if (!function_exists('mt_format_signed_money')) {
  function mt_format_signed_money($value, $currency = null, $context = null)
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return '—';
    if ($currency === null)
      $currency = mt_money_symbol('$', $context);
    $num = (float) $value;
    $abs = abs($num);
    if (abs($num) < 1e-9)
      $num = 0.0;
    $formatted = number_format($abs, 2, '.', ',');
    $sign = ($num > 0) ? '+' : (($num < 0) ? '-' : '');
    return $sign . $currency . $formatted;
  }
}
if (!function_exists('mt_format_percent')) {
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
  function mt_format_percent_compact($value, $empty = '—')
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return $empty;
    $txt = number_format((float) $value, 2, '.', '');
    $txt = rtrim(rtrim($txt, '0'), '.');
    return $txt . '%';
  }
}
if (!function_exists('mt_value_color_class')) {
  function mt_value_color_class($value, $zero = 'text-white', $pos = 'text-success', $neg = 'text-error')
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return $zero;
    $n = (float) $value;
    return ($n > 0) ? $pos : (($n < 0) ? $neg : $zero);
  }
}
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
  function mt_percent_text($value)
  {
    if ($value === null || $value === '' || !is_numeric($value))
      return '—';
    $n = (float) $value;
    return $n == 0.0 ? mt_format_percent(0, false) : mt_format_percent($n, true);
  }
}
if (!function_exists('mt_profit_ui_from_percent')) {
  function mt_profit_ui_from_percent($value)
  {
    return [
      'badgeClass' => mt_percent_badge_class($value),
      'iconClass' => mt_percent_icon_class($value),
      'text' => mt_percent_text($value),
    ];
  }
}
if (!function_exists('mt_value_icon_classes')) {
  function mt_value_icon_classes($value, $opts = [])
  {
    $pos = $opts['pos'] ?? 'mt-icon-success mt-icon_checkmark-solid';
    $neg = $opts['neg'] ?? 'mt-icon-error mt-icon_cancel';
    $zero = $opts['zero'] ?? '';
    $neutral = $opts['neutral'] ?? 'mt-icon-gray mt-icon_checkmark-solid';

    if ($value === null || $value === '' || !is_numeric($value))
      return $neutral;
    $n = (float) $value;
    if (abs($n) < 1e-9)
      return $zero;
    return $n > 0 ? $pos : $neg;
  }
}
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
      return $cls_neutral;

    if ($target === null || $target === '' || !is_numeric($target))
      return $cls_neutral;
    $t = (float) $target;

    if ($t <= 0)
      return $cls_pos;

    if (abs($v - $t) < 1e-9)
      return $cls_pos;

    return ($v > $t) ? $cls_pos : $cls_neutral;
  }
}

/* === Platform links === */
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

/* === Credenciales === */
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
function mt_accounts_get_credentials($account_id)
{
  $account_id = sanitize_text_field($account_id ?? '');
  if (!preg_match('/^[a-f0-9]{24}$/i', $account_id)) {
    return [
      'login' => '',
      'password' => '',
      'server' => '',
      'links' => ['web' => '', 'appstore' => '', 'playstore' => ''],
      'platform' => ['code' => '', 'name' => 'Trading Platform', 'icon_class' => ''],
    ];
  }

  $json = mt_accounts_fetch_account_json_by_shortcode($account_id, 1, 10);
  $acc = function_exists('mt_accounts_pick_account_from_json')
    ? mt_accounts_pick_account_from_json($json, $account_id)
    : null;

  $creds = mt_accounts_build_credentials_from_account($acc);
  $defaults = [
    'login' => '',
    'password' => '',
    'server' => '',
    'links' => ['web' => '', 'appstore' => '', 'playstore' => ''],
    'platform' => ['code' => '', 'name' => 'Trading Platform', 'icon_class' => ''],
  ];
  return array_replace_recursive($defaults, is_array($creds) ? $creds : []);
}

/* === AJAX: HTML de account-data === */
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

/* ================= Email: normalizar/validar ================= */
if (!function_exists('mt_normalize_email')) {
  function mt_normalize_email(?string $email): string
  {
    if (!is_string($email))
      return '';
    $email = trim(preg_replace('/\s+/u', '', $email));
    return mb_strtolower($email, 'UTF-8');
  }
}
if (!function_exists('mt_validate_email')) {
  function mt_validate_email(string $email): bool
  {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
  }
}
if (!function_exists('mt_email_for_api')) {
  function mt_email_for_api(string $email): string
  {
    $parts = explode('@', $email, 2);
    if (count($parts) !== 2)
      return '';
    [$local, $domain] = $parts;
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
    if ($needsEncoding)
      $local = strtr($local, $replacements);
    return $local . '@' . $domain;
  }
}
if (!function_exists('mt_sanitize_email')) {
  function mt_sanitize_email(?string $raw): array
  {
    $normalized = mt_normalize_email($raw);
    if ($normalized === '')
      return ['ok' => false, 'email' => '', 'api' => '', 'error' => 'Email vacío.'];
    if (!mt_validate_email($normalized))
      return ['ok' => false, 'email' => $normalized, 'api' => '', 'error' => 'Email inválido.'];
    $apiSafe = mt_email_for_api($normalized);
    return ['ok' => true, 'email' => $normalized, 'api' => $apiSafe, 'error' => ''];
  }
}

/* === Performance Chart Payload (incluye cutoff 6pm ET) === */
if (!function_exists('mt_accounts_build_performance_chart')) {
  function mt_accounts_build_performance_chart(array $account): array
  {
    $tz = new DateTimeZone('UTC');
    $todayDt = new DateTime('now', $tz);
    $todayDt->setTime(0, 0, 0);

    // --- fechas base (first date) ---
    $firstRawTop = (string) ($account['firstTradeDate'] ?? '');
    $firstRawMetric = (string) ($account['metrics']['firstTradeDate'] ?? $account['metric']['firstTradeDate'] ?? '');
    $firstRawAlt1 = (string) ($account['createdAt'] ?? '');
    $firstRawAlt2 = (string) ($account['owner']['account']['createdAt'] ?? '');
    $firstRaw = $firstRawTop ?: ($firstRawMetric ?: ($firstRawAlt1 ?: $firstRawAlt2));

    $firstDt = $firstRaw ? new DateTime($firstRaw, $tz) : clone $todayDt;
    $firstDt->setTime(0, 0, 0);

    // --- si first > today, normaliza ---
    if ($firstDt > $todayDt)
      $firstDt = clone $todayDt;

    // --- corte por BREACHED ---
    $status = strtoupper((string) ($account['status'] ?? ''));
    $breachDt = null;
    if ($status === 'BREACHED') {
      $breachRaw = (string) ($account['breachedAt'] ?? '');
      if ($breachRaw) {
        $breachDt = new DateTime($breachRaw, $tz);
        $breachDt->setTime(0, 0, 0); // incluir el día de breach
      }
    }

    // endDt = min(today, breachedAt si aplica)
    $endDt = clone $todayDt;
    if ($breachDt && $breachDt < $endDt) {
      $endDt = clone $breachDt;
    }

    // si por algún motivo first > end, ajusta
    if ($firstDt > $endDt)
      $firstDt = clone $endDt;

    // --- ventana y fetch ---
    $totalSinceStart = (int) $firstDt->diff($endDt)->days + 1;         // inclusivo
    $pointsToLoad = min(30, max(1, $totalSinceStart));

    $startDt = (clone $endDt)->modify('-' . ($pointsToLoad - 1) . ' days');
    if ($startDt < $firstDt)
      $startDt = clone $firstDt;

    $from = $startDt->format('Y-m-d');
    $to = $endDt->format('Y-m-d');

    $accountId = (string) ($account['accountId'] ?? $account['id'] ?? '');

    $resp = function_exists('mt_metrics_fetch_by_shortcode')
      ? mt_metrics_fetch_by_shortcode($accountId, ['from' => $from, 'to' => $to, 'perpage' => 200, 'ttl' => 30])
      : null;

    $rows = (is_array($resp) && isset($resp['data']) && is_array($resp['data'])) ? $resp['data'] : [];

    // --- compactar por día (último valor del día) ---
    $byDay = [];
    foreach ($rows as $row) {
      $metrics = isset($row['metrics']) && is_array($row['metrics']) ? $row['metrics'] : [];
      $cb = $metrics['currentBalance'] ?? null;
      if (!is_numeric($cb))
        continue;

      $ts = strtotime($row['updatedAt'] ?? $row['createdAt'] ?? '') ?: 0;
      $ymd = substr((string) ($row['date'] ?? $row['fromDate'] ?? $row['toDate'] ?? ''), 0, 10);
      if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $ymd)) {
        $ymd = $ts ? gmdate('Y-m-d', $ts) : '';
      }
      if ($ymd === '')
        continue;

      // Ignora cualquier dato posterior al corte (seguro extra)
      if ($ymd > $to)
        continue;

      if (!isset($byDay[$ymd]) || $ts >= $byDay[$ymd]['ts']) {
        $byDay[$ymd] = ['ts' => $ts, 'value' => (float) $cb];
      }
    }

    // --- construir serie continua desde $from hasta $to (incl.) ---
    $series = [];
    $cursor = new DateTime($from, $tz);
    $carry = null;
    while ($cursor <= $endDt) {
      $ymd = $cursor->format('Y-m-d');
      if (isset($byDay[$ymd]))
        $carry = $byDay[$ymd]['value'];
      if ($carry !== null)
        $series[] = ['date' => $ymd, 'value' => (float) $carry];
      $cursor->modify('+1 day');
    }

    // fallback si no hubo datos: usa balance actual, fechado al endDt (no a hoy)
    if (empty($series)) {
      $m = $account['metrics'] ?? $account['metric'] ?? [];
      $cb = is_numeric($m['currentBalance'] ?? null) ? (float) $m['currentBalance'] : null;
      if ($cb !== null) {
        $series[] = ['date' => $endDt->format('Y-m-d'), 'value' => $cb];
      }
    }

    // --- metas / límites ---
    $m = $account['metrics'] ?? $account['metric'] ?? [];
    $profit_target = is_numeric($m['equityPassLevel'] ?? null) ? (float) $m['equityPassLevel'] : null;
    $max_drawdown = is_numeric($m['maxLossLimitEquityLevel'] ?? null) ? (float) $m['maxLossLimitEquityLevel'] : null;
    $consistencyResetBalanceMark = is_numeric($m['consistencyResetBalanceMark'] ?? null)
      ? (float) $m['consistencyResetBalanceMark']
      : null;

    // --- payout ---
    $payoutCycle = $account['payout']['payoutCycle'] ?? ($account['payoutCycle'] ?? null);
    $funded_target_amount = null;
    if (is_array($payoutCycle)) {
      $raw = $payoutCycle['targetAmountFromStartBalance'] ?? ($payoutCycle['targetAmount'] ?? null);
      if (is_numeric($raw))
        $funded_target_amount = (float) $raw;
    }

    // --- periods (basado en endDt real) ---
    $periods = [];
    $sinceTextDays = $totalSinceStart;
    $sinceValue = min($pointsToLoad, $totalSinceStart);
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

    // --- título / starting balance ---
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
      'label' => $plabel,
      'plan_revenue' => $series,
      'series' => $series,
      'profit_target' => $profit_target,
      'max_drawdown' => $max_drawdown,
      'funded_target_amount' => $funded_target_amount,
      'consistency_reset_balance_mark' => $consistencyResetBalanceMark,
      'periods' => $periods,
      'starting_balance' => is_numeric($sb) ? (float) $sb : null,
      // opcional para debug: 'cutoff_to' => $to,
    ];
  }
}

/* === Account Data payload === */
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

/* === WC helpers === */
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
    $fallback_id = mt_find_product_by_category_slugs(['reset-fee']);
    return $fallback_id ? mt_checkout_add_to_cart_url($fallback_id) : '';
  }
}

/* === Agreements === */
if (!function_exists('mt_get_agreement_status_by_email')) {
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

    $sc = sprintf(
      '[mega_subscriptions_data email="%s" output="json" ttl="%d"]',
      esc_attr($email_encoded),
      max(0, $ttl)
    );
    if (defined('WP_DEBUG') && WP_DEBUG)
      error_log('[MT Agreement][sc]=' . $sc);

    $raw = do_shortcode($sc);
    $raw = is_string($raw) ? trim(wp_unslash($raw)) : '';

    if ($raw !== '' && substr($raw, 0, 3) === "\xEF\xBB\xBF")
      $raw = substr($raw, 3);
    if ($raw !== '')
      $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    if (defined('WP_DEBUG') && WP_DEBUG) {
      $preview = substr((string) $raw, 0, 800);
      error_log('[MT Agreement][raw(len)]= ' . strlen((string) $raw));
      error_log('[MT Agreement][raw(preview)]= ' . $preview);
    }

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

/* === AJAX: status por accountId === */
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

add_action('wp_ajax_mt_account_overview_data', 'mt_account_overview_data_ajax');
add_action('wp_ajax_nopriv_mt_account_overview_data', 'mt_account_overview_data_ajax');

function mt_account_overview_data_ajax()
{
  check_ajax_referer('mt-acc-nonce', 'nonce');

  $accountIds = isset($_POST['accountId'])
    ? array_map('sanitize_text_field', (array) $_POST['accountId'])
    : [];

  if (empty($accountIds)) {
    wp_send_json_error(['message' => 'Missing accountIds']);
  }

  $result = mt_account_overview_business_logic(
    accountIds: $accountIds
  );

  if (isset($result['error'])) {
    wp_send_json_error($result);
  }

  wp_send_json_success($result);
}

/* === MÉTRICS vía shortcode (JSON) === */
if (!function_exists('mt_metrics_fetch_by_shortcode')) {
  function mt_metrics_fetch_by_shortcode($accountId, $arg2 = 1, $perPage = 30, $ttl = 15)
  {
    $accountId = trim((string) $accountId);
    if ($accountId === '')
      return null;

    $page = 1;
    $per = 50;
    $from = '';
    $to = '';
    $ttlVal = 15;
    if (is_array($arg2)) {
      $page = isset($arg2['page']) ? (int) $arg2['page'] : 1;
      $per = isset($arg2['perpage']) ? (int) $arg2['perpage'] : (isset($arg2['perPage']) ? (int) $arg2['perPage'] : 50);
      $from = isset($arg2['from']) ? (string) $arg2['from'] : '';
      $to = isset($arg2['to']) ? (string) $arg2['to'] : '';
      $ttlVal = isset($arg2['ttl']) ? (int) $arg2['ttl'] : 15;
    } else {
      $page = (int) $arg2;
      $per = (int) $perPage;
      $ttlVal = (int) $ttl;
    }

    $tkey = mt_cache_key('mt:metrics_sc', [$accountId, $page, $per, $from, $to]);
    $hit = mt_cache_get($tkey);
    if (is_array($hit))
      return $hit;

    $attrs = [
      'id' => esc_attr($accountId),
      'page' => max(1, $page),
      'perpage' => max(1, $per),
      'output' => 'json',
      'ttl' => max(0, $ttlVal),
    ];
    if ($from !== '')
      $attrs['from'] = $from;
    if ($to !== '')
      $attrs['to'] = $to;

    $parts = [];
    foreach ($attrs as $k => $v) {
      $parts[] = $k . '="' . $v . '"';
    }
    $sc = '[mega_metrics_data ' . implode(' ', $parts) . ']';

    $raw = do_shortcode($sc);
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

    $out = is_array($data) ? $data : null;
    if ($out)
      mt_cache_set($tkey, $out, 60);
    return $out;
  }
}

/* === TRADES vía shortcode (JSON) === */
if (!function_exists('mt_trades_fetch_by_shortcode')) {
  function mt_trades_fetch_by_shortcode(string $accountId, string $type = 'CLOSED', int $page = 1, int $perPage = 500)
  {
    $accountId = trim((string) $accountId);
    if ($accountId === '')
      return null;

    $tkey = mt_cache_key('mt:trades_sc', [$accountId, $type, $page, $perPage]);
    $hit = mt_cache_get($tkey);
    if (is_array($hit))
      return $hit;

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

    $out = null;
    if (isset($json['data']) && is_array($json['data']))
      $out = $json['data'];
    elseif (isset($json[0]) && is_array($json[0]))
      $out = $json;

    if ($out)
      mt_cache_set($tkey, $out, 25);
    return $out;
  }
}

/* === TRADES: day stats (cutoff 6pm ET vía helper) === */
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
      if (empty($t['closeTime']))
        return false;
      $ct = (string) $t['closeTime'];
      $ct_ymd = function_exists('mt_utc_to_eastern_ymd_cutoff')
        ? mt_utc_to_eastern_ymd_cutoff($ct, 18)
        : (function_exists('mt_utc_to_eastern_ymd') ? mt_utc_to_eastern_ymd($ct) : substr($ct, 0, 10));
      return $ct_ymd === $day_iso;
    }));

    if (empty($dayTrades)) {
      return ['maxConsecWins' => '-', 'maxConsecLosses' => '-', 'avgWinDuration' => '-', 'avgLossDuration' => '-'];
    }

    usort($dayTrades, function ($a, $b) {
      $ta = strtotime((string) ($a['closeTime'] ?? '')) ?: 0;
      $tb = strtotime((string) ($b['closeTime'] ?? '')) ?: 0;
      return $ta <=> $tb;
    });

    $maxW = $maxL = $runW = $runL = 0;
    $sumW = $sumL = 0;
    $cntW = $cntL = 0;

    foreach ($dayTrades as $t) {
      $pnl = $t['pnl'] ?? null;
      $durSec = null;
      if (!empty($t['openTime']) && !empty($t['closeTime'])) {
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
        $runW = $runL = 0;
      }
    }

    $fmt = function ($s) {
      $s = (int) round($s);
      return sprintf('%02d:%02d:%02d', floor($s / 3600), floor(($s % 3600) / 60), $s % 60);
    };

    return [
      'maxConsecWins' => $maxW ?: 0,
      'maxConsecLosses' => $maxL ?: 0,
      'avgWinDuration' => $cntW > 0 ? $fmt($sumW / $cntW) : '00:00:00',
      'avgLossDuration' => $cntL > 0 ? $fmt($sumL / $cntL) : '00:00:00',
    ];
  }
}

/* === UTC -> Eastern helpers (con cutoff) === */
if (!function_exists('mt_utc_to_eastern_ymd')) {
  function mt_utc_to_eastern_ymd($utcString)
  {
    $src = is_string($utcString) ? trim($utcString) : '';
    if ($src === '')
      return '';
    try {
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
if (!function_exists('mt_utc_to_eastern_ymd_cutoff')) {
  function mt_utc_to_eastern_ymd_cutoff($utcString, $cutoffHour = 18)
  {
    $src = is_string($utcString) ? trim($utcString) : '';
    if ($src === '')
      return '';
    try {
      $utc = new DateTimeZone('UTC');
      $ny = new DateTimeZone('America/New_York');
      if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $src))
        $src .= ' 00:00:00';
      $dt = new DateTime($src, $utc);
      $dt->setTimezone($ny);
      if ((int) $dt->format('G') >= (int) $cutoffHour) {
        $dt->modify('+1 day');
      }
      return $dt->format('Y-m-d');
    } catch (\Throwable $e) {
      return '';
    }
  }
}

/* === Commissions sum / Trades count por día (ET + cutoff) === */
if (!function_exists('mt_sum_commissions_for_day')) {
  function mt_sum_commissions_for_day(string $accountId, string $day_iso_eastern)
  {
    static $cache = [];
    $accountId = trim((string) $accountId);
    $day_iso_eastern = substr((string) $day_iso_eastern, 0, 10);
    if ($accountId === '' || $day_iso_eastern === '')
      return '-';

    $toEasternYmdCutoff = function (?string $iso, int $cutoffHour = 18): string {
      $iso = is_string($iso) ? trim($iso) : '';
      if ($iso === '')
        return '';
      if (function_exists('mt_utc_to_eastern_ymd_cutoff')) {
        return mt_utc_to_eastern_ymd_cutoff($iso, $cutoffHour);
      }
      try {
        $utc = new DateTimeZone('UTC');
        $ny = new DateTimeZone('America/New_York');
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $iso))
          $iso .= ' 00:00:00';
        $dt = new DateTime($iso, $utc);
        $dt->setTimezone($ny);
        if ((int) $dt->format('G') >= $cutoffHour) {
          $dt->modify('+1 day');
        }
        return $dt->format('Y-m-d');
      } catch (\Throwable $e) {
        return substr($iso, 0, 10);
      }
    };

    if (!isset($cache[$accountId])) {
      $trades = function_exists('mt_trades_fetch_by_shortcode')
        ? mt_trades_fetch_by_shortcode($accountId, 'CLOSED', 1, 500)
        : null;
      $cache[$accountId] = is_array($trades) ? $trades : [];
    }

    $trades = $cache[$accountId];
    if (empty($trades))
      return '-';

    $sum = 0.0;
    $found = false;

    foreach ($trades as $t) {
      $ct = isset($t['closeTime']) ? (string) $t['closeTime'] : '';
      if ($ct === '')
        continue;
      $ct_ymd = $toEasternYmdCutoff($ct, 18);
      if ($ct_ymd === $day_iso_eastern) {
        if (isset($t['commission']) && is_numeric($t['commission'])) {
          $sum += (float) $t['commission'];
          $found = true;
        }
      }
    }
    return $found ? $sum : '-';
  }
}
if (!function_exists('mt_count_trades_for_day')) {
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
      $ct = isset($t['closeTime']) ? (string) $t['closeTime'] : '';
      if ($ct === '')
        continue;
      $ct_ymd = function_exists('mt_utc_to_eastern_ymd_cutoff')
        ? mt_utc_to_eastern_ymd_cutoff($ct, 18)
        : (function_exists('mt_utc_to_eastern_ymd') ? mt_utc_to_eastern_ymd($ct) : substr($ct, 0, 10));
      if ($ct_ymd === $day_iso_eastern) {
        $cnt++;
      }
    }
    return $cnt;
  }
}

/* === DAILY JOURNAL (METRICS SOLO PARA NET; RESTO DESDE TRADES) === */
if (!function_exists('mt_accounts_build_daily_journal')) {
  function mt_accounts_build_daily_journal($accountId, $page = 1, $perPage = 30)
  {
    $accountId = (string) $accountId;
    $rows = [];

    // Sin accountId o sin metrics: no hay tabla
    if ($accountId === '' || !function_exists('mega_api_get_metrics')) {
      if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[DJ] early-exit: missing accountId or mega_api_get_metrics');
      }
      return ['rows' => $rows, 'per_page' => (int) $perPage];
    }

    $has_trades_fn = function_exists('mt_trades_fetch_by_shortcode');

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
        if (defined('WP_DEBUG') && WP_DEBUG) {
          error_log('[DJ] toNY error: ' . $e->getMessage() . ' iso=' . $iso);
        }
        return null;
      }
    };

    $fmtHMS = function (int $secs): string {
      if ($secs <= 0)
        return '00:00:00';
      $h = (int) floor($secs / 3600);
      $m = (int) floor(($secs % 3600) / 60);
      $s = (int) ($secs % 60);
      return sprintf('%02d:%02d:%02d', $h, $m, $s);
    };

    /* ==================================================
     * 1) METRICS → fuente principal (solo NET + días)
     * ================================================== */

    $metricsByDay = [];

    $mPerPage = 500;
    $mPage = 1;

    if (defined('WP_DEBUG') && WP_DEBUG) {
      error_log('[DJ] metrics fetch start account=' . $accountId);
    }

    while (true) {
      $mChunk = mega_api_get_metrics($accountId, $mPage, $mPerPage, '', '');
      if (is_wp_error($mChunk)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
          error_log('[DJ] metrics fetch error page=' . $mPage . ' msg=' . $mChunk->get_error_message());
        }
        break;
      }

      $mItems = [];
      if (isset($mChunk['data']) && is_array($mChunk['data'])) {
        $mItems = $mChunk['data'];
      }

      if (empty($mItems)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
          error_log('[DJ] metrics fetch empty page=' . $mPage);
        }
        break;
      }

      foreach ($mItems as $mItem) {
        if (!is_array($mItem))
          continue;
        $m = isset($mItem['metrics']) && is_array($mItem['metrics'])
          ? $mItem['metrics']
          : $mItem;

        $dayIso = isset($m['tradeDateUTC']) ? trim((string) $m['tradeDateUTC']) : '';
        if ($dayIso === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dayIso)) {
          continue;
        }

        // API viene en DESC; nos quedamos con el primero por día
        if (!isset($metricsByDay[$dayIso])) {
          $metricsByDay[$dayIso] = $m;
        }
      }

      $pagesCount = isset($mChunk['meta']['pagesCount']) ? (int) $mChunk['meta']['pagesCount'] : null;
      if ($pagesCount && $mPage >= $pagesCount)
        break;
      if (count($mItems) < $mPerPage)
        break;

      $mPage++;
    }

    if (defined('WP_DEBUG') && WP_DEBUG) {
      error_log('[DJ] metrics days fetched=' . count($metricsByDay));
    }

    if (empty($metricsByDay)) {
      return ['rows' => [], 'per_page' => (int) $perPage];
    }

    /* ==================================================
     * 2) Construir filas base desde METRICS
     *    - net desde consTradingDailyTotalRealizedPnL
     *    - si net === 0 → NO se pinta ese día
     *    - el resto se rellena luego con TRADES (versión vieja)
     * ================================================== */

    foreach ($metricsByDay as $dayIso => $m) {

      // Net P&L desde metrics (campo nuevo principal)
      if (isset($m['consTradingDailyTotalRealizedPnL'])) {
        $netVal = (float) $m['consTradingDailyTotalRealizedPnL'];
      } elseif (isset($m['tradingDailyTotalRealizedPnL'])) {
        $netVal = (float) $m['tradingDailyTotalRealizedPnL'];
      } elseif (isset($m['dailyTotalRealizedPnL'])) {
        $netVal = (float) $m['dailyTotalRealizedPnL'];
      } else {
        $netVal = 0.0;
      }

      // Si el Net P&L es 0 → no pintamos ese row
      if (abs($netVal) < 0.00001) {
        continue;
      }

      $rows[] = [
        'date' => $dayIso,
        'openTime' => $dayIso,
        'net' => $netVal,        // METRICS
        'hi' => '-',            // TRADES (P&L High vieja)
        'lo' => '-',            // TRADES (P&L Low vieja)
        'ct' => 0,              // TRADES (Total Contracts viejo)
        'trades' => 0,              // TRADES (Total Trades viejo)
        'fees' => 0.0,            // TRADES
        'awin' => '-',            // TRADES (Avg. Win Trades viejo)
        'aloss' => '-',            // TRADES (Avg. Loss Trades viejo)
        'win' => 0.0,            // TRADES (Winning Trade % viejo)
        'loss' => 0.0,            // TRADES (Loss Trade % viejo)
        'max' => '0/0',          // TRADES (máx racha W/L viejo)
        'dur' => '00:00:00/00:00:00', // TRADES (Avg W/L Duration viejo)
      ];
    }

    // Si después del filtro no quedó nada, devolvemos vacío
    if (empty($rows)) {
      return ['rows' => [], 'per_page' => (int) $perPage];
    }

    // Índice rápido por día
    $rowsByDay = [];
    foreach ($rows as $idx => $r) {
      $dayIso = substr((string) ($r['openTime'] ?? $r['date'] ?? ''), 0, 10);
      if ($dayIso !== '') {
        $rowsByDay[$dayIso] = $idx;
      }
    }

    /* ==================================================
     * 3) TRADES → fees, hi, lo, ct, trades, awin, aloss,
     *               win, loss, max, dur (toda la lógica vieja)
     * ================================================== */

    if ($has_trades_fn) {
      $tradesAgg = [];
      $perPageFetch = 500;
      $pageFetch = 1;

      if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[DJ] fetch trades (for legacy stats) start account=' . $accountId);
      }

      while (true) {
        $chunk = mt_trades_fetch_by_shortcode($accountId, 'CLOSED', $pageFetch, $perPageFetch);
        if (is_wp_error($chunk)) {
          if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[DJ] trades fetch error page=' . $pageFetch . ' msg=' . $chunk->get_error_message());
          }
          break;
        }
        if (empty($chunk)) {
          if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[DJ] trades fetch empty page=' . $pageFetch);
          }
          break;
        }

        $items = [];
        if (isset($chunk['data']) && is_array($chunk['data'])) {
          $items = $chunk['data'];
        } elseif (is_array($chunk)) {
          $items = $chunk;
        }

        if (empty($items))
          break;

        foreach ($items as $t) {
          if (!is_array($t))
            continue;

          $cIso = isset($t['closeTime']) ? (string) $t['closeTime'] : null;
          $oIso = isset($t['openTime']) ? (string) $t['openTime'] : null;

          $closeNY = $toNY($cIso);
          $openNY = $toNY($oIso);
          if (!$closeNY || !$openNY)
            continue;

          $day = function_exists('mt_utc_to_eastern_ymd_cutoff')
            ? mt_utc_to_eastern_ymd_cutoff((string) $t['closeTime'], 18)
            : $closeNY->format('Y-m-d');

          // Solo si ese día existe en METRICS (tabla diaria no-cero)
          if (!isset($rowsByDay[$day]))
            continue;

          $pnl = (float) ($t['pnl'] ?? 0);
          $commission = (float) ($t['commission'] ?? 0);
          $lots = (int) ($t['lots'] ?? 0);
          $durSecs = max(0, (int) round($closeNY->getTimestamp() - $openNY->getTimestamp()));

          if (!isset($tradesAgg[$day])) {
            $tradesAgg[$day] = [
              'fees' => 0.0,
              'wins' => 0,
              'losses' => 0,
              'sumWin' => 0.0,
              'sumLoss' => 0.0,
              'durWinSecs' => 0,
              'durLossSecs' => 0,
              'hi' => null,
              'lo' => null,
              'ct' => 0,
              'trades' => 0,
              '_seq' => [],
            ];
          }

          $A =& $tradesAgg[$day];

          // fees
          $A['fees'] += $commission;

          // contratos y trades (como versión original)
          $A['ct'] += max(0, $lots);
          $A['trades'] += 1;

          // hi/lo P&L por trade (versión vieja)
          if ($pnl > 0) {
            $A['hi'] = is_null($A['hi']) ? $pnl : max($A['hi'], $pnl);
          } elseif ($pnl < 0) {
            $A['lo'] = is_null($A['lo']) ? $pnl : min($A['lo'], $pnl);
          }

          // W/L + sumas para Avg Win / Avg Loss + duraciones
          if ($pnl > 0) {
            $A['wins'] += 1;
            $A['sumWin'] += $pnl;
            $A['durWinSecs'] += $durSecs;
          } elseif ($pnl < 0) {
            $A['losses'] += 1;
            $A['sumLoss'] += $pnl;
            $A['durLossSecs'] += $durSecs;
          }

          // secuencia para rachas
          $A['_seq'][] = [
            'openTs' => $openNY->getTimestamp(),
            'pnl' => $pnl,
          ];

          unset($A);
        }

        $pagesCount = isset($chunk['meta']['pagesCount']) ? (int) $chunk['meta']['pagesCount'] : null;
        if ($pagesCount && $pageFetch >= $pagesCount)
          break;
        if (count($items) < $perPageFetch)
          break;

        $pageFetch++;
      }

      // Aplicar TODO lo viejo a las filas
      foreach ($tradesAgg as $day => $agg) {
        if (!isset($rowsByDay[$day]))
          continue;
        $idx = $rowsByDay[$day];

        // racha max W/L (versión vieja)
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

        // Avg. Win / Avg. Loss (versión vieja)
        $awin = $wins > 0 ? ($agg['sumWin'] / $wins) : '-';
        $aloss = $loss > 0 ? ($agg['sumLoss'] / $loss) : '-';

        // Winning % / Loss % (versión vieja)
        $winPct = round(($wins * 100.0) / $tot, 2);
        $losPct = round(100.0 - $winPct, 2);

        // Duraciones promedio (versión vieja)
        $avgWinDur = $wins > 0 ? (int) floor($agg['durWinSecs'] / $wins) : 0;
        $avgLosDur = $loss > 0 ? (int) floor($agg['durLossSecs'] / $loss) : 0;

        // Aplicar a la fila
        $rows[$idx]['fees'] = (float) $agg['fees'];
        $rows[$idx]['hi'] = is_null($agg['hi']) ? '-' : (float) $agg['hi'];
        $rows[$idx]['lo'] = is_null($agg['lo']) ? '-' : (float) $agg['lo'];
        $rows[$idx]['ct'] = (int) $agg['ct'];
        $rows[$idx]['trades'] = (int) $agg['trades'];
        $rows[$idx]['max'] = $maxW . '/' . $maxL;
        $rows[$idx]['dur'] = $fmtHMS($avgWinDur) . '/' . $fmtHMS($avgLosDur);
        $rows[$idx]['awin'] = $awin;
        $rows[$idx]['aloss'] = $aloss;
        $rows[$idx]['win'] = $winPct;
        $rows[$idx]['loss'] = $losPct;
      }
    }

    /* ==================================================
     * 4) Orden final DESC por fecha
     * ================================================== */

    usort($rows, function ($a, $b) {
      $ta = strtotime((string) ($a['openTime'] ?? $a['date'] ?? '')) ?: 0;
      $tb = strtotime((string) ($b['openTime'] ?? $b['date'] ?? '')) ?: 0;
      return $tb <=> $ta;
    });

    if (defined('WP_DEBUG') && WP_DEBUG) {
      foreach ($rows as $rr) {
        error_log(sprintf(
          '[DJ] ROW day=%s net=%s hi=%s lo=%s ct=%d trades=%d fees=%s awin=%s aloss=%s win=%s loss=%s max=%s dur=%s',
          (string) $rr['openTime'],
          is_numeric($rr['net']) ? number_format((float) $rr['net'], 2, '.', '') : (string) $rr['net'],
          (string) $rr['hi'],
          (string) $rr['lo'],
          (int) $rr['ct'],
          (int) $rr['trades'],
          is_numeric($rr['fees']) ? number_format((float) $rr['fees'], 2, '.', '') : (string) $rr['fees'],
          (string) $rr['awin'],
          (string) $rr['aloss'],
          (string) $rr['win'],
          (string) $rr['loss'],
          (string) $rr['max'],
          (string) $rr['dur']
        ));
      }
    }

    return ['rows' => $rows, 'per_page' => (int) $perPage];
  }
}

/* Backup Old function Taking Data from trade
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
          error_log('[DJ] toNY error: ' . $e->getMessage() . ' iso=' . $iso);
        return null;
      }
    };

    $fmtHMS = function (int $secs): string {
      if ($secs <= 0)
        return '00:00:00';
      $h = (int) floor($secs / 3600);
      $m = (int) floor(($secs % 3600) / 60);
      $s = (int) ($secs % 60);
      return sprintf('%02d:%02d:%02d', $h, $m, $s);
    };

    $perPageFetch = 500;
    $pageFetch = 1;
    $all = [];

    if (defined('WP_DEBUG') && WP_DEBUG)
      error_log('[DJ] fetch start account=' . $accountId);

    while (true) {
      $chunk = mt_trades_fetch_by_shortcode($accountId, 'CLOSED', $pageFetch, $perPageFetch);
      if (is_wp_error($chunk)) {
        if (defined('WP_DEBUG') && WP_DEBUG)
          error_log('[DJ] fetch error page=' . $pageFetch . ' msg=' . $chunk->get_error_message());
        break;
      }
      if (empty($chunk)) {
        if (defined('WP_DEBUG') && WP_DEBUG)
          error_log('[DJ] fetch empty page=' . $pageFetch);
        break;
      }

      $items = [];
      if (isset($chunk['data']) && is_array($chunk['data'])) {
        $items = $chunk['data'];
      } elseif (is_array($chunk)) {
        $items = $chunk;
      }

      if (defined('WP_DEBUG') && WP_DEBUG) {
        $pc = isset($chunk['meta']['pagesCount']) ? (int) $chunk['meta']['pagesCount'] : 0;
        $tc = isset($chunk['meta']['totalCount']) ? (int) $chunk['meta']['totalCount'] : 0;
        error_log('[DJ] fetch page=' . $pageFetch . ' got=' . count($items) . ' pagesCount=' . $pc . ' totalCount=' . $tc);
      }

      if (empty($items))
        break;

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

      $day = function_exists('mt_utc_to_eastern_ymd_cutoff')
        ? mt_utc_to_eastern_ymd_cutoff((string) $t['closeTime'], 18)
        : $closeNY->format('Y-m-d');

      $pnl = (float) ($t['pnl'] ?? 0);
      $lots = (int) ($t['lots'] ?? 0);
      $commission = (float) ($t['commission'] ?? 0);
      $durSecs = max(0, (int) round($closeNY->getTimestamp() - $openNY->getTimestamp()));

      if (!isset($byDay[$day])) {
        $byDay[$day] = [
          'net' => 0.0,
          'hi' => null,
          'lo' => null,
          'ct' => 0,
          'trades' => 0,
          'fees' => 0.0,
          'wins' => 0,
          'losses' => 0,
          'sumWin' => 0.0,
          'sumLoss' => 0.0,
          'durWinSecs' => 0,
          'durLossSecs' => 0,
          '_seq' => []
        ];
      }

      $D =& $byDay[$day];
      $D['trades'] += 1;
     ejemplo 
      $D['fees'] += $commission;
      $D['net'] += $pnl - $commission;

      if ($pnl > 0) {
        $D['hi'] = is_null($D['hi']) ? $pnl : max($D['hi'], $pnl);
      } elseif ($pnl < 0) {
        $D['lo'] = is_null($D['lo']) ? $pnl : min($D['lo'], $pnl);
      }

      if ($pnl > 0) {
        $D['wins'] += 1;
        $D['sumWin'] += $pnl;
        $D['durWinSecs'] += $durSecs;
      } elseif ($pnl < 0) {
        $D['losses'] += 1;
        $D['sumLoss'] += $pnl;
        $D['durLossSecs'] += $durSecs;
      }

      $D['_seq'][] = ['openTs' => $openNY->getTimestamp(), 'pnl' => $pnl];
      unset($D);
    }
    unset($D);

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
        'hi' => is_null($agg['hi']) ? '-' : (float) $agg['hi'],
        'lo' => is_null($agg['lo']) ? '-' : (float) $agg['lo'],
        'ct' => (int) $agg['ct'],
        'trades' => (int) $agg['trades'],
        'fees' => (float) $agg['fees'],
        'awin' => $awin,
        'aloss' => $aloss,
        'win' => $winPct,
        'loss' => $losPct,
        'max' => $maxW . '/' . $maxL,
        'dur' => $fmtHMS($avgWinDur) . '/' . $fmtHMS($avgLosDur),
      ];
    }

    usort($rows, function ($a, $b) {
      $ta = strtotime((string) ($a['openTime'] ?? $a['date'] ?? '')) ?: 0;
      $tb = strtotime((string) ($b['openTime'] ?? $b['date'] ?? '')) ?: 0;
      return $tb <=> $ta;
    });

    if (defined('WP_DEBUG') && WP_DEBUG) {
      foreach ($rows as $rr) {
        error_log(sprintf(
          '[DJ] ROW day=%s net=%s hi=%s lo=%s ct=%d trades=%d fees=%s max=%s dur=%s',
          (string) $rr['openTime'],
          is_numeric($rr['net']) ? number_format((float) $rr['net'], 2, '.', '') : (string) $rr['net'],
          (string) $rr['hi'],
          (string) $rr['lo'],
          (int) $rr['ct'],
          (int) $rr['trades'],
          is_numeric($rr['fees']) ? number_format((float) $rr['fees'], 2, '.', '') : (string) $rr['fees'],
          (string) $rr['max'],
          (string) $rr['dur']
        ));
      }
    }

    return ['rows' => $rows, 'per_page' => (int) $perPage];
  }
}
*/

/* === DAILY JOURNAL rows HTML === */
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

      $ymd = (string) ($r['openTime'] ?? $r['date'] ?? '');
      $day_iso = substr($ymd, 0, 10);

      if ($day_iso !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $day_iso)) {
        $day_ts = strtotime($day_iso . ' 00:00:00 UTC');
        $day_label = $day_ts ? gmdate('m/d/Y', $day_ts) : '-';
      } else {
        $day_label = '-';
      }

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

/* === Profile (My Profile modal) === */
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
add_action('wp_ajax_mt_get_profile', function () {
  check_ajax_referer('mt_profile_nonce', 'nonce');
  $res = mt_get_current_user_profile();
  if (!$res['ok'])
    wp_send_json_error(array('msg' => $res['msg']), 401);
  wp_send_json_success($res['data']);
});
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

/* === Utils varios === */
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
if (!function_exists('mt_subscription_id_for_order')) {
  function mt_subscription_id_for_order(int $order_id, int $user_id = 0): string
  {
    if ($order_id <= 0)
      return '';

    if ($user_id > 0) {
      $order = function_exists('wc_get_order') ? wc_get_order($order_id) : null;
      if (!$order)
        return '';
      $belongs = ((int) $order->get_user_id() === (int) $user_id);
      if (!$belongs)
        return '';
    }

    if (function_exists('wcs_get_subscriptions_for_order')) {
      $subs = wcs_get_subscriptions_for_order($order_id, array('order_type' => array('parent', 'renewal', 'switch')));
      if (is_array($subs) && !empty($subs)) {
        $pick = null;
        foreach ($subs as $sub) {
          if (is_object($sub) && method_exists($sub, 'get_id')) {
            $status = method_exists($sub, 'get_status') ? (string) $sub->get_status() : '';
            if (in_array($status, array('active', 'on-hold', 'pending-cancel'), true)) {
              $pick = $sub;
              break;
            }
            if ($pick === null)
              $pick = $sub;
          }
        }
        if ($pick)
          return (string) $pick->get_id();
      }
    }
    $maybe = get_post_meta($order_id, '_subscription_id', true);
    if (is_scalar($maybe) && (string) $maybe !== '')
      return (string) $maybe;
    return '';
  }
}

/* === Prepare accounts list for payout UI (logos centralizados) === */
if (!function_exists('mt_prepare_ui_payout')) {
  function mt_prepare_ui_payout(string $email): array
  {
    $out = ['items' => [], 'selected' => null];

    $clean_json = static function (string $raw) {
      $decoded = html_entity_decode($raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      $stripped = trim(wp_strip_all_tags($decoded));
      if (preg_match('/(\{.*\}|\[.*\])/s', $stripped, $m))
        $stripped = $m[1];
      $arr = json_decode($stripped, true);
      return (json_last_error() === JSON_ERROR_NONE && is_array($arr)) ? $arr : null;
    };
    $aget = static function (array $a, array $path, $def = null) {
      $v = $a;
      foreach ($path as $k) {
        if (is_array($v) && array_key_exists($k, $v))
          $v = $v[$k];
        else
          return $def;
      }return $v;
    };
    $b = static function ($v): bool {
      if (is_bool($v))
        return $v;
      if (is_numeric($v))
        return ((int) $v) !== 0;
      if (is_string($v)) {
        $t = strtolower(trim($v));
        if (in_array($t, ['1', 'true', 'yes', 'on'], true))
          return true;
        if (in_array($t, ['0', 'false', 'no', 'off', ''], true))
          return false;
      }
      return !empty($v);
    };
    $endsFunded = static function ($s) {
      return (bool) preg_match('/\bFunded\s*$/i', (string) $s);
    };
    $extract_account_size = static function (?string $label, ?string $desc): string {
      $src = trim((string) ($label ?: $desc ?: ''));
      if ($src === '')
        return '';
      $parts = explode('|', $src, 2);
      return trim($parts[0]);
    };

    $email = sanitize_email($email);
    if (!$email)
      return $out;

    $sc_accounts = sprintf('[mega_accounts_data email="%s" page="1" perpage="50" output="json"]', esc_attr($email));
    $raw = (string) do_shortcode($sc_accounts);
    $acc = $clean_json($raw);
    if (!$acc)
      return $out;

    $list = null;
    foreach ([['data', 'items'], ['items'], ['results'], ['data'], []] as $p) {
      $cand = $p ? $aget($acc, $p, null) : $acc;
      if (is_array($cand) && $cand && array_keys($cand) === range(0, count($cand) - 1)) {
        $list = $cand;
        break;
      }
    }
    if (!$list)
      return $out;

    $filtered = [];
    foreach ($list as $row) {
      if (!is_array($row))
        continue;
      $st = strtoupper(trim((string) ($row['status'] ?? '')));
      if ($st !== 'ACTIVE')
        continue;
      $desc = (string) $aget($row, ['program', 'description'], '');
      $label = (string) $aget($row, ['program', 'label'], '');
      if (!$endsFunded($desc) && !$endsFunded($label))
        continue;
      $row['_createdAt'] = (string) ($row['createdAt'] ?? '');
      $row['_programDesc'] = $desc;
      $row['_programLabel'] = $label;
      $row['_accountSize'] = $extract_account_size($label, $desc);
      $filtered[] = $row;
    }
    if (!$filtered)
      return $out;

    usort($filtered, static function ($a, $b) {
      $ta = strtotime((string) ($a['_createdAt'] ?? '')) ?: 0;
      $tb = strtotime((string) ($b['_createdAt'] ?? '')) ?: 0;
      return $tb <=> $ta;
    });
    $out['selected'] = (string) ($filtered[0]['id'] ?? '');

    $resolve_by_id = static function (string $id) use ($clean_json) {
      if (function_exists('mt_accounts_resolve_account_by_id')) {
        try {
          $acc = mt_accounts_resolve_account_by_id($id);
          if (is_array($acc))
            return $acc;
        } catch (\Throwable $e) {
        }
      }
      $sc = sprintf('[mega_account_data id="%s" page="1" perpage="50" output="json"]', esc_attr($id));
      $raw = (string) do_shortcode($sc);
      return $clean_json($raw) ?: null;
    };
    $fetch_elig = static function (string $internalId) {
      $data = mega_api_get_payout_eligibility($internalId);
      return is_wp_error($data) ? null : (is_array($data) ? $data : null);
    };

    $STATUS_KEYS = [
      'amountAvailable',
      'userKYCVerified',
      'accountIsFlat',
      'accountHasMetMinTradingDays',
      'accountHasProfitShare',
      'accountIsActive',
      'accountHasProfit',
      'accountHasWithdrawalAmount',
      'accountIsFunded',
      'accountHasPendingPayout',
      'accountConsistencyMet',
      'payoutHasMetMinTradingDays',
      'payoutCycleCheckPassed',
    ];

    foreach ($filtered as $row) {
      $id = (string) ($row['id'] ?? '');
      if (!$id)
        continue;

      $byId = $resolve_by_id($id);
      if (!is_array($byId))
        continue;

      $accountName = (string) ($byId['platform']['accountId'] ?? $byId['accountId'] ?? '');
      if ($accountName === '')
        continue;

      $platformRaw = (string) (
        ($byId['platform']['platform'] ?? $byId['platform']['name'] ?? '') ?:
        ($row['program']['platform'] ?? ($row['platform'] ?? ''))
      );
      $logo = MT_Accounts::platform_logo($platformRaw);

      $currentBalance = (float) (($byId['metrics']['currentBalance'] ?? 0) ?: 0);
      $startingBalance = (float) (($byId['program']['startingBalance'] ?? 0) ?: 0);

      $minMap = class_exists('MT_PAYOUT') ? MT_PAYOUT::MIN_BALANCE_MAP : [];
      $minimumBalance = isset($minMap[$startingBalance]) ? (float) $minMap[$startingBalance] : 0.0;

      $minWMap = class_exists('MT_PAYOUT') ? MT_PAYOUT::MIN_WITHDRAWAL_MAP : [];
      $minimumWithdrawal = isset($minWMap[$startingBalance]) ? (float) $minWMap[$startingBalance] : 0.0;

      $withdrawalRoom = max(0.0, $currentBalance - $minimumBalance);

      $elig = $fetch_elig($id) ?: [];
      $enabled = (bool) ($elig['payoutCycle']['enabled'] ?? false);
      $targetPassed = (bool) ($elig['payoutCycle']['targetPassed'] ?? false);
      $status = (array) ($elig['accountStatus'] ?? []);

      $allStatusOK = true;
      foreach ($STATUS_KEYS as $k) {
        if (!($status[$k] ?? false)) {
          $allStatusOK = false;
          break;
        }
      }

      $maxWithdrawalApi = (float) ($elig['payoutCycle']['maxWithdrawal'] ?? ($byId['payout']['payoutCycle']['maxWithdrawal'] ?? 0));
      $eligibleBase = ($enabled && $targetPassed && $allStatusOK);
      $eligibleForPayout = ($eligibleBase && ($withdrawalRoom >= $minimumWithdrawal));
      $maxWithdrawalUI = $eligibleForPayout ? max(0.0, min($withdrawalRoom, (float) $maxWithdrawalApi)) : 0.0;

      $badge = $eligibleForPayout
        ? ['text' => 'Eligible', 'class' => 'badge-mega badge-mega-fit-content badge-mega-funded badge-mega-sm']
        : ['text' => 'Ineligible', 'class' => 'badge-mega badge-mega-error badge-mega-fit-content badge-mega-sm'];

      $out['items'][] = [
        'id' => $id,
        'logo' => $logo,
        'accountSize' => (string) ($row['_accountSize'] ?? ''),
        'accountName' => $accountName,
        'platformAccountId' => $accountName,
        'programLabel' => (string) ($row['_programLabel'] ?? ''),
        'programDescription' => (string) ($row['_programDesc'] ?? ''),
        'eligible' => $eligibleBase,
        'eligibleForPayout' => $eligibleForPayout,
        'meta' => [
          'userKYCVerified' => !empty($status['userKYCVerified']),
          'maxWithdrawal' => is_numeric($maxWithdrawalApi) ? ($maxWithdrawalApi + 0) : null,
          'maxWithdrawalApi' => is_numeric($maxWithdrawalApi) ? ($maxWithdrawalApi + 0) : null,
          'maxWithdrawalUI' => $maxWithdrawalUI,
          'currentBalance' => $currentBalance,
          'startingBalance' => $startingBalance,
          'minimumBalance' => $minimumBalance,
          'withdrawalRoom' => $withdrawalRoom,
          'minWithdrawal' => $minimumWithdrawal,
        ],
        'badge' => $badge,
      ];
    }

    return $out;
  }
}

if (!function_exists('mt_get_account_payout_eligibility')) {
  /**
   * Devuelve elegibilidad de payout para UNA cuenta (por accountId interno).
   *
   * @return array {
   *   @type string  id
   *   @type bool    eligibleForPayout
   *   @type bool    eligibleBase
   *   @type float   maxWithdrawalUI
   *   @type array   meta
   * }
   */
  function mt_get_account_payout_eligibility(string $account_id): array
  {
    $account_id = trim((string) $account_id);
    if ($account_id === '') {
      return [
        'id' => '',
        'eligibleBase' => false,
        'eligibleForPayout' => false,
        'maxWithdrawalUI' => 0.0,
        'meta' => [],
      ];
    }

    // Resolver cuenta
    $byId = null;
    if (function_exists('mt_accounts_resolve_account_by_id')) {
      try {
        $byId = mt_accounts_resolve_account_by_id($account_id);
      } catch (\Throwable $e) {
        $byId = null;
      }
    }

    if (!is_array($byId)) {
      return [
        'id' => $account_id,
        'eligibleBase' => false,
        'eligibleForPayout' => false,
        'maxWithdrawalUI' => 0.0,
        'meta' => [],
      ];
    }

    // Datos base (igual lógica que en mt_prepare_ui_payout)
    $currentBalance = (float) (($byId['metrics']['currentBalance'] ?? 0) ?: 0);
    $startingBalance = (float) (($byId['program']['startingBalance'] ?? 0) ?: 0);

    $minMap = class_exists('MT_PAYOUT') ? MT_PAYOUT::MIN_BALANCE_MAP : [];
    $minimumBalance = isset($minMap[$startingBalance]) ? (float) $minMap[$startingBalance] : 0.0;

    $minWMap = class_exists('MT_PAYOUT') ? MT_PAYOUT::MIN_WITHDRAWAL_MAP : [];
    $minimumWithdrawal = isset($minWMap[$startingBalance]) ? (float) $minWMap[$startingBalance] : 0.0;

    $withdrawalRoom = max(0.0, $currentBalance - $minimumBalance);

    // Llamamos al mega_api_get_payout_eligibility para esta cuenta
    $elig = [];
    if (function_exists('mega_api_get_payout_eligibility')) {
      $raw = mega_api_get_payout_eligibility($account_id);
      if (!is_wp_error($raw) && is_array($raw)) {
        $elig = $raw;
      }
    }

    $enabled = (bool) ($elig['payoutCycle']['enabled'] ?? false);
    $targetPassed = (bool) ($elig['payoutCycle']['targetPassed'] ?? false);
    $status = (array) ($elig['accountStatus'] ?? []);

    $STATUS_KEYS = [
      'amountAvailable',
      'userKYCVerified',
      'accountIsFlat',
      'accountHasMetMinTradingDays',
      'accountHasProfitShare',
      'accountIsActive',
      'accountHasProfit',
      'accountHasWithdrawalAmount',
      'accountIsFunded',
      'accountHasPendingPayout',
      'accountConsistencyMet',
      'payoutHasMetMinTradingDays',
      'payoutCycleCheckPassed',
    ];

    $allStatusOK = true;
    foreach ($STATUS_KEYS as $k) {
      if (empty($status[$k])) {
        $allStatusOK = false;
        break;
      }
    }

    $maxWithdrawalApi = (float) ($elig['payoutCycle']['maxWithdrawal']
      ?? ($byId['payout']['payoutCycle']['maxWithdrawal'] ?? 0));

    $eligibleBase = ($enabled && $targetPassed && $allStatusOK);
    $eligibleForPayout = ($eligibleBase && ($withdrawalRoom >= $minimumWithdrawal));
    $maxWithdrawalUI = $eligibleForPayout
      ? max(0.0, min($withdrawalRoom, (float) $maxWithdrawalApi))
      : 0.0;

    return [
      'id' => (string) $account_id,
      'eligibleBase' => $eligibleBase,
      'eligibleForPayout' => $eligibleForPayout,
      'maxWithdrawalUI' => $maxWithdrawalUI,
      'meta' => [
        'userKYCVerified' => !empty($status['userKYCVerified']),
        'maxWithdrawal' => $maxWithdrawalApi,
        'maxWithdrawalApi' => $maxWithdrawalApi,
        'maxWithdrawalUI' => $maxWithdrawalUI,
        'currentBalance' => $currentBalance,
        'startingBalance' => $startingBalance,
        'minimumBalance' => $minimumBalance,
        'withdrawalRoom' => $withdrawalRoom,
        'minWithdrawal' => $minimumWithdrawal,
        'accountNr' => isset($byId['accountNr']) ? (string) $byId['accountNr'] : '',
        'platformAccountId' => isset($byId['platform']['accountId']) ? (string) $byId['platform']['accountId'] : '',
      ],
    ];
  }
}


if (!function_exists('mega_api_get_bulk')) {
  /**
   * Realiza múltiples solicitudes GET concurrentes usando cURL multi.
   *
   * @param array $endpoints Lista de endpoints relativos o URLs completas.
   * @param array $extraHeaders (opcional) Headers adicionales.
   * @return array Resultados por URL.
   */
  function mega_api_get_bulk(array $endpoints, array $extraHeaders = []): array
  {
    if (empty($endpoints)) {
      return ['error' => 'No endpoints provided'];
    }

    // Obtener configuración base y API key
    $opts = function_exists('mega_api_options') ? mega_api_options() : [];
    $base_url = isset($opts['base_url']) ? rtrim($opts['base_url'], '/') : '';
    $api_key = isset($opts['api_key']) ? $opts['api_key'] : '';

    // Construir headers comunes
    $headers = array_merge([
      'X-API-KEY: ' . $api_key,
      'Accept: application/json',
    ], $extraHeaders);

    $multiHandle = curl_multi_init();
    $curlHandles = [];
    $results = [];

    foreach ($endpoints as $key => $endpoint) {
      // Soporta tanto URLs completas como relativas
      $url = preg_match('/^https?:\/\//', $endpoint)
        ? $endpoint
        : "{$base_url}/" . ltrim($endpoint, '/');

      $ch = curl_init();
      curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => $headers,
      ]);

      curl_multi_add_handle($multiHandle, $ch);
      $curlHandles[$key] = $ch;
    }

    $running = null;
    do {
      $status = curl_multi_exec($multiHandle, $running);
      if ($status > 0) {
        error_log('Mega API MultiCurl error: ' . curl_multi_strerror($status));
      }
      curl_multi_select($multiHandle);
    } while ($running > 0);

    foreach ($curlHandles as $key => $ch) {
      $response = curl_multi_getcontent($ch);
      $error = curl_error($ch);
      $info = curl_getinfo($ch);

      if ($error) {
        $results[$key] = [
          'error' => $error,
          'url' => $info['url'] ?? null,
        ];
      } else {
        $decoded = json_decode($response, true);
        $results[$key] = json_last_error() === JSON_ERROR_NONE
          ? $decoded
          : ['error' => 'Invalid JSON', 'raw' => $response];
      }

      curl_multi_remove_handle($multiHandle, $ch);
      curl_close($ch);
    }

    curl_multi_close($multiHandle);

    return $results;
  }
}

function mt_account_overview_business_logic($accountIds = [])
{
  /* === Estado base === */
  $mt_user_email = '';
  $mt_user_email_api = '';
  $mt_account_ui = ['current' => null, 'accounts' => []];
  $mt_selected_id = '';
  $mt_performance = [];
  $mt_fetch_variant = '';
  $mt_cnt_plain = 0;
  $mt_cnt_encoded = 0;
  $mt_feature_content = [];
  $mt_account_data = [];
  $mt_daily_journal = [];
  $__selected_subscription_id = '';
  $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : '/cart';
  $__can_manage_subscription = true;
  $mt_chart = [];

  if (!is_user_logged_in()) {
    return ['error' => 'You must be logged in to access this page.'];
  }

  $u = wp_get_current_user();
  $raw_email = (string) ($u->user_email ?? '');

  // Saneado (lowercase, trim, validación, urlencode para API)
  $san = function_exists('mt_sanitize_email')
    ? mt_sanitize_email($raw_email)
    : [
      'ok' => true,
      'email' => strtolower(trim($raw_email)),
      'api' => rawurlencode(strtolower(trim($raw_email))),
      'error' => '',
    ];

  if (!empty($san['ok'])) {
    $mt_user_email = (string) ($san['email'] ?? ''); // plain, normalizado
    $mt_user_email_api = (string) ($san['api'] ?? '');   // encoded (%2B, %40, ...)

    /* === 1) Traer cuentas (cache 30s, plain -> encoded) === */
    list($accounts, $mt_fetch_variant) = mt_fetch_accounts_cached($mt_user_email, $mt_user_email_api);

    // === Agreement Modal (con caché 5min) ===
    $__mt_agreement = mt_get_agreement_status_cached($mt_user_email_api);

    $__mt_agreement_url = (is_array($__mt_agreement) && !empty($__mt_agreement['agreementURL']))
      ? (string) $__mt_agreement['agreementURL']
      : '';

    $__mt_agreement_show = (is_array($__mt_agreement)
      && array_key_exists('agreementSigned', $__mt_agreement)
      && $__mt_agreement['agreementSigned'] === false) ? '1' : '0';

    /* === Preferencia de cookie para cuenta seleccionada (si existe y es válida) === */
    $cookie_selected_id = '';
    if (is_user_logged_in()) {
      $uid = get_current_user_id();
      $cookie_keys = array(
        'mt:lastAccountId' . ($uid ? (':' . $uid) : ''),
        'mt:lastAccountId',
      );
      foreach ($cookie_keys as $ck) {
        if (!empty($_COOKIE[$ck])) {
          $cookie_selected_id = sanitize_text_field(wp_unslash($_COOKIE[$ck]));
          break;
        }
      }
    }

    // 1️⃣ Obtener todas las cuentas (válidas y con error)
    $accounts = MT_Api::fetch_accounts_bulk($raw_email, $accountIds);

    // 2️⃣ Crear un nuevo array solo con las válidas
    $valid_accounts = [];
    foreach ($accounts as $id => $account) {
      if (is_array($account) && !isset($account['error'])) {
        $valid_accounts[$id] = $account;
      }
    }

    /* === 2) Preparar UI SIEMPRE (todas las cuentas; Active y no Active) === */
    if (class_exists('MT_Accounts')) {
      $mt_account_ui = MT_Accounts::prepare_ui_v2(accounts: $valid_accounts);
    }

    /* IDs válidos (de prepare_ui) */
    $__valid_ids = array();
    if (!empty($mt_account_ui['accounts']) && is_array($mt_account_ui['accounts'])) {
      foreach ($mt_account_ui['accounts'] as $row) {
        if (!empty($row['id']))
          $__valid_ids[(string) $row['id']] = true;
      }
    }
    if (!empty($mt_account_ui['current']['id'])) {
      $__valid_ids[(string) $mt_account_ui['current']['id']] = true;
    }

    /* Resolver seleccionado con prioridad: ?acc → cookie → current */
    $param_acc = isset($_GET['acc']) ? sanitize_text_field((string) $_GET['acc']) : '';

    if ($param_acc && isset($__valid_ids[$param_acc])) {
      $mt_selected_id = $param_acc;
    } elseif ($cookie_selected_id && isset($__valid_ids[$cookie_selected_id])) {
      $mt_selected_id = $cookie_selected_id;
    } else {
      $mt_selected_id = (string) ($mt_account_ui['current']['id'] ?? '');
    }

    /* === 3) Resolver cuenta seleccionada === */
    if ($mt_selected_id === '') {
      $mt_selected_id = (string) ($mt_account_ui['current']['id'] ?? '');
    }

    // === Resolver la cuenta una sola vez y construir payloads ===
    $resolved = (!empty($mt_selected_id) && function_exists('mt_accounts_resolve_account_by_id'))
      ? mt_accounts_resolve_account_by_id($mt_selected_id)
      : null;

    if ($resolved) {
      // Account data (re-usa $resolved, evita resolve duplicado)
      if (function_exists('mt_accounts_build_account_data')) {
        $mt_account_data = mt_accounts_build_account_data($resolved);
      }
    }

    /* === Mapa id => order y order activo === */
    $__orders_map_by_id = [];

    if (!empty($mt_account_ui['accounts']) && is_array($mt_account_ui['accounts'])) {
      foreach ($mt_account_ui['accounts'] as $row) {
        $id = (string) ($row['id'] ?? '');
        $ord = (int) ($row['order'] ?? $row['orderId'] ?? $row['orderID'] ?? 0);
        if ($id !== '')
          $__orders_map_by_id[$id] = $ord;
      }
    }
    if (!empty($mt_account_ui['current']['id'])) {
      $cid = (string) $mt_account_ui['current']['id'];
      if (!isset($__orders_map_by_id[$cid])) {
        $__orders_map_by_id[$cid] = (int) ($mt_account_ui['current']['order'] ?? $mt_account_ui['current']['orderId'] ?? $mt_account_ui['current']['orderID'] ?? 0);
      }
    }

    $__active_order_id = 0;
    if ($mt_selected_id !== '') {
      $__active_order_id = (int) ($__orders_map_by_id[$mt_selected_id] ?? 0);
      if (!$__active_order_id && !empty($mt_account_ui['current']) && (string) $mt_account_ui['current']['id'] === (string) $mt_selected_id) {
        $__active_order_id = (int) ($mt_account_ui['current']['order'] ?? 0);
      }
    }

    $__mt_selected_status = (string) (
      $resolved['status']
      ?? ($mt_account_ui['current']['status'] ?? '')
    );

    $__breach_key = 'BREACHED';
    if (class_exists('Label')) {
      $__breach_key = \Label::ACCOUNT_STATUS_MAP['BREACHED'] ?? 'BREACHED';
    }

    $__mt_breach_show = (strcasecmp($__mt_selected_status, $__breach_key) === 0) ? '1' : '0';
    $__breach_reset_url = '/my-account/reset';
    $__reset_product_id = (string) (
      $resolved['rules']['resetProductId']
      ?? ($mt_account_ui['current']['resetProductId'] ?? '')
    );

    if ($__reset_product_id !== '' && function_exists('wc_get_checkout_url')) {
      $__breach_reset_url = wc_get_checkout_url() . '?add-to-cart=' . urlencode($__reset_product_id);
    }

    /* ==== Passed/Activation meta & helpers ==== */

    $__normalize_id = function ($v) {
      if ($v === null)
        return '';
      $s = trim((string) $v);
      $sl = strtolower($s);
      return ($s === '' || $s === '0' || $sl === 'null') ? '' : $s;
    };

    $__status_raw = (string) ($resolved['status'] ?? ($mt_account_ui['current']['status'] ?? ''));
    $__status_norm = strtoupper(trim($__status_raw));

    $__activation_product_id = (string) (
      $resolved['rules']['activationProductId'] ??
      ($mt_account_ui['current']['activationProductId'] ?? '')
    );

    $__has_activation_id = ($__normalize_id($__activation_product_id) !== '');

    $__activation_url = ($__has_activation_id && function_exists('wc_get_checkout_url'))
      ? wc_get_checkout_url() . '?add-to-cart=' . urlencode($__activation_product_id)
      : '#';

    $__mt_account_passed_show = (in_array($__status_norm, ['PENDING_ACTIVATION', 'PASSED'], true)) ? '1' : '0';

    $__note_passed_with_id = Label::META_ACCOUNT_OVERVIEW['passed_modal_note_status_passed_w_activation_id'];
    $__note_passed_no_id = Label::META_ACCOUNT_OVERVIEW['passed_modal_note_status_passed_no_activation_id'];

    $__body_subtitle = Label::META_ACCOUNT_OVERVIEW['passed_modal_body_subtitle_default'];
    $__body_subtitle_w_activation_id = Label::META_ACCOUNT_OVERVIEW['passed_modal_body_subtitle_w_activation_id'];
    $__body_subtitle_no_activation_id = Label::META_ACCOUNT_OVERVIEW['passed_modal_body_subtitle_no_activation_id'];

    $__note_text = '';
    $__show_note = false;
    $__body_text = $__body_subtitle;

    if ($__status_norm === 'ACTIVATION_PENDING') {
      $__status_norm = 'PENDING_ACTIVATION';
    }

    if ($__status_norm === 'PASSED' && $__has_activation_id) {
      $__show_note = true;
      $__note_text = $__note_passed_with_id;
    } elseif ($__status_norm === 'PASSED' && !$__has_activation_id) {
      $__show_note = true;
      $__note_text = $__note_passed_no_id;
    }

    if ($__status_norm === 'PENDING_ACTIVATION' && $__has_activation_id) {
      $__body_text = $__body_subtitle;
    } elseif ($__status_norm === 'PASSED' && $__has_activation_id) {
      $__body_text = $__body_subtitle_w_activation_id;
    } elseif ($__status_norm === 'PASSED' && !$__has_activation_id) {
      $__body_text = $__body_subtitle_no_activation_id;
    }

    $__btn_classes = [];
    if ($__status_norm === 'PENDING_ACTIVATION' && $__has_activation_id) {
      // botón activo
    } elseif ($__status_norm === 'PASSED' && $__has_activation_id) {
      $__btn_classes[] = 'disabled';
    } elseif ($__status_norm === 'PASSED' && !$__has_activation_id) {
      $__btn_classes[] = 'd-none';
    } else {
      $__btn_classes[] = 'd-none';
    }
    $__btn_classes_attr = implode(' ', $__btn_classes);

    $__main_product_id = (string) (
      $resolved['rules']['mainProductId']
      ?? ($mt_account_ui['current']['mainProductId'] ?? '')
    );

    if (!empty($mt_account_ui['accounts']) || !empty($mt_account_ui['current'])) {
      $selRow = null;

      if (!empty($mt_selected_id) && !empty($mt_account_ui['accounts'])) {
        foreach ($mt_account_ui['accounts'] as $row) {
          if ((string) ($row['id'] ?? '') === (string) $mt_selected_id) {
            $selRow = $row;
            break;
          }
        }
      }
      if (
        !$selRow && !empty($mt_account_ui['current']) &&
        (empty($mt_selected_id) || (string) $mt_account_ui['current']['id'] === (string) $mt_selected_id)
      ) {
        $selRow = $mt_account_ui['current'];
      }

      if (is_array($selRow)) {
        $__selected_subscription_id = (string) ($selRow['subscriptionId'] ?? '');
        $hasSubLegacy = !empty($selRow['hasSubscription']);
        $__can_manage_subscription = $__selected_subscription_id !== '' || $hasSubLegacy;
      }
    }
  } else {
    return ['error' => 'Invalid email'];
  }

  return [
    'mt_user_email' => $mt_user_email,
    'mt_account_ui' => $mt_account_ui,
    'mt_user_email_api' => $mt_user_email_api,
    'mt_selected_id' => $mt_selected_id,
    //        '$__mt_breach_show' => $__mt_breach_show,
//        '$__mt_account_passed_show' => $__mt_account_passed_show,
//        '$__selected_subscription_id' => $__selected_subscription_id,
//        '$__can_manage_subscription' => $__can_manage_subscription,
//        '$__active_order_id' => $__active_order_id,
//        '$__mt_agreement_show' => $__mt_agreement_show,
//        '$__mt_agreement_url' => $__mt_agreement_url,
//        '$__main_product_id' => $__main_product_id,
//        '$__reset_product_id' => $__reset_product_id,
//        '$__breach_reset_url' => $__breach_reset_url,
//        '$__note_passed_with_id' => $__note_passed_with_id,
//        '$__note_passed_no_id' => $__note_passed_no_id,
//        '$__activation_product_id' => $__activation_product_id,
//        '$__body_subtitle' => $__body_subtitle,
//        '$__body_subtitle_w_activation_id' => $__body_subtitle_w_activation_id,
//        '$__body_subtitle_no_activation_id' => $__body_subtitle_no_activation_id,
//        '$__body_text' => $__body_text,
//        '$__note_text' => $__note_text,
//        '$__show_note' => $__show_note,
//        '$__status_norm' => $__status_norm,
//        '$__activation_url' => $__activation_url,
//        '$__btn_classes_attr' => $__btn_classes_attr,
    'mt_account_data' => $mt_account_data,
    'mt_active_order_id' => isset($__active_order_id) ? (int) $__active_order_id : 0,
  ];
}


// ================= TRADES: fetch liviano para tabla (OPEN/CLOSED) =================
if (!function_exists('mt_trades_history_fetch')) {
  function mt_trades_history_fetch(string $accountId, string $type = 'CLOSED', int $page = 1, int $perPage = 25): array
  {
    $t = strtoupper($type) === 'OPEN' ? 'OPEN' : 'CLOSED';
    // Usa el shortcode existente para minimizar latencia y reutilizar caché del plugin
    $shortcode = sprintf(
      '[mega_trades_data id="%s" type="%s" page="%d" perpage="%d" output="json"]',
      esc_attr($accountId),
      esc_attr($t),
      max(1, $page),
      max(1, $perPage)
    );

    $json = do_shortcode($shortcode);
    if (empty($json)) {
      return [
        'type' => $t,
        'page' => $page,
        'perPage' => $perPage,
        'total' => 0,
        'pages' => 0,
        'records' => [],
      ];
    }

    $payload = json_decode($json, true);
    if (!is_array($payload)) {
      return [
        'type' => $t,
        'page' => $page,
        'perPage' => $perPage,
        'total' => 0,
        'pages' => 0,
        'records' => [],
      ];
    }

    $meta = $payload['meta'] ?? [];
    $rows = $payload['data'] ?? [];
    $total = isset($meta['totalCount']) ? (int) $meta['totalCount'] : count($rows);
    $pages = isset($meta['pagesCount']) ? (int) $meta['pagesCount'] : (int) ceil($total / max(1, $perPage));

    $records = [];
    foreach ($rows as $r) {
      // Campos crudos
      $openPrice = (float) ($r['openPrice'] ?? 0);
      $closePrice = (float) ($r['closePrice'] ?? 0);
      $pnl = (float) ($r['pnl'] ?? 0);
      $comm = (float) ($r['commission'] ?? 0);
      $openTime = $r['openTime'] ?? null;
      $closeTime = $r['closeTime'] ?? null;

      // Fechas con cutoff si existe helper
      $openDateStr = '-';
      $closeDateStr = '-';
      if ($openTime) {
        if (function_exists('mt_cutoff_date')) {
          $openDateStr = mt_cutoff_date($openTime); // MM/DD/YYYY
        } else {
          $openDateStr = gmdate('m/d/Y', strtotime($openTime));
        }
      }
      if ($closeTime) {
        if (function_exists('mt_cutoff_date')) {
          $closeDateStr = mt_cutoff_date($closeTime);
        } else {
          $closeDateStr = gmdate('m/d/Y', strtotime($closeTime));
        }
      }

      // Horas (HH:MM) para las nuevas columnas
      $openTimeStr = '-';
      $closeTimeStr = '-';
      try {
        if ($openTime) {
          $dtO = new DateTime($openTime);
          // si quieres usar la zona WP:
          if (function_exists('wp_timezone')) {
            $dtO->setTimezone(wp_timezone());
          }
          $openTimeStr = $dtO->format('H:i:s'); // 24h: 18:31
        }
        if ($closeTime) {
          $dtC = new DateTime($closeTime);
          if (function_exists('wp_timezone')) {
            $dtC->setTimezone(wp_timezone());
          }
          $closeTimeStr = $dtC->format('H:i:s');
        }
      } catch (Exception $e) {
        // silencioso, dejamos '-'
      }

      // Duración (formateada como “Xm Ys”)
      $duration = '-';
      if ($openTime && $closeTime) {
        $sec = max(0, strtotime($closeTime) - strtotime($openTime));
        $h = floor($sec / 3600);
        $m = floor(($sec % 3600) / 60);
        $s = $sec % 60;
        $duration = $h > 0 ? sprintf('%dh %02dm %02ds', $h, $m, $s) : sprintf('%dm %02ds', $m, $s);
      }

      // Net y status
      $net = $pnl - $comm;
      $status = $t === 'OPEN' ? 'OPEN' : ($net < 0 ? 'LOSS' : 'WIN');

      $records[] = [
        'symbol' => $r['symbol'] ?? '-',
        'side' => $r['side'] ?? '-',
        'closeDate' => $closeDateStr,
        'closeTimeStr' => $closeTimeStr,
        'net' => $net,
        'duration' => $duration,
        'avgEntry' => $openPrice,
        'avgExit' => $closePrice,
        'openDate' => $openDateStr,
        'openTimeStr' => $openTimeStr,
        'status' => $status,
      ];
    }


    return [
      'type' => $t,
      'page' => $page,
      'perPage' => $perPage,
      'total' => $total,
      'pages' => $pages,
      'records' => $records,
    ];
  }
}
// === Platform term resolver (API -> Woo term) ===
if (!function_exists('mt_platform_term_by_api_key')) {
  function mt_platform_term_by_api_key(string $api_key) {
    $api_key = trim($api_key);
    if ($api_key === '') return null;

    $terms = get_terms([
      'taxonomy'   => 'pa_platform',
      'hide_empty' => false,
      'meta_query' => [
        [
          'key'     => 'mt_api_platform_key',
          'value'   => $api_key,
          'compare' => '=',
        ],
      ],
      'number' => 1,
    ]);

    if (is_wp_error($terms) || empty($terms)) return null;
    return $terms[0];
  }
}

if (!function_exists('mt_platform_icon_url_from_term')) {
  function mt_platform_icon_url_from_term($term): string {
    if (!$term || empty($term->term_id)) return '';

    // ✅ TU PLUGIN: guarda el attachment ID aquí
    $image_id = (int) get_term_meta($term->term_id, 'attribute_image_id', true);
    if ($image_id > 0) {
      $url = wp_get_attachment_image_url($image_id, 'full');
      if ($url) return $url;
    }

    // Intento 1: si algún plugin guarda un attachment_id en una meta
    $maybe_id = (int) get_term_meta($term->term_id, 'attribute_icon_id', true);
    if ($maybe_id > 0) {
      $url = wp_get_attachment_image_url($maybe_id, 'full');
      if ($url) return $url;
    }

    // Intento 2: algunos guardan directamente una URL
    $maybe_url = (string) get_term_meta($term->term_id, 'attribute_icon', true);
    if ($maybe_url !== '') return esc_url_raw($maybe_url);

    // Intento 3: fallback típico Woo
    $thumb_id = (int) get_term_meta($term->term_id, 'thumbnail_id', true);
    if ($thumb_id > 0) {
      $url = wp_get_attachment_image_url($thumb_id, 'full');
      if ($url) return $url;
    }

    return '';
  }
}

