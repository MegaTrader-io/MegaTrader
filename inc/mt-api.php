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

  public static function fetch_user_by_email(string $email): array|null
  {
    if (!$email) return [];

    $key = 'mt_user_' . md5(strtolower($email));
    $cached = get_transient($key);
    if ($cached !== false) return $cached;

    // Ejecuta el shortcode en modo consulta (json vacío)
    $sc = sprintf('[mega_user_update email="%s" output="json"]', esc_attr($email));

    $raw = do_shortcode($sc);

    $data = json_decode($raw, true);

    if (!is_array($data)) $data = null;
    if (isset($data['error'])) {
      error_log('Error fetching user from fetch_user_by_email: ' . $data['error']);
      $data = null;
    };

    if ($data) {
      set_transient($key, $data, 60); // Cache 1 minuto
    } else {
      delete_transient($key);
    }

    return $data ?: null;
  }

  public static function update_user_by_email(array $fields): array
  {
    if (empty($fields['email'])) {
      return ['error' => 'email_required', 'message' => 'email is required'];
    }

    $email = sanitize_email($fields['email']);
    unset($fields['email']);

    // Sanitizar cada campo
    $clean = [];
    foreach ($fields as $k => $v) {
      $clean[$k] = is_scalar($v) ? sanitize_text_field($v) : $v;
    }

    // Construir el payload
    $payload = array_merge(['email' => $email], $clean);

    // Llamar al API principal (usa mega_api_update_user internamente)
    $response = mega_api_update_user($payload);

    // Decodificar si viene como JSON string
    if (is_string($response)) {
      $decoded = json_decode($response, true);
      if (json_last_error() === JSON_ERROR_NONE) {
        $response = $decoded;
      }
    }

    // Normalizar errores
    if (is_wp_error($response)) {
      return [
        'error' => $response->get_error_code(),
        'message' => $response->get_error_message(),
      ];
    }

    return $response;
  }
}
