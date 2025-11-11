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

if (!defined('ABSPATH')) exit;
if (empty($args['items']) || !is_array($args['items'])) return;

$items      = $args['items'];
$aria_label = $args['aria_label'] ?? __('Account pages');
$select_id  = $args['select_id']  ?? 'account-navigation-select';
?>

<div class="mega-navigation" data-nav="overview">
  <nav class="woocommerce-MyAccount-navigation d-none d-md-block" aria-label="<?php echo esc_attr($aria_label); ?>">
    <ul class="mega-navigation-list text-capitalize" role="tablist">
      <?php foreach ($items as $it): 
        $is_tab = !empty($it['view']); // metrics | journal
      ?>
        <li
          class="<?php echo !empty($it['active']) ? 'is-active' : ''; ?>"
          <?php if (!empty($it['item_id'])) : ?> id="<?php echo esc_attr($it['item_id']); ?>"<?php endif; ?>
          role="presentation"
        >
          <a
            href="<?php echo esc_url($it['url']); ?>"
            <?php echo !empty($it['active']) ? 'aria-current="page"' : ''; ?>
            <?php if ($is_tab): ?>
              role="tab"
              data-action="switch-view"
              data-view="<?php echo esc_attr($it['view']); ?>"
              aria-selected="<?php echo !empty($it['active']) ? 'true' : 'false'; ?>"
            <?php endif; ?>
          >
            <?php echo esc_html($it['label']); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <select
    id="<?php echo esc_attr($select_id); ?>"
    class="mega-navigation-select d-block d-md-none form-select text-a8a29e"
    data-action="switch-view-select"
  >
    <?php foreach ($items as $it): ?>
      <option
        value="<?php echo esc_url($it['url']); ?>"
        data-view="<?php echo esc_attr($it['view'] ?? ''); ?>"
        <?php selected(!empty($it['active'])); ?>
      >
        <?php echo esc_html($it['label']); ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>
