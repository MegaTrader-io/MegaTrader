<?php
/** @var array $account_types */
$account_types = $args['account_types'] ?? [];

/** @var string $mt_default_platform */
$mt_default_platform = $args['mt_default_platform'] ?? '';

/** @var string $mt_default_market_type */
$mt_default_market_type = $args['mt_default_market_type'] ?? '';
?>

<div class="mt-pricing-table-plan-options">
    <?php foreach ($account_types as $index => $item): ?>
        <?php
        $slug = esc_attr($item['slug']);
        $name = esc_html($item['name']);
        $thumbnail = esc_url($item['thumbnail_url']);
        $radio_id = esc_attr('account-type-' . $slug);
        $checked_attr = $index === 0 ? 'checked="true"' : '';
        ?>

        <input type="radio" name="account-type"
               data-default-platform="<?= $mt_default_platform ?>"
               data-default-market-type="<?= $mt_default_market_type ?>"
               value="<?= $slug ?>" id="<?= $radio_id ?>" <?= $checked_attr ?>/>
        <div class="mt-pricing-table-plan-options__item">
            <label for="<?= $radio_id ?>">
                <?php if ($thumbnail): ?>
                    <img class="mt-pricing-table-plan-options__icon" src="<?= $thumbnail; ?>" alt="Icon">
                <?php endif; ?>

                <div class="mt-pricing-table-plan-options__text">
                    <?= $name ?>
                </div>
            </label>
        </div>

    <?php endforeach; ?>
</div>


