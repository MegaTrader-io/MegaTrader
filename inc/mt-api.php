<?php
defined('ABSPATH') || exit;

class MT_Api {
  /**
   * Fetch accounts via existing shortcode.
   */
  public static function fetch_accounts_by_email(string $email, int $page = 1, int $perPage = 50): array {
    if (!$email) return [];

    $key = 'mt_acc_' . md5(strtolower($email) . "_{$page}_{$perPage}");
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

  public static function fetch_user_by_email(string $email, bool $forceToGetData = false): array|null
  {
    if (!$email) return [];
    $email_lowercase = strtolower($email);
    $key = sprintf(CACHE_KEY::USER_INFO, md5($email_lowercase));
    $cached = get_transient($key);
    if (!$forceToGetData && $cached !== false) return $cached;

    // Ejecuta el shortcode en modo consulta (json vacío)
    $sc = sprintf('[mega_user_update email="%s" output="json"]', esc_attr($email_lowercase));

    $raw = do_shortcode($sc);

    $data = json_decode($raw, true);

    if (!is_array($data)) $data = null;
    if (isset($data['error'])) {
      error_log('[MT][fetch_user_by_email] : ' . $data['error'] . ' - ' . $sc);
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

  public static function fetch_accounts_bulk($email, array $accountIds): array {
    if (empty($accountIds)) {
      return [];
    }

    $key = 'mt_bulk_acc_' . md5(implode('|', $accountIds).strtolower($email));
    $cached = get_transient($key);
    if ($cached !== false) {
      return $cached;
    }

    // Construye el shortcode
    $ids_str = implode(',', array_map('esc_attr', $accountIds));
    $sc = sprintf('[mega_bulk_accounts_data ids="%s" output="json"]', $ids_str);

    // Ejecuta y decodifica
    $raw = do_shortcode($sc);
    $data = json_decode($raw, true);

    if (!is_array($data)) {
      $data = [];
    }
    if (isset($data['error'])) {
      error_log('[MT][fetch_accounts_bulk] ' . $data['error']);
      $data = [];
    }

    set_transient($key, $data, 30); // cache 30s (ajusta si quieres)
    return $data;
  }

  /**
   * Realiza múltiples solicitudes GET concurrentes usando cURL multi.
   *
   * @param array $endpoints Lista de endpoints relativos o URLs completas.
   * @param array $extraHeaders (opcional) Headers adicionales.
   * @param int $cache_ttl (opcional) Tiempo de cache en segundos (default: 30s).
   * @return array Resultados por URL.
   */
  public static function get_bulk(array $endpoints, array $extraHeaders = [], int $cache_ttl = 30): array
  {
    if (empty($endpoints)) {
      return ['error' => 'No endpoints provided'];
    }

    // Cache key única
    $cache_key = 'mt_get_bulk_' . md5(implode('|', $endpoints));
    $cached = get_transient($cache_key);
    if ($cached !== false) {
      return $cached;
    }

    // Configuración base y headers
    $opts = function_exists('mega_api_options') ? mega_api_options() : [];
    $base_url = isset($opts['base_url']) ? rtrim($opts['base_url'], '/') : '';
    $api_key  = isset($opts['api_key']) ? $opts['api_key'] : '';

    $headers = array_merge([
      'X-API-KEY: ' . $api_key,
      'Accept: application/json',
    ], $extraHeaders);

    $multiHandle = curl_multi_init();
    $curlHandles = [];
    $results = [];

    // Crear handles concurrentes
    foreach ($endpoints as $key => $endpoint) {
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

    // Ejecutar concurrentemente
    $running = null;
    do {
      $status = curl_multi_exec($multiHandle, $running);
      if ($status > 0) {
        error_log('[MT][get_bulk] MultiCurl error: ' . curl_multi_strerror($status));
      }
      curl_multi_select($multiHandle);
    } while ($running > 0);

    // Procesar respuestas
    foreach ($curlHandles as $key => $ch) {
      $response = curl_multi_getcontent($ch);
      $error = curl_error($ch);
      $info = curl_getinfo($ch);

      if ($error) {
        $results[$key] = [
          'error' => $error,
          'url'   => $info['url'] ?? null,
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

    // Cachear resultado para evitar exceso de requests
    set_transient($cache_key, $results, $cache_ttl);

    return $results;
  }
}
