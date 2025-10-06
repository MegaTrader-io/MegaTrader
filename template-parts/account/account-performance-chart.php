<div class="account-performance-chart mt-card mt-card-dark <?= esc_attr($card_class) ?>">
  <div
    class="account-performance-chart__overlay"
    <?= $has_enough_points ? 'hidden' : '' ?>
    aria-hidden="<?= $has_enough_points ? 'true' : 'false' ?>"
  >
    <span class="apc-overlay__text">There is not enough data to generate the graph.</span>
  </div>

  <div class="account-performance-chart__header">
    <div class="d-flex flex-column flex-md-row align-items-center gap-3">
      <div class="d-flex align-items-center gap-2 w-100">
        <div class="mt-card__title__text fw-medium text-uppercase">
          <?= esc_html($chart_title) ?>
        </div>
        <?php if (isset($chart_title_tooltip) && !empty($chart_title_tooltip)): ?>
          <span class="mt-tooltip">
            <i class="mt-icon mt-icon-base mt-icon_info-solid" tabindex="0" aria-label="Chart information"></i>
            <span class="mt-tooltip__panel" role="tooltip">
              <?php if (isset($chart_title_tooltip['title'])): ?>
                <div class="mt-tooltip__title"><?= $chart_title_tooltip['title'] ?></div>
              <?php endif; ?>
              <?php if (isset($chart_title_tooltip['description'])): ?>
                <div class="mt-tooltip__body"><?= $chart_title_tooltip['description'] ?></div>
              <?php endif; ?>
            </span>
          </span>
        <?php endif; ?>
      </div>

      <select
        id="lastDaysSelect"
        class="form-select w-fit w-sm-100"
        name="last-days-select"
        <?= $has_enough_points ? '' : 'disabled' ?>
        aria-disabled="<?= $has_enough_points ? 'false' : 'true' ?>"
      >
        <?php foreach ($periods as $period): ?>
          <option value="<?= (int) $period['value']; ?>">
            <?= esc_html($period['text']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="account-performance-chart__header">
    <div id="account-performance-chart" style="margin-left: -20px;"></div>
  </div>
</div>
