<?php
if (!defined('ABSPATH')) exit;

$tabs = [
  ['id'=>'overview','label'=>'Overview','active'=>true],
  ['id'=>'es',     'label'=>'E-mini S&P 500'],
  ['id'=>'nq',     'label'=>'E-mini NASDAQ 100'],
  ['id'=>'rt',     'label'=>'E-mini Russell 2000'],
  ['id'=>'ng',     'label'=>'E-mini Natural Gas'],
  ['id'=>'nkd',    'label'=>'Nikkei NKD'],
  ['id'=>'aud',    'label'=>'Australian Dollar'],
  ['id'=>'gbp',    'label'=>'British Pound'],
];
?>
<section data-fc style="display:block;max-width:100%;overflow:hidden;box-sizing:border-box">
  <div style="width:100%;max-width:100%;display:flex;align-items:center;gap:8px;box-sizing:border-box">

    <!-- Arrow Left -->
    <div role="button" tabindex="0" aria-label="Scroll left" data-fc-arrow="left" class="mt-btn mt-btn--sm mt-btn">
      <i class="mt-icon mt-icon_chevron-left"></i>
    </div>

    <!-- Viewport + scroller -->
    <div style="flex:1 1 0%;min-width:0;width:0;position:relative;overflow:hidden;display:flex;align-items:center;box-sizing:border-box">

      <!-- Gradiente izquierdo -->
      <div data-fc-grad-left
           style="position:absolute;left:0;top:0;bottom:0;width:48px;z-index:1;background:linear-gradient(270deg, rgba(19,18,16,0) 0%, var(--Surface-Body,#131210) 100%);pointer-events:none;opacity:0"></div>

      <!-- Scroller -->
      <div data-fc-tabs role="tablist" aria-label="Markets"
           style="flex:1 1 0%;min-width:0;width:0;max-width:100%;display:flex;gap:8px;align-items:center;flex-wrap:nowrap;overflow-x:auto;scrollbar-width:none;-ms-overflow-style:none;scroll-behavior:smooth;scroll-snap-type:x proximity;box-sizing:border-box">
        <?php foreach ($tabs as $t): $active = !empty($t['active']); ?>
          <div data-fc-tab
               data-id="<?php echo esc_attr($t['id']); ?>"
               data-status="<?php echo $active ? 'active' : 'normal'; ?>"
               class="<?php echo $active ? 'active' : ''; ?>"
               role="tab"
               aria-selected="<?php echo $active ? 'true' : 'false'; ?>"
               tabindex="<?php echo $active ? '0' : '-1'; ?>"
               style="flex:0 0 auto;padding:12px 16px;background:<?php echo $active ? 'var(--Primary-500,#F1A035)' : 'var(--Surface-Dark,#292524)'; ?>;border-radius:12px;outline:1px <?php echo $active ? 'var(--Primary-500,#F1A035)' : 'var(--Colors-Gray-700,#404040)'; ?> solid;outline-offset:-1px;display:flex;align-items:center;gap:8px;white-space:nowrap;cursor:pointer;scroll-snap-align:start;box-sizing:border-box">
            <div class="mt-fc-tab-label"
                 style="color:<?php echo $active ? 'var(--Surface-Body,#131210)' : 'var(--Colors-Gray-400,#A8A29E)'; ?>;font-size:16px;font-family:Roboto;font-weight:500;line-height:24px;">
              <?php echo esc_html($t['label']); ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Gradiente derecho -->
      <div data-fc-grad-right
           style="position:absolute;right:0;top:0;bottom:0;width:48px;z-index:1;background:linear-gradient(90deg, rgba(19,18,16,0) 0%, var(--Surface-Body,#131210) 100%);pointer-events:none;opacity:0"></div>
    </div>

    <!-- Arrow Right -->
    <div role="button" tabindex="0" aria-label="Scroll right" data-fc-arrow="right" class="mt-btn mt-btn--sm mt-btn"
        >
      <i class="mt-icon mt-icon_chevron-right"></i>
    </div>
  </div>

  <!-- Panel demo (luego API) -->
  <div data-fc-panel role="region" aria-live="polite"
       style="margin-top:12px;padding:16px;background:var(--Surface-Page,#1E1E1E);border-radius:16px;outline:1px var(--Colors-Gray-700,#404040) solid;min-height:96px;color:#e5e5e5;box-sizing:border-box"></div>
</section>
