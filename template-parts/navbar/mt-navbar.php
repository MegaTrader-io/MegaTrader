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

$navbar_actions = $args['navbar_actions'] ?? function () {};

?>

<header class="mt-navbar <?= $classes_navbar ?>">
    <div class="mt-navbar__wrapper">
        <?php do_action('mega_sticky_promo_render_banner'); ?>
        <div class="mt-navbar__links">
            <div class="mt-navbar__container">
                <div class="mt-navbar__logo">
                    <a href="<?php echo esc_url(home_url()); ?>" class="mt-navbar__logo-link">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-mobile-original.svg"
                             alt="MegaTrader" class="mt-navbar__logo-icon" width="60" height="60" loading="eager">
                        <div class="mt-navbar__logo-text">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/megatrader-text-original.svg"
                                 alt="MegaTrader" class="mt-navbar__logo-wordmark" height="28" loading="eager"/>
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
                    <?php if ($navbar_actions && is_callable($navbar_actions)): ?>
                        <?php $navbar_actions(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>