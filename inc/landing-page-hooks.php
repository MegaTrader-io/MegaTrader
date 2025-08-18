<?php

if (!function_exists('megatrader_landing_page_scripts')) {
    function megatrader_landing_page_scripts(): void
    {
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

        wp_enqueue_style('megatrader-main', $css_uri . 'style-landing-page.css', [], $style_landing_version);
        wp_enqueue_style('nouislider', $css_uri . 'nouislider.min.css', [], $nouislider_css_version);
        wp_enqueue_script('tw-modal', $js_uri . 'tw-modal.js', [], $tw_modal_js_version, true);
        wp_enqueue_script('megatrader-main', $js_uri . 'landing-page.js', [], $landing_js_version, true);
        wp_enqueue_script('nouislider', $js_uri . 'nouislider.min.js', [], $nouislider_js_version, true);
        wp_enqueue_script('clipboard', $js_uri . 'clipboard.min.js', [], $clipboard_js_version, true);

        $products_data = get_products_with_attributes();
        wp_localize_script('megatrader-main', 'MG_GLOBAL', [
            'adminAjaxApi' => admin_url('admin-ajax.php'),
            'baseApi' => esc_url_raw(rest_url('megatrader/v1')),
            'nonce' => wp_create_nonce('wp_rest'),
            'subscriptionNonce' => wp_create_nonce('subscription_action'),
            'products' => $products_data['products'] ?? [],
        ]);
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
    function megatrader_get_market_data(): WP_Error|WP_REST_Response|WP_HTTP_Response
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
        $mobileClasses = 'w-screen h-dvh max-h-dvh rounded-none border-0';
        return <<<HTML
    <div role="dialog" id="{$modalId}"
         class="mgt-dialog {$mobileClasses} fixed top-1/2 left-1/2 z-[2000] inline-flex sm:h-auto md:max-h-[85vh] md:w-[calc(100vw-32px)] -translate-x-1/2 -translate-y-1/2 flex-col items-center justify-start gap-8 overflow-hidden md:rounded-2xl md:border md:border-neutral-700 bg-[#131210] px-4 py-8 pt-4 shadow-[0px_20px_20px_20px_rgba(0,0,0,0.10)] focus:outline-none sm:max-w-[800px] lg:max-h-full"
         tabindex="-1" style="pointer-events: auto;display: none;">
        <h2 class="w-full px-4">
            <div class="flex justify-between items-center w-full">
                <div class="text-white text-2xl font-medium uppercase leading-7">{$title}</div>
                <button class="btn-close-dialog select-none outline-none focus-visible:border-none" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="text-white w-6 h-6">
                        <path fill-rule="evenodd"
                              d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z"
                              clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </h2>
        <div class="w-full h-full sm:h-auto overflow-auto pr-4 pl-4 px-0 !pb-0 lg:!max-h-[847px] scrollbar scrollbar-track-mgt-dark scrollbar-thumb-rounded-full scrollbar-track-rounded-full scrollbar-w-2 scrollbar-thumb-neutral-700 scrollbar-thumb-custom">
            <div class="md:grid my-1">
                <div class="block md:hidden">
                    <div class="relative w-full">
                        <select name="titles" class="w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none">
                            <option value="0" disabled="">Table of contents</option>
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 15L7 10H17L12 15Z" fill="white"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center">
                    <div class="sm:border-r-2 sm:border-r-[#404040] mb-8 w-full h-full md:w-0 md:my-0 "></div>
                </div>
                <div class="text-white space-y-12 lg:mr-4">
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

            echo '<div class="mx-auto max-w-[1030px] space-y-8 ' . ($categoryId != $defaultCategoryId ? 'hidden' : '') . '" data-category="' . esc_attr($categoryId) . '">';

            foreach ($faqs as $index => $faq) {
                $radioCategoryGroup = 'radio-' . $categoryId;
                $questionId = 'id-' . $categoryId . '-' . ($index + 1);
                $question = $faq['question'] ?? '';
                $answer = $faq['answer'] ?? '';

                ?>
                <label for="<?= $questionId ?>"
                       class="px-4 cursor-pointer relative flex w-full items-center flex-col text-left">
                    <input type="radio" name="<?= $radioCategoryGroup ?>" id="<?= $questionId ?>"
                           class="peer/faq-question hidden" <?= $index == 0 ? 'checked' : '' ?> />
                    <div class="block peer-checked/faq-question:hidden absolute right-4 top-1">
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
                    <div class="hidden peer-checked/faq-question:block absolute right-4 top-1">
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
                    <div class="flex w-full items-center justify-between">
                        <div class="text-white text-xl font-light w-full leading-6 uppercase mr-[30px] py-2 select-none">
                            <?= esc_html($question) ?>
                        </div>
                    </div>
                    <div class="hidden peer-checked/faq-question:block text-stone-400 justify-start text-base font-medium leading-6 pt-2">
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
<div class="animate-pulse p-3 bg-mgt-dark rounded-lg border border-transparent inline-table">
    <div class="grid grid-cols-[1fr_auto] gap-4 w-[278px] h-[48px]">
        <div>
            <h3 class="h-6  bg-slate-800/70 text-white text-base font-bold text-nowrap"></h3>
            <p class="h-6  bg-slate-800/30 text-stone-400 font-normal"></p>
        </div>

        <div class="flex justify-center items-center text-nowrap">
            <p
                class="flex gap-2 text-base font-bold"
            >
                <span class="bg-slate-800/70 w-[75px] h-6">
                </span>
                <span class="bg-slate-800/70 rounded-full w-6 h-6">
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
    <div class="flex gap-3 overflow-x-auto scrollbar-hide market-wrapper">
        {$cards}
    </div>
HTML;
    }
}



