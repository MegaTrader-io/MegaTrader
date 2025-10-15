<?php

if (!function_exists('megatrader_landing_page_scripts')) {
    function megatrader_landing_page_scripts(): void
    {
        error_log('[LV] before load assets megatrader_landing_page_scripts');
        $css_path = get_template_directory() . '/assets/css/';
        $js_path = get_template_directory() . '/assets/js/';
        $css_uri = get_template_directory_uri() . '/assets/css/';
        $js_uri = get_template_directory_uri() . '/assets/js/';

        $style_landing_version = file_exists($css_path . 'style-landing-page.css') ? filemtime($css_path . 'style-landing-page.css') : null;
        $nouislider_css_version = file_exists($css_path . 'nouislider.min.css') ? filemtime($css_path . 'nouislider.min.css') : null;
        $tw_modal_js_version = file_exists($js_path . 'tw-modal.js') ? filemtime($js_path . 'tw-modal.js') : null;
        $landing_js_version = file_exists($js_path . 'landing-page.js') ? filemtime($js_path . 'landing-page.js') : null;
        $nouislider_js_version = file_exists($js_path . 'nouislider.min.js') ? filemtime($js_path . 'nouislider.min.js') : null;
        $clipboard_js_version = file_exists($js_path . 'clipboard.min.js') ? filemtime($js_path . 'clipboard.min.js') : null;

        wp_enqueue_style('bootstrap', MEGATRADER_CSS . 'bootstrap.min.css', array(), _MEGATRADER_VERSION);
        wp_enqueue_style( 'megatrader-base',		MEGATRADER_CSS .'style.css', array(), REALTIME_VERSION );
        wp_enqueue_style( 'megatrader-style',       get_stylesheet_uri(), array(), _MEGATRADER_VERSION );

        wp_enqueue_style('megatrader-main', $css_uri . 'style-landing-page.css', [], $style_landing_version);
        wp_enqueue_style('megatrader-dev', MEGATRADER_CSS . 'megatrader-dev.css', array('megatrader-style'), REALTIME_VERSION);
        wp_enqueue_style('mt-components', MEGATRADER_CSS . 'mt-components.css', array(), REALTIME_VERSION);
        wp_enqueue_style('mt-navbar', MEGATRADER_CSS . 'mt-navbar.css', array(), REALTIME_VERSION);
        wp_enqueue_style('nouislider', $css_uri . 'nouislider.min.css', [], $nouislider_css_version);

        wp_enqueue_script('nouislider', $js_uri . 'nouislider.min.js', [], $nouislider_js_version, true);
        wp_enqueue_script('clipboard', $js_uri . 'clipboard.min.js', [], $clipboard_js_version, true);
        wp_enqueue_script( 'mt-tabs',	        MEGATRADER_JS .'mt-tabs.js', array(), REALTIME_VERSION, true);
        wp_enqueue_script('tw-modal', $js_uri . 'tw-modal.js', [], $tw_modal_js_version, true);
        wp_enqueue_script('megatrader-main', $js_uri . 'landing-page.js', ['mt-tabs'], $landing_js_version, true);

        error_log('[LV] after load assets megatrader_landing_page_scripts');

        error_log('[LV] before load get_products_with_attributes');
        $products_data = get_products_with_attributes();
        error_log('[LV] after load get_products_with_attributes');

        error_log('[LV] before load products_with_best_coupons');
        $products_with_best_coupons = [];
        error_log('[LV] after load products_with_best_coupons');

        error_log('[LV] before load megatrader-main');
        wp_localize_script('megatrader-main', 'MG_GLOBAL', [
                'adminAjaxApi' => admin_url('admin-ajax.php'),
                'baseApi' => esc_url_raw(rest_url('megatrader/v1')),
                'nonce' => wp_create_nonce('wp_rest'),
                'subscriptionNonce' => wp_create_nonce('subscription_action'),
                'products' => $products_data['products'] ?? [],
                'productsWithBestCoupons' => $products_with_best_coupons ?? [],
                'productMetaLabel' => Label::PRODUCT_META,
                'bestProducts' => mt_most_popular_products()
        ]);
        error_log('[LV] after load megatrader-main');
    }
}

if (!function_exists('mgt_footer_links')) {
    /**
     * @return array<int, array{id: string, title: string, template: string}>
     */
    function mgt_footer_links(): array
    {
        return [
                ['id' => 'disclaimer-modal-id', 'title' => 'Disclaimer', 'template' => 'partials/modal-body/disclaimer.php'],
                ['id' => 'privacy_policy-modal-id', 'title' => 'Privacy Policy', 'template' => 'partials/modal-body/privacy_policy.php'],
                ['id' => 'terms_of_service-modal-id', 'title' => 'Terms of Service', 'template' => 'partials/modal-body/terms_of_service.php'],
                ['id' => 'cookies-modal-id', 'title' => 'Cookies Settings', 'template' => 'partials/modal-body/cookies.php'],
        ];
    }
}

if (!function_exists('megatrader_get_market_data')) {
    function megatrader_get_market_data()
    {
        $cache_key = 'megatrader_market_data';
        $cached_data = get_transient($cache_key);

        if ($cached_data !== false) {
            return rest_ensure_response($cached_data);
        }

        $RAPIDAPI_SECRET_KEY = 'ef899177a8msh895555ec7ed874bp1214dcjsn8571cae50121';
        $api_url = 'https://yahoo-finance15.p.rapidapi.com/api/v1/markets/stock/quotes?ticker=ES=F,MES=F,NQ=F,MNQ=F,RTY=F,M2K=F,NKD=F,YM=F,MYM=F,6A=F,M6A=F,6B=F,M6B=F,6C=F,6E=F,M6E=F,6J=F,6M=F,6N=F,6S=F,E7=F,CL=F,QM=F,MCL=F,NG=F,QG=F,MNG=F,HO=F,RB=F,GC=F,MGC=F,SI=F,SIL=F,HG=F,MHG=F,PL=F,ZC=F,ZW=F,ZS=F,ZL=F,ZM=F,HE=F,LE=F,ZT=F,ZF=F,ZN=F,TN=F,ZB=F,UB=F';

        $args = [
                'headers' => [
                        'X-Rapidapi-Key' => $RAPIDAPI_SECRET_KEY,
                        'X-Rapidapi-Host' => 'yahoo-finance15.p.rapidapi.com',
                ],
                'timeout' => 15,
        ];

        $response = wp_remote_get($api_url, $args);

        if (is_wp_error($response)) {
            return new WP_Error('api_error', 'Unable to connect with rapid API.', [
                    'status' => 500,
                    'details' => $response->get_error_message()
            ]);
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            return new WP_Error('invalid_response', 'Invalid response from rapid API.', [
                    'status' => $code,
                    'body' => wp_remote_retrieve_body($response),
            ]);
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        $SYMBOL_DATA = [
                "ES=F" => "E-mini S&P 500", "MES=F" => "Micro E-mini S&P 500", "NQ=F" => "E-mini NASDAQ 100", "MNQ=F" => "Micro E-mini NASDAQ 100", "RTY=F" => "E-mini Russell 2000", "M2K=F" => "Micro E-mini Russell 2000", "NKD=F" => "Nikkei USD", "YM=F" => "Mini-DOW", "MYM=F" => "Micro Mini-DOW", "6A=F" => "Australian Dollar", "M6A=F" => "Micro AUD/USD", "6B=F" => "British Pound", "M6B=F" => "Micro GBP/USD",
                "6C=F" => "Canadian Dollar", "6E=F" => "Euro FX", "M6E=F" => "Micro EUR/USD", "6J=F" => "Japanese Yen", "6M=F" => "Mexican Peso", "6N=F" => "New Zealand Dollar", "6S=F" => "Swiss Franc",
                "E7=F" => "E-mini Euro FX", "CL=F" => "Crude Oil", "QM=F" => "E-mini Crude Oil", "MCL=F" => "Micro Crude Oil", "NG=F" => "Natural Gas", "QG=F" => "E-mini Natural Gas", "MNG=F" => "Micro Henry Hub Natural Gas", "HO=F" => "Heating Oil", "RB=F" => "RBOB Gasoline", "GC=F" => "Gold", "MGC=F" => "Micro Gold", "SI=F" => "Silver", "SIL=F" => "Micro Silver",
                "HG=F" => "Copper", "MHG=F" => "Micro Copper", "PL=F" => "Platinum", "ZC=F" => "Corn", "ZW=F" => "Wheat", "ZS=F" => "Soybeans", "ZL=F" => "Soybean Oil", "ZM=F" => "Soybean Meal", "HE=F" => "Lean Hogs", "LE=F" => "Live Cattle", "ZT=F" => "2-Year Note",
                "ZF=F" => "5-Year Note", "ZN=F" => "10-Year Note", "TN=F" => "10-Year Ultra-Note", "ZB=F" => "30-Year Bond", "UB=F" => "Ultra-Bond",
        ];

        $results = [];


        foreach ($body['body'] ?? [] as $item) {
            $symbol = $item['symbol'] ?? '';
            [$clean_symbol] = explode('=', $symbol);

            $results[] = [
                    'name' => ($SYMBOL_DATA[$symbol] ?? 'Unknown') . " ($clean_symbol)",
                    'price' => $item['regularMarketPrice'] ?? null,
                    'change' => $item['regularMarketChange'] ?? null,
            ];
        }

        set_transient($cache_key, $results, 5 * MINUTE_IN_SECONDS);

        return rest_ensure_response($results);
    }
}

add_action('rest_api_init', function () {
    register_rest_route('megatrader/v1', '/markets', [
            'methods' => 'GET',
            'callback' => 'megatrader_get_market_data',
    ]);
});

add_action('wp_ajax_subscription_form_submit', 'handle_subscription_form_submit');
add_action('wp_ajax_nopriv_subscription_form_submit', 'handle_subscription_form_submit');

function get_client_ip_address(): string
{
    $keys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
    ];

    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ipList = explode(',', $_SERVER[$key]);
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
    }

//    return '0.0.0.0';
    return '190.219.213.44';
}

function get_ip_geolocation_data(string $ip): ?array
{
    $api_key = '5be85e728309415cb70f0976e9b0d363';
    $url = sprintf(
            'https://ipgeolocation.abstractapi.com/v1/?api_key=%s&ip_address=%s',
            $api_key,
            urlencode($ip)
    );

    $response = wp_remote_get($url, [
            'timeout' => 5,
            'headers' => [
                    'Accept' => 'application/json'
            ]
    ]);

    if (is_wp_error($response)) {
        error_log('Geo API error: ' . $response->get_error_message());
        return null;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    if ($status_code === 200) {
        $data = json_decode($body, true);

        if (!is_array($data)) {
            return null;
        }

        return [
                'ip' => $ip,
                'city' => $data['city'] ?? '',
                'region' => $data['region'] ?? '',
                'zip' => $data['postal_code'] ?? '',
                'country' => $data['country'] ?? '',
                'longitude' => $data['longitude'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'timezone' => $data['timezone']['name'] ?? '',
        ];
    }

    if ($status_code === 400) {
        $err_data = json_decode($body, true);
        if (
                isset($err_data['error']['code']) &&
                $err_data['error']['code'] === 'validation_error' &&
                $err_data['error']['details']['ip_address'][0] === 'Invalid IP Address.'
        ) {
            return null;
        }
    }

    error_log('Geo API unexpected status ' . $status_code . ': ' . $body);
    return null;
}

function validate_email_address_with_api(string $email): array
{
    $api_key = '6251ad73244b4a23926998a839393c28';
    $url = sprintf(
            'https://emailvalidation.abstractapi.com/v1/?api_key=%s&email=%s',
            $api_key,
            urlencode($email)
    );

    $response = wp_remote_get($url, [
            'timeout' => 5,
            'headers' => [
                    'Accept' => 'application/json'
            ]
    ]);

    if (is_wp_error($response)) {
        return [
                'success' => false,
                'detail' => 'Request failed: ' . $response->get_error_message()
        ];
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    if ($status_code !== 200) {
        return [
                'success' => false,
                'detail' => 'API Error: ' . $body
        ];
    }

    $data = json_decode($body, true);
    if (!is_array($data)) {
        return [
                'success' => false,
                'detail' => 'Invalid response format'
        ];
    }

    return [
            'success' => isset($data['deliverability']) && $data['deliverability'] === 'DELIVERABLE',
            'detail' => $data['deliverability'] ?? 'unknown'
    ];
}

function handler_subscription_request(): string
{
    check_ajax_referer('subscription_action', 'nonce');

    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $consent = isset($_POST['email_consent']) && $_POST['email_consent'] === 'true';

    if (empty($email)) {
        wp_send_json_error(['message' => 'Email is required'], 422);
    }

    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Invalid email'], 422);
    }

    $result = validate_email_address_with_api($email);

    if (!$result['success']) {
        wp_send_json_error(['message' => 'Invalid email'], 422);
    }

    if (!$consent) {
        wp_send_json_error(['message' => 'Consent is required'], 400);
    }

    return $email;
}

function create_klaviyo_profile(string $email, array $location = []): array
{
    $api_key = 'pk_5a92a736289822ee50fdb33a5087b2b776';
    $url = 'https://a.klaviyo.com/api/profiles?additional-fields[profile]=subscriptions';

    $payload = [
            'data' => [
                    'type' => 'profile',
                    'attributes' => array_merge([
                            'email' => $email,
                            'locale' => 'en-US',
                            'properties' => new stdClass(), // debe ser objeto vacío {}
                    ], $location ? ['location' => $location] : [])
            ]
    ];

    $response = wp_remote_post($url, [
            'timeout' => 10,
            'headers' => [
                    'Authorization' => 'Klaviyo-API-Key ' . $api_key,
                    'Accept' => 'application/vnd.api+json',
                    'Content-Type' => 'application/vnd.api+json',
                    'revision' => '2025-04-15',
            ],
            'body' => wp_json_encode($payload),
    ]);

    if (is_wp_error($response)) {
        error_log('Klaviyo API error: ' . $response->get_error_message());
        return ['profileId' => null, 'duplicated' => false];
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if ($status_code === 409) {
        $profileId = $data['errors'][0]['meta']['duplicate_profile_id'] ?? null;
        return ['profileId' => $profileId, 'duplicated' => true];
    }

    if ($status_code !== 201) {
        error_log("Klaviyo profile creation failed ({$status_code}): " . $body);
        return ['profileId' => null, 'duplicated' => false];
    }

    $profileId = $data['data']['id'] ?? null;
    return ['profileId' => $profileId, 'duplicated' => false];
}

function add_profile_to_list(string $profile_id): array
{
    $api_key = 'pk_5a92a736289822ee50fdb33a5087b2b776';
    $list_id = 'WZVM7e';
    $url = "https://a.klaviyo.com/api/lists/{$list_id}/relationships/profiles";

    $body = [
            'data' => [
                    [
                            'type' => 'profile',
                            'id' => $profile_id
                    ]
            ]
    ];

    $response = wp_remote_post($url, [
            'timeout' => 10,
            'headers' => [
                    'Authorization' => 'Klaviyo-API-Key ' . $api_key,
                    'Accept' => 'application/vnd.api+json',
                    'Content-Type' => 'application/vnd.api+json',
                    'revision' => '2025-04-15'
            ],
            'body' => wp_json_encode($body)
    ]);

    if (is_wp_error($response)) {
        return [
                'success' => false,
                'status' => 0,
                'detail' => 'Request error: ' . $response->get_error_message()
        ];
    }

    $status = wp_remote_retrieve_response_code($response);

    if ($status === 204) {
        return [
                'success' => true,
                'status' => 204
        ];
    }

    $detail = wp_remote_retrieve_body($response);

    return [
            'success' => false,
            'status' => $status,
            'detail' => $detail
    ];
}


function handle_subscription_form_submit(): void
{
    $email = handler_subscription_request();

    $ip = get_client_ip_address();
    $location = get_ip_geolocation_data($ip);

    $result = create_klaviyo_profile($email, $location);
    if ($result['duplicated']) {
        wp_send_json_error(['message' => 'Email already exists'], 422);
    }

    $profileId = $result['profileId'] ?? null;

    if (!$profileId) {
        wp_send_json_error(['message' => 'Klaviyo profile creation failed'], 422);
    }

    $result = add_profile_to_list($profileId);
    if (!$result['success']) {
        wp_send_json_error([
                'message' => 'Failed to add profile to list',
                'detail' => $result['detail'],
                'profile_id' => $profileId,
        ], $result['status']);
    }

    wp_send_json_success(['message' => 'You have been subscribed!', 'location' => $location]);
}

if (!function_exists('mgt_render_dialog')) {
    function mgt_render_dialog(string $modalId, string $title, string $template): string
    {
        $mobileClasses = 'tw-w-screen tw-h-dvh tw-max-h-dvh tw-rounded-none tw-border-0';
        return <<<HTML
    <div role="dialog" id="{$modalId}"
         class="mgt-dialog {$mobileClasses} tw-fixed tw-top-1/2 tw-left-1/2 tw-z-[2000] tw-inline-flex sm:tw-h-auto md:tw-max-h-[85vh] md:tw-w-[calc(100vw-32px)] -tw-translate-x-1/2 -tw-translate-y-1/2 tw-flex-col tw-items-center tw-justify-start tw-gap-8 overflow-hidden md:tw-rounded-2xl md:tw-border md:tw-border-neutral-700 tw-bg-[#131210] tw-px-4 tw-py-8 tw-pt-4 tw-shadow-[0px_20px_20px_20px_rgba(0,0,0,0.10)] focus:tw-outline-none sm:tw-max-w-[800px] lg:tw-max-h-full"
         tabindex="-1" style="pointer-events: auto;display: none;">
        <h2 class="tw-w-full tw-px-4 tw-mb-0">
            <div class="tw-flex tw-justify-between tw-items-center tw-w-full">
                <div class="tw-text-white tw-text-2xl tw-font-medium tw-uppercase tw-leading-7">{$title}</div>
                <button class="btn-close-dialog tw-text-[0px] tw-p-0 tw-bg-transparent tw-select-none tw-outline-none focus-visible:tw-border-none" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="tw-text-white tw-w-6 tw-h-6">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                              clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </h2>
        <div class="tw-w-full tw-h-full sm:tw-h-auto tw-overflow-auto tw-pr-4 tw-pl-4 tw-px-0 !tw-pb-0 lg:!tw-max-h-[847px] tw-scrollbar tw-scrollbar-track-[#1e1e1e] tw-scrollbar-thumb-rounded-full tw-scrollbar-track-rounded-full tw-scrollbar-w-2 tw-scrollbar-thumb-neutral-700 scrollbar-thumb-custom">
            <div class="md:tw-grid tw-my-1">
                <div class="tw-block md:tw-hidden">
                    <div class="tw-elative tw-w-full">
                        <select name="titles" class="tw-w-full tw-py-3 tw-px-4 tw-pr-10 tw-rounded-xl tw-border tw-border-neutral-700 tw-text-stone-400 tw-bg-[#1e1e1e]/70 tw-appearance-none focus:tw-outline-none">
                            <option value="0" disabled="">Table of contents</option>
                        </select>
                        <div class="tw-absolute tw-inset-y-0 tw-right-3 tw-flex tw-items-center tw-pointer-events-none">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 15L7 10H17L12 15Z" fill="white"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="tw-flex tw-justify-center">
                    <div class="sm:tw-border-r-2 sm:tw-border-r-[#404040] tw-mb-8 tw-w-full tw-h-full md:tw-w-0 md:tw-my-0"></div>
                </div>
                <div class="tw-text-white tw-space-y-12 lg:tw-mr-4">
                    {$template}
                </div>
            </div>
        </div>
    </div>
    HTML;
    }
}

if (!function_exists('get_saved_challenge_addons')) {
    function get_saved_challenge_addons(): array
    {
        $raw = get_option('wc_checkout_add_ons');
        $addons = [];

        foreach ($raw as $addon_id => $addon) {
            if (!isset($addon['options']) || !is_array($addon['options'])) {
                continue;
            }

            foreach ($addon['options'] as $option) {
                if (isset($option['metadata']) && is_string($option['metadata'])) {
                    $option['metadata'] = str_replace('\\"', '"', $option['metadata']);
                    $metadataDecoded = json_decode($option['metadata'], true);
                    $option['metadata'] = json_last_error() === JSON_ERROR_NONE ? $metadataDecoded : null;
                }

                $option_key = str_replace(' ', '_', strtolower($option['label']));

                $addons[] = [
                        'addon_id' => $addon_id,
                        'field' => $option_key,
                        'label' => $option['label'] ?? '',
                        'description' => $option['description'] ?? '',
                        'adjustment' => $option['adjustment'] ?? 0,
                        'adjustment_symbol' => $option['adjustment_type'] === 'percent' ? '%' : '$',
                        'metadata' => $option['metadata'],
                ];
            }
        }

        return $addons;
    }
}

if (!function_exists('render_faqs')) {
    function render_faqs($defaultCategoryId = '', array $faqsGroup = []): void
    {
        foreach ($faqsGroup as $faqData) {
            $categoryId = $faqData['id'] ?? '';
            $faqs = $faqData['faqs'] ?? [];

            echo '<div class="tw-mx-auto tw-max-w-[1030px] tw-space-y-8 ' . ($categoryId != $defaultCategoryId ? 'tw-hidden' : '') . '" data-category="' . esc_attr($categoryId) . '">';

            foreach ($faqs as $index => $faq) {
                $radioCategoryGroup = 'radio-' . $categoryId;
                $questionId = 'id-' . $categoryId . '-' . ($index + 1);
                $question = $faq['question'] ?? '';
                $answer = $faq['answer'] ?? '';

                ?>
                <label for="<?= $questionId ?>"
                       class="tw-px-4 tw-cursor-pointer tw-relative tw-flex tw-w-full tw-items-center tw-flex-col tw-text-left">
                    <input type="radio" name="<?= $radioCategoryGroup ?>" id="<?= $questionId ?>"
                           class="tw-peer/faq-question tw-hidden" <?= $index == 0 ? 'checked' : '' ?> />
                    <div class="tw-block peer-checked/faq-question:tw-hidden tw-absolute tw-right-4 tw-top-1">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_8223_24371" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="30" height="30">
                                <rect width="30" height="30" fill="#D9D9D9"></rect>
                            </mask>
                            <g mask="url(#mask0_8223_24371)">
                                <path d="M13.75 16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75V16.25Z"
                                      fill="white"></path>
                            </g>
                        </svg>
                    </div>
                    <div class="tw-hidden peer-checked/faq-question:tw-block tw-absolute tw-right-4 tw-top-1">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_8280_2980" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="30" height="30">
                                <rect width="30" height="30" fill="#D9D9D9"></rect>
                            </mask>
                            <g mask="url(#mask0_8280_2980)">
                                <path d="M6.25 16.25V13.75H23.75V16.25H6.25Z" fill="white"></path>
                            </g>
                        </svg>
                    </div>
                    <div class="tw-flex tw-w-full tw-items-center tw-justify-between">
                        <div class="tw-text-white tw-text-xl tw-font-light tw-w-full tw-leading-6 tw-uppercase tw-mr-[30px] tw-py-2 tw-select-none">
                            <?= esc_html($question) ?>
                        </div>
                    </div>
                    <div class="tw-hidden peer-checked/faq-question:tw-block tw-text-stone-400 tw-justify-start tw-text-base tw-font-medium tw-leading-6 tw-pt-2">
                        <?= esc_html($answer) ?>
                    </div>
                </label>
                <?php
            }

            echo '</div>';
        }
    }
}

if (!function_exists('get_faq_categories')) {
    function get_faq_categories(array $faqsGroup): array
    {
        if (empty($faqsGroup)) {
            return [];
        }

        $categories = [];

        foreach ($faqsGroup as $section) {
            if (!isset($categories[$section['id']])) {
                $categories[$section['id']] = $section['category'];
            }
        }

        return $categories;
    }
}


if (!function_exists('single_skeleton_loading')) {
    function single_skeleton_loading(): string
    {
        return <<<HTML
<div class="tw-animate-pulse tw-p-3 tw-bg-mgt-dark tw-rounded-lg tw-border tw-border-transparent tw-inline-table">
    <div class="tw-grid tw-grid-cols-[1fr_auto] tw-gap-4 tw-w-[278px] tw-h-[48px]">
        <div>
            <h3 class="tw-h-6 tw-mb-0  tw-bg-slate-800/70 tw-text-white tw-text-base tw-font-bold tw-text-nowrap"></h3>
            <p class="tw-h-6 tw-mb-0  tw-bg-slate-800/30 tw-text-stone-400 tw-font-normal"></p>
        </div>

        <div class="tw-flex tw-justify-center tw-items-center tw-text-nowrap">
            <p class="tw-flex tw-mb-0 tw-gap-2 tw-text-base tw-font-bold">
                <span class="tw-bg-slate-800/70 tw-w-[75px] tw-h-6">
                </span>
                <span class="tw-bg-slate-800/70 tw-rounded-full tw-w-6 tw-h-6">
                </span>
            </p>
        </div>
    </div>
</div>
HTML;
    }
}

if (!function_exists('skeleton_cards')) {
    function skeleton_cards(): string
    {
        $cards = '';

        for ($i = 0; $i < 10; $i++) {
            $cards .= single_skeleton_loading();
        }

        return <<<HTML
    <div class="tw-flex tw-gap-3 overflow-x-auto scrollbar-hide market-wrapper">
        {$cards}
    </div>
HTML;
    }
}

if (!function_exists('assets_landing_page')) {
    function assets_landing_page(string $resource): string
    {
        return get_template_directory_uri() . "/assets/img/landing-page/{$resource}";
    }
}





