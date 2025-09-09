<?php
defined('ABSPATH') || exit;

class MT_Api {
  /**
   * Fetch accounts via existing shortcode.
   */
  public static function fetch_accounts_by_email(string $email, int $page = 1, int $perPage = 50): array {
    if (!$email) return [];

    $key = 'mt_acc_' . md5(strtolower($email) . "_$page_$perPage");
    $cached = get_transient($key);
    if ($cached !== false) return $cached;

    $sc = sprintf('[mega_accounts_data email="%s" page="%d" perpage="%d" output="json"]',
      esc_attr($email), $page, $perPage
    );

    $raw  = do_shortcode($sc);
    $data = json_decode($raw, true);

    if (!is_array($data)) $data = [];
    if (isset($data['error'])) $data = [];
    if (isset($data['items']) && is_array($data['items'])) $data = $data['items'];
    if (isset($data['data'])  && is_array($data['data']))  $data = $data['data'];

    set_transient($key, $data, 60); // cache 60s (ajústalo si quieres)
    return $data;
  }
}
