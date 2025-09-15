<?php
if (!defined('ABSPATH')) exit;

$tabs = [
  ['id'=>'overview','label'=>'Overview',           'active'=>true],
  ['id'=>'es',     'label'=>'E-mini S&P 500'],
  ['id'=>'nq',     'label'=>'E-mini NASDAQ 100'],
  ['id'=>'rt',     'label'=>'E-mini Russell 2000'],
  ['id'=>'ng',     'label'=>'E-mini Natural Gas'],
  ['id'=>'nkd',    'label'=>'Nikkei NKD'],
  ['id'=>'aud',    'label'=>'Australian Dollar'],
  ['id'=>'gbp',    'label'=>'British Pound'],
];
?>
<section data-fc class="container my-3">
  <div style="align-self:stretch;justify-content:flex-start;align-items:center;gap:8px;display:inline-flex">

    <!-- Arrow Left -->
    <div role="button" tabindex="0" aria-label="Scroll left"
         data-fc-arrow="left"
         style="padding:12px;background:var(--Surface-Dark,#292524);border-radius:12px;outline:1px var(--Colors-Gray-700,#404040) solid;outline-offset:-1px;display:flex;align-items:center;justify-content:center">
      <div style="width:24px;height:24px;position:relative">
        <div style="width:24px;height:24px;left:0;top:0;position:absolute;background:#D9D9D9"></div>
        <div style="width:7.4px;height:12px;left:8px;top:6px;position:absolute;background:#fff"></div>
      </div>
    </div>

    <!-- Viewport + scroller -->
    <div style="flex:1 1 0;position:relative;overflow:hidden;justify-content:flex-start;align-items:center;gap:8px;display:flex">
      <div data-fc-tabs
           style="display:flex;gap:8px;align-items:center;overflow-x:auto;scrollbar-width:none;-ms-overflow-style:none">
        <?php foreach ($tabs as $t):
          $active = !empty($t['active']); ?>
          <div data-fc-tab
               data-id="<?php echo esc_attr($t['id']); ?>"
               data-status="<?php echo $active ? 'active' : 'normal'; ?>"
               role="tab"
               aria-selected="<?php echo $active ? 'true' : 'false'; ?>"
               tabindex="<?php echo $active ? '0' : '-1'; ?>"
               style="padding:12px 16px;background:<?php echo $active ? 'var(--Primary-500,#F1A035)' : 'var(--Surface-Dark,#292524)'; ?>;border-radius:12px;outline:1px <?php echo $active ? 'var(--Primary-500,#F1A035)' : 'var(--Colors-Gray-700,#404040)'; ?> solid;outline-offset:-1px;justify-content:center;align-items:center;gap:8px;display:flex;white-space:nowrap;cursor:pointer">
            <div class="mt-fc-tab-label"
                 style="color:<?php echo $active ? 'var(--Surface-Body,#131210)' : 'var(--Colors-Gray-400,#A8A29E)'; ?>;font-size:16px;font-family:Roboto;font-weight:500;line-height:24px;">
              <?php echo esc_html($t['label']); ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Gradiente derecho -->
      <div data-fc-grad-right
           style="width:48px;height:48px;position:absolute;right:0;top:0;background:linear-gradient(90deg, rgba(19,18,16,0) 0%, var(--Surface-Body,#131210) 100%);pointer-events:none"></div>
    </div>

    <!-- Arrow Right -->
    <div role="button" tabindex="0" aria-label="Scroll right"
         data-fc-arrow="right"
         style="padding:12px;background:var(--Surface-Dark,#292524);border-radius:12px;outline:1px var(--Colors-Gray-700,#404040) solid;outline-offset:-1px;display:flex;align-items:center;justify-content:center">
      <div style="width:24px;height:24px;position:relative">
        <div style="width:24px;height:24px;left:-0.5px;top:0;position:absolute;background:#D9D9D9"></div>
        <div style="width:7.4px;height:12px;left:7.5px;top:6px;position:absolute;background:#fff"></div>
      </div>
    </div>

  </div>

  <!-- Panel (JS no toca el HTML, solo dispara evento) -->
  <div data-fc-panel class="mt-3" role="region" aria-live="polite"></div>
</section>
