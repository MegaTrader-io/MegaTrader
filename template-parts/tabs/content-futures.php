<?php

defined('ABSPATH') || exit;

/** @var LayoutType $layoutType */
$layoutType = $args['layoutType'] ?? LayoutType::MyAccount;

if (!$layoutType instanceof LayoutType) {
    $layoutType = LayoutType::MyAccount;
}

$products_data = get_products_with_attributes();
$attributes = $products_data['attributes'] ?? [];

$page_slug = pathinfo(__FILE__, PATHINFO_FILENAME);

$remember_previous_selection = true;

$account_sizes = [];
$account_types = [];
$platforms = [];

foreach ($attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_account-size':
            $account_sizes[] = $attr;
            break;
        case 'pa_account-types':
            $account_types[] = $attr;
            break;
        case 'pa_platform':
            $platforms[] = $attr;
            break;
    }
}

function render_account_sizes($account_sizes) {
    if (empty($account_sizes)) return;

    $is_active_assigned = false;
    foreach ($account_sizes as $index => $item) {
        $slug = esc_attr($item['slug']);
        $name = esc_html($item['name']);
        $description = esc_html($item['description']);

        $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
        $config = isset($parsed['config']) ? $parsed['config'] : [];
        $badge = isset($parsed['config']['badge']) ? $parsed['config']['badge'] : [];
        $is_disabled = isset($config['status']) && $config['status'] === 'disabled';
        $disabled_class = $is_disabled ? ' disabled-within' : '';
        $radio_id = esc_attr('account-size-' . $slug);

        $checked_attr = '';
        if( ! $is_disabled && ! $is_active_assigned){
            // $checked_attr = 'checked="true"';
            $is_active_assigned = true;
        }
        ?>
        <input type="radio" name="account-size" hidden value="<?= $slug ?>" id="<?= $radio_id ?>" <?= $checked_attr ?>>
        <div class="radio__label__wrapper">
            <label class="mt-card mt-card-dark mt-card-radio <?= $slug . $disabled_class?>"
                   for="<?= $radio_id ?>"
                   data-value="<?= $slug ?>"
                   title="<?= $description ?>"
            >
                <div class="mt-card__header">
                    <i class="mt-card__radio disabled-target"></i>
                    <?php if ($badge):
                        $badge_style_class =  isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                        $badge_text =  isset($badge['text']) ? $badge['text'] : '';
                        ?>
                        <div class="mt-card__badge mt-badge <?= $badge_style_class ?>"><?= $badge_text ?></div>
                    <?php endif; ?>
                </div>
                <div class="mt-card__title disabled-target justify-content-center">
                    <span class="mt-card__title__text"><?= esc_html($name); ?></span>
                </div>
                <?php if (!empty($parsed['data'])): ?>
                    <ul class="mt-card__check-list disabled-target">
                        <?php foreach ($parsed['data'] as $text): ?>
                            <li class="mt-card__check-list__item">
                                <i class="mt-icon mt-icon_checkmark mt-icon-primary"></i>
                                <span class="mt-card_check-list__item__text"><?= esc_html($text); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </label>
        </div>
        <?php
    }
}

function render_account_types($account_types) {
    if (empty($account_types)) return;

    foreach ($account_types as $index => $item) {
        $slug = esc_attr($item['slug']);
        $name = esc_html($item['name']);
        $description = esc_attr($item['description']);
        $thumbnail = esc_url($item['thumbnail_url']);

        $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
        $config = isset($parsed['config']) ? $parsed['config'] : [];
        $badge = isset($parsed['config']['badge']) ? $parsed['config']['badge'] : [];

        $is_disabled = isset($config['status']) && $config['status'] === 'disabled';
        $disabled_class = $is_disabled ? ' disabled-within' : '';
        $radio_id = esc_attr('account-type-' . $slug);

        ?>
        <li>
            <input type="radio" name="account-type" hidden value="<?= $slug ?>" id="<?= $radio_id ?>"/>
            <div class="radio__label__wrapper">
                <label class="mt-card mt-card-dark mt-card-md mt-card-radio <?= $slug . $disabled_class?>"
                       for="<?= $radio_id ?>"
                       data-value="<?= $slug ?>"
                       title="<?= $description ?>"
                >
                    <div class="mt-card__header">
                        <i class="mt-card__radio disabled-target"></i>
                        <?php if ($badge):
                            $badge_style_class =  isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                            $badge_text =  isset($badge['text']) ? $badge['text'] : '';
                            ?>
                            <div class="mt-card__badge mt-badge <?= $badge_style_class ?>"><?= $badge_text ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mt-card__title disabled-target">
                        <?php if ($thumbnail): ?>
                            <img class="mt-card__title__icon" src="<?= $thumbnail; ?>" alt="Icon">
                        <?php endif; ?>
                        <span class="mt-card__title__text"><?= $name; ?></span>
                    </div>
                    <?php if (!empty($parsed['data'])): ?>
                        <ul class="mt-card__check-list disabled-target">
                            <?php foreach ($parsed['data'] as $text): ?>
                                <li class="mt-card__check-list__item">
                                    <i class="mt-icon mt-icon_checkmark mt-icon-primary"></i>
                                    <span class="mt-card_check-list__item__text"><?= esc_html($text); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </label>
            </div>
        </li>
        <?php
    }
}

function render_platforms($platforms) {
    if (empty($platforms)) return;

    $is_active_assigned = false;
    foreach ($platforms as $index => $item) {
        $slug = esc_attr($item['slug']);
        $name = esc_html($item['name']);
        $description = esc_html($item['description']);
        $thumbnail = esc_url($item['thumbnail_url']);

        $comingSoon = '';
        if (!empty($item['attribute_meta']) && is_array($item['attribute_meta'])) {
            foreach ($item['attribute_meta'] as $meta) {
                $normalized = strtolower(str_replace(' ', '', trim($meta)));
                if ($normalized === 'comingsoon') {
                    $comingSoon = ' coming-soon';
                    break;
                }
            }
        }

        $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);
        $config = isset($parsed['config']) ? $parsed['config'] : [];
        $badge = isset($config['badge']) ? $config['badge'] : [];
        $is_disabled = isset($config['status']) && $config['status'] === 'disabled';
        $disabled_class = $is_disabled ? ' disabled-within' : '';
        $radio_id = esc_attr('platform-' . $slug);

        $checked_attr = '';
        if( ! $is_disabled && ! $is_active_assigned){
            // $checked_attr = 'checked="true"';
            $is_active_assigned = true;
        }
        ?>
        <input type="radio" name="platform" hidden value="<?= $slug ?>" id="<?= $radio_id ?>" <?= $checked_attr ?>/>
        <div class="radio__label__wrapper">
            <label class="mt-card mt-card-dark mt-card-md mt-card-radio <?= $slug . $disabled_class?>"
                   for="<?= $radio_id ?>"
                   data-value="<?= $slug ?>"
                   title="<?= $description ?>"
            >
                <div class="mt-card__header">
                    <i class="mt-card__radio disabled-target"></i>
                    <?php if ($badge):
                        $badge_style_class =  isset($badge['style']) ? 'mt-badge-' . $badge['style'] : 'mt-badge-light';
                        $badge_text =  isset($badge['text']) ? $badge['text'] : '';
                        ?>
                        <div class="mt-card__badge mt-badge <?= $badge_style_class ?>"><?= $badge_text ?></div>
                    <?php endif; ?>
                </div>
                <div class="mt-card__title disabled-target">
                    <?php if ($thumbnail): ?>
                        <img class="mt-card__title__image" src="<?= $thumbnail; ?>" alt="Icon">
                    <?php endif; ?>
                    <span class="mt-card__title__text"><?= $name; ?></span>
                </div>
                <?php if (!empty($parsed['data'])): ?>
                    <ul class="mt-card__check-list disabled-target">
                        <?php foreach ($parsed['data'] as $text): ?>
                            <li class="mt-card__check-list__item">
                                <i class="mt-icon mt-icon_checkmark mt-icon-primary"></i>
                                <span class="mt-card__check-list__item__text"><?= esc_html($text); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if (!empty($config['links'])): ?>
                    <div class="mt-card__links disabled-target">
                        <?php foreach ($config['links'] as $link):
                            $href = isset($link['url']) ? $link['icon'] : 'javascript:void(0);';
                            ?>
                            <a class="mt-card__links__item" href="<?= esc_attr($href); ?>">
                                <div class="mt-badge mt-badge-md mt-badge-pill mt-badge-dark">
                                    <?php if(isset($link['icon'])): ?>
                                        <i class="mt-icon mt-icon_<?= esc_attr($link['icon']) ?>"></i>
                                    <?php endif; ?>
                                    <?php if(isset($link['text'])): ?>
                                        <span class="mt-card__links__text"><?= esc_html($link['text']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </label>
        </div>
        <?php
    }
}

?>

<form class="futures-form d-flex flex-column gap-32" id="futures-form">
    <input type="hidden" name="market-type" value="futures" />
    <section class="product-section" id="account-size">
        <h2 class="product-section__header mb-3">
            <i class="mt-icon mt-icon_wallet mt-icon-md mt-icon-primary" aria-hidden="true"></i>
            <span class="product-section__title"><?= Label::FUTURES['size_section_title']; ?></span>
        </h2>
        <div class="product-section__list product-section__list_grid">
            <?php render_account_sizes($account_sizes); ?>
        </div>
    </section>
    <section class="product-section" id="account-type">
        <h2 class="product-section__header mb-3">
            <i class="mt-icon mt-icon_lightning mt-icon-md mt-icon-primary" aria-hidden="true"></i>
            <span class="product-section__title"><?= Label::FUTURES['plan_section_title']; ?></span>
        </h2>

        <div id="account-type-glide">
            <div data-glide-el="track">
                <ul class="product-section__list">
                    <?php render_account_types(array_reverse($account_types)); ?>
                </ul>
            </div>
        </div>
    </section>
    <section class="product-section" id="account-platform">
        <h2 class="product-section__header mb-3">
            <i class="mt-icon mt-icon_grid mt-icon-md mt-icon-primary" aria-hidden="true"></i>
            <span class="product-section__title"><?= Label::FUTURES['platform_section_title']; ?></span>
        </h2>
        <div class="product-section__list">
            <?php render_platforms($platforms); ?>
        </div>
    </section>

    <?php if ($layoutType === LayoutType::MyAccount): ?>
        <?php
        $plan_includes_list = Label::FUTURES['plan_includes_list'];
        if (!empty($plan_includes_list) && count($plan_includes_list) > 0): ?>
            <section class="plan-includes">
                <h2 class="plan-includes__title mb-3"><?= Label::FUTURES['plan_includes_title']; ?></h2>
                <ul class="plan-includes__list row list-reboot">
                    <?php foreach ($plan_includes_list as $item) : ?>
                        <li class="plan-includes-list__item col-12 col-md-6 py-2 d-flex align-items-center gap-2">
                            <i class="mt-icon mt-icon_<?= htmlspecialchars($item['icon']); ?> mt-icon-primary flex-shrink-0"
                               aria-hidden="true"></i>
                            <span><?= htmlspecialchars($item['text']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
        <section class="futures-form__footer d-flex flex-column gap-32 pb-4 mb-n4 bg-1e1e1e">
            <hr class="m-0">
            <a class="mega-btn-md mega-btn-primary-md" id="proceed-to-checkout-btn"
               href="/checkout/"><?= Label::FUTURES['submit_btn_text'] ?></a>
        </section>
    <?php else: ?>
        <div class="plan-detail-selection plan-detail-selection--landing-page">
            <div class="plan-detail-selection__card mt-card">
                <div class="btn-plan-detail plan-detail-selection__header btn-plan-detail--active">
                    <img class="plan-detail-selection__account-icon" alt="Plan Icon">
                    <div class="plan-detail-selection__name"></div>
                    <div class="plan-detail-selection__checked-wrapper">
                        <i class="mt-icon mt-icon-white mt-icon_caret-up-solid" style="width: 30px;height: 30px;"></i>
                    </div>
                </div>
                <div class="plan-detail-selection__body" style="height: auto;">
                    <div class="plan-detail-selection__features-title">
                        Objectives and Rules
                    </div>
                    <ul class="plan-detail-selection__features-list mt-card__check-list disabled-target">
                        <li class="plan-detail-selection__features-item">
                            <img src="/wp-content/themes/megatrader-addons/assets/img/landing-page/check.svg"
                                 alt="check">
                            <div class="mega-info-row__label">---</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <script>
            const header = document.querySelector('.btn-plan-detail');
            const body = header.nextElementSibling;

            header.addEventListener('click', (e) => {
                const el = e.currentTarget;
                const isActive = el.classList.toggle('btn-plan-detail--active');

                if (isActive) {
                    body.style.height = body.scrollHeight + 'px';
                    body.addEventListener(
                        'transitionend',
                        () => (body.style.height = 'auto'),
                        {once: true}
                    );
                } else {
                    body.style.height = body.scrollHeight + 'px';
                    requestAnimationFrame(() => {
                        body.style.height = '0';
                    });
                }
            });
        </script>
    <?php endif; ?>
</form>

<script>
    const PAGE_KEY = '<?= $page_slug ?>-storage';
    window[PAGE_KEY] = {
        screenLoaded: false
    };

    const REMEMBER_PREVIOUS_SELECTION = <?= $remember_previous_selection ? 'true' : 'false' ?>;
    const CHECKOUT_URL = '<?= home_url( '/checkout/?add-to-cart=PRODUCT_ID' ) ?>';
    const GO_TO_URL = '<?= home_url( '/auth/register/?redirect_to=' ) ?>';
    const isUserLoggedIn = <?= is_user_logged_in() ? 'true' : 'false' ?>;
    const products = <?= wp_json_encode( $products_data['products'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ); ?>;

    function normalizeAttributes(data) {
        if (!data || !Array.isArray(data)) return {};

        return data.reduce((acc, item) => {
            return { ...acc, ...item };
        }, {});
    }

    function getFormValues(formEl) {
        const data = {};
        const formData = new FormData(formEl);

        for (const [key, value] of formData.entries()) {
            data[key] = value;
        }
        return data;
    }

    function updateSelectedProduct(){
        let values = getFormValues(form);

        const selectedProduct = normalizeAttributes(products.find(product => product.slug === values['account-type'])?.[values['account-type']]?.[values['account-size']]?.[values['account-type']]?.[values['platform']]?.[values['market-type']] ?? []);
        console.log('selectedProduct', selectedProduct);
        const selectedProductId = selectedProduct.id ?? '';

        const checkoutBtn = document.getElementById('proceed-to-checkout-btn');

        const event = new CustomEvent("product:selected", {
            detail: { product: Object.values(selectedProduct).length > 0 ? selectedProduct: null, values }
        });

        form.dispatchEvent(event);

        if (!checkoutBtn) {
            return;
        }

        let checkoutUrl = CHECKOUT_URL.replace('PRODUCT_ID', selectedProductId);

        const couponElement = document.querySelector('.pricing-table-panel__pricing_card .mt-pricing-card__code');
        if (couponElement) {
            checkoutUrl = checkoutUrl + '&coupon=' + couponElement.innerText;
        }

        if (!isUserLoggedIn) {
            checkoutBtn.href = GO_TO_URL + encodeURIComponent(checkoutUrl);
            return;
        }

        checkoutBtn.href = checkoutUrl;
    }

    const form = document.getElementById("futures-form");

    form.addEventListener("change", updateSelectedProduct);

    document.addEventListener("DOMContentLoaded", () => {
        setTimeout(() => {
            const saved = localStorage.getItem(PAGE_KEY);

            if (saved && REMEMBER_PREVIOUS_SELECTION) {
                const values = JSON.parse(saved);
                Object.entries(values).forEach(([key, value]) => {
                    const input = form.querySelector(`input[name="${key}"][value="${value}"]`);
                    if (input) {
                        input.checked = true;
                    }
                });

                localStorage.removeItem(PAGE_KEY)
            } else {
                let values = getFormValues(form);

                if (Object.keys(values).length < 4) {
                    let defaultInputChecked = {}
                    document.querySelectorAll('form > section input[type=radio]').forEach(input => {
                        if (defaultInputChecked[input.name]) {
                            return;
                        }

                        defaultInputChecked[input.name] = true;
                        input.checked = true;
                    });
                }
            }


            updateSelectedProduct();

            const btnCheckout = document.getElementById('proceed-to-checkout-btn');

            if (btnCheckout) {
                btnCheckout.addEventListener('click', function () {
                    let values = getFormValues(form);
                    localStorage.setItem(PAGE_KEY, JSON.stringify(values));
                });
            }
        }, 0);
    });
</script>

<script>
    const stickyFooter = document.querySelector('.futures-form__footer');

    if (stickyFooter) {
        const observer = new IntersectionObserver(
            (entries, obs) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && entry.boundingClientRect.top > 0) {
                        stickyFooter.classList.add('sticky-bottom');
                        obs.unobserve(entry.target); // stop observing after first time
                    }
                });
            },
            { threshold: 0.9 } // adjust sensitivity
        );
        observer.observe(stickyFooter);
    }
</script>



