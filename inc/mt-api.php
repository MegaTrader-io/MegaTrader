<?php
defined('ABSPATH') || exit;

class MT_Api {
  /**
   * Fetch accounts via existing shortcode (1 sola llamada).
   */
  public static function fetch_accounts_by_email(string $email, int $page = 1, int $perPage = 50): array {
    if (!$email) return [];
    $sc = sprintf(
      '[mega_accounts_data email="%s" page="%d" perpage="%d" output="json"]',
      esc_attr($email), $page, $perPage
    );
    $raw  = do_shortcode($sc);
    $data = json_decode($raw, true);

    if (!is_array($data)) return [];
    if (isset($data['error'])) return [];
    if (isset($data['items']) && is_array($data['items'])) return $data['items'];
    if (isset($data['data'])  && is_array($data['data']))  return $data['data'];
    return $data;
  }
}
