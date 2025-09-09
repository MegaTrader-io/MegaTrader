<?php
defined('ABSPATH') || exit;

class MT_Accounts {
  // Mapea estados
  public static function is_active_status($status): bool {
    $s = strtolower(trim((string)$status));
    return in_array($s, ['active','approved','open','enabled','running','live','activated'], true);
  }
  public static function badge_class(string $status): string {
    $s = strtolower(trim($status));
    if (in_array($s, ['active','approved','open','enabled','running','live','activated'], true)) return 'badge-mega-active';
    if (in_array($s, ['pending','pending-cancel','paused','submitted'], true)) return 'badge-mega-pending';
    if (in_array($s, ['rejected','closed','disabled'], true)) return 'badge-mega-danger';
    return 'badge-mega-default';
  }
  public static function dot_class(string $status): string {
    $s = strtolower(trim($status));
    if (in_array($s, ['active','approved','open','enabled','running','live','activated'], true)) return 'dot-status-active';
    if (in_array($s, ['pending','pending-cancel','paused','submitted','on-hold','refunded'], true)) return 'dot-status-pending';
    if (in_array($s, ['rejected','closed','disabled','cancelled','failed','expired'], true)) return 'dot-status-danger';
    return 'dot-status-default';
  }
  private static function norm($s): string {
    return preg_replace('/[^a-z0-9]+/','', strtolower((string)$s));
  }

  // "50k Elite Plan | DXXT | Evaluation" -> ["50K", "Elite Plan"]
  public static function parse_program_label(string $label, $startingBalance = null): array {
    $head = $label;
    $pos = strpos($label, '|');
    if ($pos !== false) $head = substr($label, 0, $pos);
    $head = trim($head);

    $size = '';
    $name = $head;

    if (preg_match('/^\s*([0-9]+k)\b/i', $head, $m)) {
      $size = strtoupper($m[1]);
      $name = trim(substr($head, strlen($m[1])));
    } elseif (is_numeric($startingBalance)) {
      $k = round(((int)$startingBalance)/1000);
      if ($k > 0) $size = strtoupper($k.'k');
    }

    if ($name === '') $name = 'Account';
    return [$size, $name];
  }

  // Prefer program.platform; fallback: 2º segmento del label
  public static function extract_platform(array $acc): string {
    $p = trim((string)($acc['program']['platform'] ?? ''));
    if ($p !== '') return $p;
    $label = (string)($acc['program']['label'] ?? ($acc['program']['description'] ?? ''));
    $parts = array_map('trim', explode('|', $label));
    return $parts[1] ?? '';
  }

  /**
   * Transforma respuesta cruda -> payload UI para account-selection.
   * return ['current'=>[...], 'accounts'=>[...]]
   */
  public static function prepare_ui(array $accounts): array {
    // logos
    $DEFAULT_LOGO = 'https://subscriptions.megatrader.io/wp-content/uploads/2025/07/Stylecolor-Sizelg.svg';
    $PLATFORM_LOGOS = [
      self::norm('MegaTrader')  => $DEFAULT_LOGO,
      self::norm('NinjaTrader') => 'https://subscriptions.megatrader.io/wp-content/uploads/2025/02/icon_ninjatrader.svg',
      self::norm('Tradovate')   => 'https://subscriptions.megatrader.io/wp-content/uploads/2025/02/icon_tradovate.svg',
      self::norm('Quantower')   => 'https://subscriptions.megatrader.io/wp-content/uploads/2025/02/icon_quantower.svg',
    ];

    // activas ordenadas por createdAt desc
    $active = array_values(array_filter($accounts, fn($a) => self::is_active_status($a['status'] ?? '')));
    usort($active, function($a,$b){
      $ta = strtotime((string)($a['createdAt'] ?? '')) ?: 0;
      $tb = strtotime((string)($b['createdAt'] ?? '')) ?: 0;
      return $tb <=> $ta;
    });

    if (empty($active)) return ['current'=>null,'accounts'=>[]];

    // current
    $cur = $active[0];
    $pl  = (string)($cur['program']['label'] ?? ($cur['program']['description'] ?? ''));
    $sb  = $cur['program']['startingBalance'] ?? null;
    [$size, $name] = self::parse_program_label($pl, $sb);
    $current = [
      'id'         => (string)($cur['id'] ?? ''),
      'status'     => (string)($cur['status'] ?? ''),
      'badgeClass' => self::badge_class($cur['status'] ?? ''),
      'size'       => $size,
      'name'       => $name,
    ];

    // modal
    $modal = [];
    foreach ($active as $a) {
      $pl2 = (string)($a['program']['label'] ?? ($a['program']['description'] ?? 'Account'));
      $sb2 = $a['program']['startingBalance'] ?? null;
      [$sz, $nm] = self::parse_program_label($pl2, $sb2);

      $platform = self::extract_platform($a);
      $logo = $DEFAULT_LOGO;
      if ($platform !== '') {
        $key = self::norm($platform);
        if (isset($PLATFORM_LOGOS[$key])) $logo = $PLATFORM_LOGOS[$key];
      }

      $modal[] = [
        'id'        => (string)($a['id'] ?? ''),
        'status'    => (string)($a['status'] ?? ''),
        'dotClass'  => self::dot_class($a['status'] ?? ''),
        'size'      => $sz,
        'name'      => $nm,
        'platform'  => $platform,
        'logo'      => $logo,
        'createdAt' => (string)($a['createdAt'] ?? ''),
      ];
    }

    return ['current'=>$current, 'accounts'=>$modal];
  }
}
