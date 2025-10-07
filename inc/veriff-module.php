<?php

add_action('rest_api_init', function () {
    register_rest_route('veriff/v1', '/callback', [
        'methods' => 'POST',
        'callback' => 'mt_veriff_callback_handler',
        'permission_callback' => '__return_true', // Veriff no envía auth headers
    ]);
});

add_action('wp_ajax_mt_start_veriff_verification', 'mt_start_veriff_verification');
add_action('wp_ajax_mt_get_veriff_status', 'mt_get_veriff_status');

function mt_get_veriff_status()
{
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to verify your identity.'], 401);
    }

    check_ajax_referer('mt_veriff_nonce', 'security');

    $user = wp_get_current_user();
    $status = get_user_meta($user->ID, 'veriff_status', true);
    $updated_at = get_user_meta($user->ID, 'veriff_updated_at', true);

    wp_send_json_success([
        'status' => $status,
        'updated_at' => $updated_at,
        'verified' => ($status === 'approved'),
    ]);
}

function mt_start_veriff_verification()
{
    // ✅ Bloquear acceso directo
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'You must be logged in to verify your identity.'], 401);
    }

    // ✅ Validar nonce de seguridad
    check_ajax_referer('mt_veriff_nonce', 'security');

    $user = wp_get_current_user();
    $first_name = $user->first_name ?: 'Unknown';
    $last_name = $user->last_name ?: 'User';
    $email = $user->user_email;

    $api_url = 'https://api.veriff.me/v1/sessions';
    $api_key = VERIFF_API_KEY;

    $payload = [
        'verification' => [
            'callback' => home_url('/wp-json/veriff/v1/callback'),
            'person' => [
                'firstName' => $first_name,
                'lastName' => $last_name,
                'idNumber' => (string)$user->ID,
            ],
            'additionalData' => [
                'email' => $email,
            ],
            'document' => ['type' => 'ID_CARD'],
            'vendorData' => (string)$user->ID,
            'lang' => 'en',
        ]
    ];

    $response = wp_remote_post($api_url, [
        'headers' => [
            'X-AUTH-CLIENT' => $api_key,
            'Content-Type' => 'application/json',
        ],
        'body' => wp_json_encode($payload),
        'timeout' => 30,
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Request error: ' . $response->get_error_message()], 422);
    }

    error_log('Veriff response: ' . wp_remote_retrieve_body($response));

    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (empty($data['verification'])) {
        wp_send_json_error(['message' => 'Invalid API response.'], 422);
    }

    wp_send_json_success([
        'url' => $data['verification']['url'] ?? null,
        'token' => $data['verification']['sessionToken'] ?? null,
    ]);
}

function mt_veriff_callback_handler(WP_REST_Request $request)
{
    $body = $request->get_body();
    $headers = $request->get_headers();

    error_log('Veriff callback received: ' . $body);

    $signature = $headers['x_hmac_signature'][0] ?? null;

    if (!$signature) {
        return new WP_REST_Response(['error' => 'Missing signature'], 400);
    }

    $secret = VERIFF_WEBHOOK_SECRET;
    $expected_signature = hash_hmac('sha256', $body, $secret);

    // ✅ Comparación segura
    if (!hash_equals($expected_signature, $signature)) {
        return new WP_REST_Response(['error' => 'Invalid signature'], 403);
    }

    // ✅ Si pasa la validación, procesas normalmente
    $data = json_decode($body, true);

    if (isset($data['code']) && $data['code']) {
        $user_id = intval($data['vendorData'] ?? 0);
        $code = sanitize_text_field($data['code']);
        $id = sanitize_text_field($data['id']);
        $value = $id . '|' . $code;

        update_user_meta($user_id, 'veriff_id_code', $value);

        delete_user_meta($user_id, 'veriff_status');

        return new WP_REST_Response(['success' => true], 200);
    }

    $status = sanitize_text_field($data['verification']['status']);
    $user_id = intval($data['verification']['vendorData'] ?? 0);

    $code = sanitize_text_field($data['verification']['code']);
    $id = sanitize_text_field($data['verification']['id']);

    $value = $id . '|' . $code;

    update_user_meta($user_id, 'veriff_id_code', $value);
    update_user_meta($user_id, 'veriff_status', $status);

    return new WP_REST_Response(['success' => true], 200);
}