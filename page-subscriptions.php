<?php
/**
 * Template Name: Subscription Template
 *
 * The template for displaying front page
 * The Home template file
 *
 *
 * @package megatrader
 */
nocache_headers();
header("Cache-Control: private, must-revalidate");

get_header();

$products_data = get_products_with_attributes();
$attributes = $products_data['attributes'] ?? [];
$mt_products_raw = $products_data['products'] ?? [];

$market_types = $mt_account_sizes = $platforms = [];

foreach ($attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_market-type':
            $market_types[] = $attr;
            break;
        case 'pa_account-types':
            $mt_account_types[] = $attr;
            break;
        case 'pa_account-size':
            $account_sizes[] = $attr;
            break;
        case 'pa_platform':
            $platforms[] = $attr;
            break;
    }
}

require_once get_template_directory() . '/mt_prices_manager.php';

$instance = new MT_PRICESManager(
        products: $mt_products_raw,
        marketTypes: $market_types,
        accountTypes: $mt_account_types,
        sizes: $account_sizes,
        platforms: $platforms,
);

$marketType = $instance->marketTypes()->getFirst();

$accountTypes = $instance->accountTypesByMarketType($marketType['slug']);

$accountType = $accountTypes[0];

$marketTypeSlug = $marketType['slug'];
$accountTypeSlug = $accountTypes['slug'];

$accountSizes = $instance->accountSizesByAccountType($marketTypeSlug, $accountTypeSlug);
?>

    <div class="container">
        <div class="mt-page">
            <div class="mt-page__main">
                <div class="mt-subscriptions">
                    <?php render_step_selector(1); ?>
                    <div class="mt-checkout-first">
                        <section class="product-section">
                            <h2 class="product-section__header mb-3">
                                <span class="product-section__title">1. Market Type</span>
                            </h2>

                            <div class="product-section__list product-section__list_grid">
                                <?php foreach ($market_types as $key => $item): ?>
                                    <?php
                                    $input_id = 'market-type-' . $item['slug'];
                                    $checked = $key == 0 ? 'checked' : '';
                                    $slug = esc_attr($item['slug']);
                                    $name = esc_html($item['name']);
                                    $doesNotHaveItems = $item['count'] == 0;
                                    $description = esc_html($item['description']);
                                    $thumbnail = esc_url($item['thumbnail_url']);
                                    ?>
                                    <input type="radio"
                                           name="market-type" <?= $doesNotHaveItems ? 'disabled="disabled"' : '' ?>
                                           value="<?= $slug ?>"
                                           id="<?= $input_id ?>" <?= $checked ?> class="mt-circle-radio">
                                    <div class="radio__label__wrapper">
                                        <label class="mt-card mt-card-dark mt-card-radio" for="<?= $input_id ?>">
                                            <div class="mt-card__header">
                                                <i class="mt-card__radio"></i>
                                            </div>
                                            <div class="mt-card__title">
                                                <span class="mt-card__title__text"><?= $name ?></span>
                                                <?php if ($thumbnail): ?>
                                                    <img class="mt-card__title__image" src="<?= $thumbnail; ?>"
                                                         alt="Icon">
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section class="product-section">
                            <h2 class="product-section__header mb-3">
                                <span class="product-section__title">2. Account Type</span>
                            </h2>

                            <div class="product-section__list product-section__list_grid">
                                <?php foreach ($accountTypes as $key => $item): ?>
                                    <?php
                                    $input_id = 'account-type-' . $item['slug'];
                                    $checked = $key == 0 ? 'checked' : '';
                                    $slug = esc_attr($item['slug']);
                                    $name = esc_html($item['name']);
                                    $doesNotHaveItems = $item['count'] == 0;
                                    $description = esc_html($item['description']);
                                    $thumbnail = esc_url($item['thumbnail_url']);

                                    $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
                                    $config = isset($parsed['config']) ? $parsed['config'] : [];
                                    $badge = isset($parsed['config']['badge']) ? $parsed['config']['badge'] : [];
                                    ?>
                                    <input type="radio"
                                           name="account-type" <?= $doesNotHaveItems ? 'disabled="disabled"' : '' ?>
                                           value="<?= $slug ?>"
                                           id="<?= $input_id ?>" <?= $checked ?> class="mt-circle-radio">
                                    <div class="radio__label__wrapper">
                                        <label class="mt-card mt-card-dark mt-card-radio" for="<?= $input_id ?>">
                                            <div class="mt-card__header">
                                                <i class="mt-card__radio"></i>
                                            </div>
                                            <div class="mt-card__title">
                                                <span class="mt-card__title__text"><?= $name ?></span>
                                                <?php if ($badge):
                                                    $badge_style_class = isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                                                    $badge_text = $badge['text'] ?? '';
                                                    ?>
                                                    <div class="mt-dropdown__badge mt-card__badge mt-badge mt-badge-rounded-sm <?= $badge_style_class ?>"><?= $badge_text ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </section>
                        <section class="product-section">
                            <h2 class="product-section__header mb-3">
                                <span class="product-section__title">3. Account Size</span>
                            </h2>
                        </section>
                        <section class="product-section">
                            <h2 class="product-section__header mb-3">
                                <span class="product-section__title">4. Broker</span>
                            </h2>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
get_footer();