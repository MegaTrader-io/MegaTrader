<?php
/**
 * Template part: Account Navigation (agnostic)
 *
 * Expected $args structure:
 *
 * $args = [
 *   'items' => [
 *      [
 *        'label'   => 'Trade Area',
 *        'url'     => '/my-account/overview',
 *        'active'  => true,
 *        'item_id' => 'opcional-id-li', // (opcional) NUEVO
 *      ],
 *      [
 *        'label'   => 'Manage Subscription',
 *        'url'     => '/my-account/subscriptions',
 *        'active'  => false,
 *        'item_id' => 'mt-nav-manage-subscription', // (opcional) NUEVO
 *      ],
 *      [
 *        'label'   => 'Payment Method',
 *        'url'     => '/my-account/payment-methods',
 *        'active'  => false,
 *      ],
 *   ],
 *   'aria_label' => 'Account pages',
 *   'select_id'  => 'mega-navigation-select',
 * ];
 */

if ( ! defined('ABSPATH') ) exit;

if ( empty($args['items']) || ! is_array($args['items']) ) {
  return;
}

$items      = $args['items'];
$aria_label = $args['aria_label'] ?? __('Account pages');
$select_id  = $args['select_id'] ?? 'account-navigation-select';
?>

<div class="mega-navigation">
  <nav class="woocommerce-MyAccount-navigation d-none d-md-block" aria-label="<?php echo esc_attr($aria_label); ?>">
      <ul class="mega-navigation-list text-capitalize">
        <?php foreach ( $items as $item ) : ?>
          <li
            class="<?php echo !empty($item['active']) ? 'is-active' : ''; ?>"
            <?php if ( !empty($item['item_id']) ) : ?>
              id="<?php echo esc_attr($item['item_id']); ?>"
            <?php endif; ?>
          >
            <a
              href="<?php echo esc_url($item['url']); ?>"
              <?php echo !empty($item['active']) ? 'aria-current="page"' : ''; ?>
            >
              <?php echo esc_html($item['label']); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
  </nav>

  <select
    id="<?php echo esc_attr($select_id); ?>"
    class="mega-navigation-select d-block d-md-none form-select text-a8a29e"
    onchange="if (this.value) window.location.href=this.value;"
  >
    <?php foreach ( $items as $item ) : ?>
      <option
        value="<?php echo esc_url($item['url']); ?>"
        <?php selected( !empty($item['active']) ); ?>
      >
        <?php echo esc_html($item['label']); ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>
