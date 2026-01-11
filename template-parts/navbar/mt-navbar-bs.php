<?php

/**
 * @var array<int, array{
 *     href: string,
 *     value: string,
 *     icon_class?: string,
 *     wrapper_attributes?: array<string, string>
 * }> $links
 */

$links = $args['links'] ?? [];

/** @var string $aria_label */
$aria_label = $args['aria_label'] ?? $links['aria_label'] ?? 'Main navigation';

/** @var string $class */
$class = $args['class'] ?? '';

$section = $args['section'] ?? '';

/** @var string|null $classes_navbar */
$classes_navbar = $args['classes_navbar'] ?? '';

$navbar_actions_template = $args['navbar_actions_template'] ?? '';

?>

<header class="mt-navbar <?= $classes_navbar ?>">
    <div class="mt-navbar__wrapper">
        <?php do_action('mega_sticky_promo_render_banner'); ?>
        <div class="mt-navbar__links">
            <div class="mt-navbar__container">
                <div class="mt-navbar__logo">
                    <a href="<?php echo esc_url(home_url()); ?>" class="mt-navbar__logo-link">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-mobile-original.svg"
                            alt="MegaTrader" class="mt-navbar__logo-icon-bs" width="40" height="40" loading="eager">
                        <div class="mt-navbar__logo-text-bs">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-text-original-bs.svg"
                                alt="MegaTrader" class="mt-navbar__logo-wordmark-bs" loading="eager" />
                        </div>
                    </a>
                </div>

                <nav class="mt-navbar__nav" aria-label="<?= $aria_label ?>">
                    <?php foreach ($links as $index => $link): ?>
                        <a href="<?= $link['href'] ?>" <?= $link['wrapper_attributes'] ? ' ' . implode(' ', array_map(function ($key, $value) {
                              return $key . '="' . $value . '"';
                          }, array_keys($link['wrapper_attributes']), $link['wrapper_attributes'])) : '' ?>
                            class="mt-navbar__nav-link <?= !empty($link['class']) ? esc_attr($link['class']) : '' ?> <?= $class ?: '' ?> <?= !empty($link['icon_class']) ? 'mt-navbar__nav-link--has-icon' : '' ?>">

                            <?php if (!empty($link['icon_class'])): ?>
                                <i class="<?= esc_attr($link['icon_class']); ?>" aria-hidden="true"></i>
                            <?php endif; ?>
                            <span><?= esc_html($link['value']); ?></span>
                        </a>

                    <?php endforeach; ?>
                </nav>

                <div class="mt-navbar__actions">
                    <?php if ($navbar_actions_template): ?>
                        <?php get_template_part($navbar_actions_template); ?>
                    <?php endif; ?>

                    <?php if ($section == 'landing-page-bs' || $section == 'checkout'): ?>
                        <div class="mt-menu-container dropdown">

                            <button class="mt-menu-container__btn-burger mega-btn-md mega-btn-secondary-md dropdown-toggle"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <div style="width: 21px; height: 21px;">
                                    <svg width="16" height="11" viewBox="0 0 16 11" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M0 10.3889V8.65741H15.5833V10.3889H0ZM0 6.06019V4.3287H15.5833V6.06019H0ZM0 1.73148V0H15.5833V1.73148H0Z"
                                            fill="white" />
                                    </svg>
                                </div>
                            </button>

                            <ul class="dropdown-menu mt-3">
                                <?php foreach ($links as $index => $link): ?>
                                    <li class="dropdown-item">
                                        <a href="<?= esc_url($link['href']) ?>" <?= !empty($link['wrapper_attributes'])
                                              ? ' ' . implode(' ', array_map(function ($key, $value) {
                                                                  return $key . '="' . esc_attr($value) . '"';
                                                              }, array_keys($link['wrapper_attributes']), $link['wrapper_attributes']))
                                              : '' ?>
                                            class="dropdown-menu__link dropdown-item text-center <?= !empty($link['icon_class']) ? 'mt-navbar__nav-link--has-icon' : '' ?>">

                                            <?php if (!empty($link['icon_class'])): ?>
                                                <i class="<?= esc_attr($link['icon_class']); ?>" aria-hidden="true"></i>
                                            <?php endif; ?>

                                            <span><?= esc_html($link['value']); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>