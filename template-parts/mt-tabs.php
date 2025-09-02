<?php
/**
 * MT Tabs Component Template
 *
 * File location:
 *   /template-parts/mt-tabs.php
 *
 * ------------------------------------------------------------------------
 * HOW TO REGISTER THE HELPER FUNCTION:
 * ------------------------------------------------------------------------
 * In your theme's functions.php, register a helper like this:
 *
 *   function render_tabs($tabs, $selected_id = null) {
 *     set_query_var('tabs', $tabs);
 *     set_query_var('selected_id', $selected_id);
 *     get_template_part('template-parts/mt-tabs');
 *   }
 *
 * This keeps render_tabs() available globally.
 *
 * ------------------------------------------------------------------------
 * PARAMETERS STRUCTURE:
 * ------------------------------------------------------------------------
 * $tabs (array of associative arrays). Each tab supports:
 *   - id        (string, required)     Unique ID for the Tab
 *   - panel_id  (string, required)     Matching ID for the tabpanel the Tab controls
 *   - title     (string, required)     Tab's Title
 *   - subtitle  (string, optional)     Tab's Subtitle
 *   - icon      (string, optional)     URL to an icon image for the Tab
 *   - content   (string, optional)     HTML markup shown inside the panel the Tab Controls. Optional if Tab is disabled.
 *   - disabled  (bool, optional)       True to disable tab
 *
 *
 * $selected_id (string, optional) - ID of the tab to mark active.
 *   If not provided, the first tab in $tabs will be active.
 *
 * ------------------------------------------------------------------------
 * USAGE EXAMPLE IN A TEMPLATE:
 * ------------------------------------------------------------------------
 * $tabs = [
 *   [
 *     'id'       => 'tab-1',
 *     'panel_id' => 'tabpanel-1',
 *     'title'    => 'Tab One',
 *     'content'  => '<p>Tab One Content</p>',
 *   ],
 *   [
 *     'id'       => 'tab-2',
 *     'panel_id' => 'tabpanel-2',
 *     'title'    => 'Tab Two',
 *     'content'  => '<p>Tab Two Content</p>',
 *     'disabled' => true
 *   ],
 * ];
 *
 * render_tabs($tabs, 'tab-2');
 *
 */

$tabs        = get_query_var('tabs');
$selected_id = get_query_var('selected_id');

// Default to first tab if none selected
if ($selected_id === null && !empty($tabs)) {
    $selected_id = $tabs[0]['id'];
}

$first_key = array_key_first($tabs);
$last_key  = array_key_last($tabs);
?>

<div class="mt-tabs">
  <div class="mt-tabs__container">

    <!-- Tablist -->
    <div class="mt-tabs__list" role="tablist">
      <?php foreach ($tabs as $tab): ?>
        <a
          id="<?= esc_attr($tab['id']); ?>"
          class="mt-tabs__item"
          role="tab"
          href="#"
          aria-controls="<?= esc_attr($tab['panel_id']); ?>"
          aria-selected="<?= $tab['id'] === $selected_id ? 'true' : 'false'; ?>"
          <?= $tab['id'] === $selected_id ? '' : 'tabindex="-1"'; ?>
          <?= !empty($tab['disabled']) ? 'disabled' : ''; ?>
        >
          <?php if (!empty($tab['icon'])): ?>
            <img class="mt-tabs__icon" src="<?= esc_url($tab['icon']); ?>" alt="" />
          <?php endif; ?>

          <span class="mt-tabs__title" ><?= esc_html($tab['title']); ?></span>

          <?php if (!empty($tab['subtitle'])): ?>
            <small class="mt-tabs__subtitle" ><?= esc_html($tab['subtitle']); ?></small>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>

      <?php ob_start(); ?>
      <div class="mt-tabs__modal-content">
        <?php foreach ($tabs as $tab): ?>
          <a
            id="<?= esc_attr($tab['id']); ?>"
            class="mt-tabs__item"
            role="tab"
            href="#"
            aria-controls="<?= esc_attr($tab['panel_id']); ?>"
            aria-selected="<?= $tab['id'] === $selected_id ? 'true' : 'false'; ?>"
            <?= $tab['id'] === $selected_id ? '' : 'tabindex="-1"'; ?>
            <?= !empty($tab['disabled']) ? 'disabled' : ''; ?>
          >
            <?php if (!empty($tab['icon'])): ?>
              <img class="mt-tabs__icon" src="<?= esc_url($tab['icon']); ?>" alt="" />
            <?php endif; ?>

            <span class="mt-tabs__title" ><?= esc_html($tab['title']); ?></span>

            <?php if (!empty($tab['subtitle'])): ?>
              <small class="mt-tabs__subtitle" ><?= esc_html($tab['subtitle']); ?></small>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
      <?php
        $modal_content = ob_get_clean();
        render_modal([
          'modalId'     	=> $tabsModalId,
          'modalTitle'  	=> 'Select Type',
          'bodyContent' 	=> $modal_content,
        ]);
      ?>

    <!-- Tabpanels -->

    <?php foreach ($tabs as $key => $tab): ?>
        <?php if (!empty($tab['content'])): 
            $is_hidden_class = $tab['id'] === $selected_id ? '' : 'is-hidden';
            $position_class  = $key === $first_key ? 'is-first' : ($key === $last_key ? 'is-last' : '');
        ?>
            <div
                id="<?= esc_attr($tab['panel_id']); ?>"
                class="mt-tabs__panel <?= esc_attr(trim($is_hidden_class . ' ' . $position_class)); ?>"
                role="tabpanel"
                aria-labelledby="<?= esc_attr($tab['id']); ?>"
            >
                <?= $tab['content']; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>



  </div>
</div>

