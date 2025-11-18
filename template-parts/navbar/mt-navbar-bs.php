<?php

/**
 * @var array<int, array{
 *     href: string,
 *     value: string,
 *     wrapper_attributes?: array<string, string>
 * }> $links
 */

$links = $args['links'] ?? [];

/** @var string $aria_label */
$aria_label = $args['aria_label'] ?? $links['aria_label'] ?? 'Main navigation';

/** @var string $class */
$class = $args['class'] ?? '';

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
                                 alt="MegaTrader" class="mt-navbar__logo-wordmark-bs" loading="eager"/>
                        </div>
                    </a>
                </div>

                <nav class="mt-navbar__nav" aria-label="<?= $aria_label ?>">
                    <?php foreach ($links as $index => $link): ?>
                        <a href="<?= $link['href'] ?>"
                                <?= $link['wrapper_attributes'] ? ' ' . implode(' ', array_map(function ($key, $value) {
                                            return $key . '="' . $value . '"';
                                        }, array_keys($link['wrapper_attributes']), $link['wrapper_attributes'])) : '' ?>
                           class="mt-navbar__nav-link <?= $index === 0 ? 'mt-navbar__nav-link--active' : '' ?> <?= $class ?: '' ?>">
                            <?= $link['value'] ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <div class="mt-navbar__actions">
                    <?php if ($navbar_actions_template): ?>
                        <?php get_template_part($navbar_actions_template); ?>
                    <?php endif; ?>

                    <a href="/auth/login" class="btn w-100 mega-btn-md mega-btn-primary-md w-100">
                        GET FUNDED
                    </a>

                    <div class="mt-menu-container dropdown">
                        <button class="mt-menu-container__btn-burger mega-btn-md mega-btn-secondary-md dropdown-toggle"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <mask id="mask0_17072_197" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                      y="0" width="24" height="24">
                                    <rect width="24" height="24" fill="#D9D9D9"/>
                                </mask>
                                <g mask="url(#mask0_17072_197)">
                                    <mask id="mask1_17072_197" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="-1"
                                          y="0" width="25" height="24">
                                        <rect x="-0.5" width="24" height="24" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask1_17072_197)">
                                        <path d="M2.5 18V16H20.5V18H2.5ZM2.5 13V11H20.5V13H2.5ZM2.5 8V6H20.5V8H2.5Z"
                                              fill="white"/>
                                    </g>
                                </g>
                            </svg>
                        </button>
                        <ul class="dropdown-menu mt-3">
                            <?php foreach ($links as $index => $link): ?>
                                <li class="dropdown-item">
                                    <a href="<?= $link['href'] ?>"
                                            <?= $link['wrapper_attributes'] ? ' ' . implode(' ', array_map(function ($key, $value) {
                                                        return $key . '="' . $value . '"';
                                                    }, array_keys($link['wrapper_attributes']), $link['wrapper_attributes'])) : '' ?>
                                       class="dropdown-menu__link dropdown-item text-center">
                                        <?= $link['value'] ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>