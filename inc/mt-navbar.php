<?php

if (!defined('ABSPATH')) {
  exit;
}

final class Mt_Navbar
{
  public static function render_navbar_bs(string $section, string|null $classes_navbar = null): void
  {
    $method = str_replace('-', '_', $section) . '_nav';

    $template_action = "template-parts/navbar/actions/{$section}-actions";

    if (!method_exists(self::class, $method)) {
      echo 'No navbar found for section: ' . $section;
      return;
    }

    get_template_part('template-parts/navbar/mt-navbar-bs', null, [
      'links' => self::$method(),
      'section' => $section,
      'classes_navbar' => $classes_navbar,
      'navbar_actions_template' => $template_action
    ]);
  }

  public static function render_navbar(string $section, string|null $classes_navbar = null): void
  {
    $method = str_replace('-', '_', $section) . '_nav';

    if (!method_exists(self::class, $method)) {
      echo 'No navbar found for section: ' . $section;
      return;
    }

    get_template_part('template-parts/navbar/mt-navbar', null, [
      'links' => self::$method(),
      'classes_navbar' => $classes_navbar,
      'navbar_actions_template' => "template-parts/navbar/actions/{$section}-actions"
    ]);
  }

  private static function landing_page_nav(): array
  {
    return [
      ['href' => '#hero-section', 'value' => 'HOME', 'class' => 'navbar__nav-link--active', 'wrapper_attributes' => ['data-menu' => 'hero-section']],
      ['href' => '#how-it-works', 'value' => 'HOW IT WORKS', 'wrapper_attributes' => ['data-menu' => 'how-it-works']],
      ['href' => '#pricing', 'value' => 'PRICING', 'wrapper_attributes' => ['data-menu' => 'pricing']],
      ['href' => '#features', 'value' => 'FEATURES', 'wrapper_attributes' => ['data-menu' => 'features']],
      ['href' => '#our-team', 'value' => 'OUR TEAM', 'wrapper_attributes' => ['data-menu' => 'our-team']],
      ['href' => '#faq', 'value' => 'FAQ', 'wrapper_attributes' => ['data-menu' => 'faq']],
    ];
  }

  private static function landing_page_bs_nav(): array
  {
    return [
      ['href' => '#hero-section', 'value' => 'HOME', 'class' => 'navbar__nav-link--active', 'wrapper_attributes' => ['data-menu' => 'hero-section']],
      ['href' => '#how-it-works', 'value' => 'HOW IT WORKS', 'wrapper_attributes' => ['data-menu' => 'how-it-works']],
      ['href' => '#pricing', 'value' => 'PRICING', 'wrapper_attributes' => ['data-menu' => 'pricing']],
      ['href' => 'https://discord.com/invite/megatrader', 'value' => 'JOIN DISCORD', 'wrapper_attributes' => ['target' => '_blank']],
      ['href' => 'https://help.megatrader.io/en/', 'value' => 'HELP CENTER', 'wrapper_attributes' => ['target' => '_blank']],
    ];
  }

  private static function account_nav(): array
  {
    return [
      ['href' => esc_url(home_url('/my-account/overview/')), 'value' => 'ACCOUNT OVERVIEW', 'class' => 'navbar__nav-link--active'],
      ['href' => esc_url(home_url('/my-account/referrals/123')), 'value' => 'REFERRALS'],
      ['href' => esc_url(home_url('/my-account/payouts/')), 'value' => 'PAYOUTS'],
      ['href' => 'https://help.megatrader.io/en/', 'value' => 'HELP CENTER', 'wrapper_attributes' => ["target" => "_blank", "rel" => "noopener"]],
    ];
  }

private static function checkout_nav(): array
{
  return [
    [
      'href' => 'javascript:void(0);',
      'value' => 'BACK TO WEBSITE',
      'icon_class' => 'mt-icon mt-icon_caret-left',
      'class' => 'navbar__nav-link--active',
      'wrapper_attributes' => [
        'onclick' => "if (window.history.length > 1) { window.history.back(); } else { window.location.href = '" . esc_url(home_url('/')) . "'; } return false;",
        'aria-label' => 'Back to website'
      ],
    ],
  ];
}



}

