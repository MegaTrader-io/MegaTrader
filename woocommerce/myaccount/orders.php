<?php
/**
 * Orders (redirige a view-order o view-subscription)
 * Lee opcionalmente orderId por GET/POST sin romper el flujo normal.
 *
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

defined('ABSPATH') || exit;

/** Utilidades de entrada **/
function mt_read_order_param() : ?int {
    // aceptamos varias claves por compatibilidad
    $keys = ['orderId','optionalOrderId','oid'];
    foreach ($keys as $k) {
        if (isset($_REQUEST[$k])) { // GET o POST
            $id = intval($_REQUEST[$k]);
            return $id > 0 ? $id : null;
        }
    }
    return null;
}

/** ¿la orden pertenece al usuario? */
function mt_order_belongs_to($order, $user_id) : bool {
    if (! $order instanceof WC_Order) return false;
    $cid = (int) $order->get_customer_id();
    // Para invitados, Woo usa 0. Si necesitas permitir admins, ajústalo aquí.
    return ($cid === (int) $user_id);
}

/** obtiene la última orden “válida” del usuario (excluye órdenes que sean SOLO reset-fee/activation-fee) */
function mt_get_latest_valid_order_for_user($user_id) : ?WC_Order {
    $orders = wc_get_orders([
        'customer_id' => $user_id,
        'status'      => ['wc-processing','wc-completed','wc-on-hold','wc-pending','wc-failed','wc-cancelled','wc-refunded'],
        'limit'       => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ]);
    if (empty($orders)) return null;

    // filtro: si TODAS las líneas de la orden son “reset-fee” o “activation-fee”, la saltamos
    foreach ($orders as $order) {
        $only_fee = true;
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if (!$product) continue;
            $slug = $product->get_slug();
            if (!in_array($slug, ['reset-fee','activation-fee'], true)) {
                $only_fee = false;
                break;
            }
        }
        if (!$only_fee) {
            return $order; // esta es válida
        }
    }

    // si todas eran “solo fee”, devolvemos la más reciente igualmente
    return $orders[0] ?? null;
}

/** calcula la URL destino (view-subscription o view-order) */
function mt_build_destination_url($order, $myaccount_url) : ?string {
    if (! $order instanceof WC_Order) return null;

    // Subscriptions
    $subs = [];
    if (function_exists('wcs_get_subscriptions_for_order')) {
        $subs = wcs_get_subscriptions_for_order($order, ['order_type' => 'any']);
    }
    if (!empty($subs)) {
        $sub = reset($subs);
        if (is_object($sub) && method_exists($sub, 'get_id')) {
            return wc_get_endpoint_url('view-subscription', $sub->get_id(), $myaccount_url);
        }
    }

    // Orden normal
    return wc_get_endpoint_url('view-order', $order->get_id(), $myaccount_url);
}

/** redirección segura en template: SOLO JS (no headers) + <noscript> */
function mt_echo_client_redirect_and_exit($url) {
    $url = esc_url_raw($url ?: home_url('/subscriptions/'));
    ?>
    <div class="woocommerce">
      <p class="text-white"><?php echo esc_html__('Redirecting…', 'woocommerce'); ?></p>
      <noscript>
        <meta http-equiv="refresh" content="0;url=<?php echo esc_attr($url); ?>">
        <a class="button" href="<?php echo esc_url($url); ?>">
          <?php echo esc_html__('Continue', 'woocommerce'); ?>
        </a>
      </noscript>
      <script>
        try { window.location.replace(<?php echo wp_json_encode($url); ?>); }
        catch(e){ window.location.href = <?php echo wp_json_encode($url); ?>; }
      </script>
    </div>
    <?php
    // Cortamos aquí el template para evitar que se renderice la tabla de órdenes
    return;
}

/** ================= LÓGICA PRINCIPAL ================= */

if (!is_user_logged_in()) {
    // si no está logueado, dejamos que Woo renderice su flujo normal
    // (o puedes redirigir a my-account)
    do_action('woocommerce_before_account_orders', false);
    wc_get_template('myaccount/form-login.php');
    return;
}

$user_id      = get_current_user_id();
$myaccount_url= wc_get_page_permalink('myaccount');

// 1) parámetro opcional (GET/POST)
$param_order_id = mt_read_order_param();
if ($param_order_id) {
    $order = wc_get_order($param_order_id);

    // si no existe o no es del usuario, caemos al flujo normal por usuario
    if ($order && mt_order_belongs_to($order, $user_id)) {
        $dest = mt_build_destination_url($order, $myaccount_url);
        if ($dest) {
            mt_echo_client_redirect_and_exit($dest);
            return;
        }
    }
    // si la orden no es válida, seguimos al flujo “última del usuario”.
}

// 2) sin parámetro (o parámetro inválido): buscamos última orden válida del usuario
$latest = mt_get_latest_valid_order_for_user($user_id);
if ($latest instanceof WC_Order) {
    $dest = mt_build_destination_url($latest, $myaccount_url);
    if ($dest) {
        mt_echo_client_redirect_and_exit($dest);
        return;
    }
}

// 3) fallback final: si no hay órdenes, mandamos a /subscriptions/
$subs_url = home_url('/subscriptions/');
mt_echo_client_redirect_and_exit($subs_url);
return;

/**
 * Nota:
 * - No usamos wp_safe_redirect() aquí porque este template se carga cuando
 *   ya hubo salida (cabecera enviada). El log muestra warnings de headers ya enviados,
 *   por eso hacemos redirección del lado del cliente. :contentReference[oaicite:1]{index=1}
 * - Si más adelante quieres rehacer esto vía hook `template_redirect`, quita
 *   toda esta lógica del template y haz redirección server-side antes de enviar salida.
 */
