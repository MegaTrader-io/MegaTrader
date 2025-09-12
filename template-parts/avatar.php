<?php

defined('ABSPATH') || exit;

$avatar_size = $args['avatar_size'] ?? 64;
$has_real_avatar = $args['has_real_avatar'] ?? false;
$avatar_url = $args['avatar_url'] ?? '';
$display_name = $args['display_name'] ?? '';
$billing_country = $args['billing_country'] ?? '';
$user_email = $args['user_email'] ?? '';
$hidden_email = $args['hidden_email'] ?? false;
$initials = $args['initials'] ?? '';

?>

<?php if ($has_real_avatar): ?>
    <div class="avatar-initials" style="--avatar-size: <?php echo esc_attr($avatar_size); ?>px;">
        <img src="<?php echo esc_url($avatar_url); ?>"
             alt="<?php echo esc_attr($display_name); ?>"/>
    </div>
<?php else: ?>

    <div class="avatar-initials" style="--avatar-size: <?php echo esc_attr($avatar_size); ?>px;">
        <?php echo esc_html($initials); ?>
    </div>
<?php endif; ?>
<?php if (!$hidden_email): ?>
<div class="avatar-area__user-information flex-fill d-flex flex-column">
    <div class="text-white text-16px fw-medium">
        <?php echo esc_html($display_name); ?>
    </div>

    <?php if ($billing_country): ?>
        <div class="user-country text-a8a29e text-16px fw-medium">
            <?php echo esc_html($billing_country); ?>
        </div>
    <?php endif; ?>
    <div class="text-a8a29e text-14px-line-20px fw-medium">
        <?php echo esc_html($user_email); ?>
    </div>
</div>
<?php endif; ?>
