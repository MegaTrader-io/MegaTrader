<?php
    $products_data = get_products_with_attributes();
    $attributes = $products_data['attributes'] ?? [];

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
    
    function render_account_types($account_types) {
        if (empty($account_types)) return;

        foreach ($account_types as $index => $type) {
            $slug = esc_attr($type['slug']);
            $name = esc_html($type['name']);
            $description = esc_html($type['description']);
            $thumbnail = esc_url($type['thumbnail_url']);
            $is_active = $index === 0 ? ' active' : '';
            ?>
            <div data-value="<?= $slug ?>" id="<?= $slug ?>" class="button <?= $slug . $is_active ?>">
                <img src="<?= $thumbnail ?>" alt="Icon">
                <span class="content">
                    <span class="title"><?= $name ?></span>
                    <span class="text"><?= $description ?></span>
                </span>
            </div>
            <?php
        }
    }
    
    function render_platforms($platforms) {
        if (empty($platforms)) return;

        foreach ($platforms as $index => $platform) {
            $slug = esc_attr($platform['slug']);
            $name = esc_html($platform['name']);
            $description = esc_html($platform['description']);
            $thumbnail = esc_url($platform['thumbnail_url']);

            $comingSoon = '';
            if (!empty($platform['attribute_meta']) && is_array($platform['attribute_meta'])) {
                foreach ($platform['attribute_meta'] as $meta) {
                    $normalized = strtolower(str_replace(' ', '', trim($meta)));
                    if ($normalized === 'comingsoon') {
                        $comingSoon = ' coming-soon';
                        break;
                    }
                }
            }

            $is_active = $index === 0 ? ' active' : '';

            ?>
            <div data-value="<?= $slug ?>" id="<?= $slug ?>" class="button <?= $slug . $is_active . $comingSoon?>">
                <img src="<?= $thumbnail ?>" alt="Icon" width="57" height="57">
                <span class="content">
                    <?php if ($comingSoon): ?> <span class="mbadge">Coming Soon</span> <?php endif; ?>
                    <span class="title"> <?= $name ?> </span>
                    <span class="text"><?= $description ?></span>
                </span>
            </div>
            <?php
        }
    }

    function render_account_sizes($account_sizes) {
        if (empty($account_sizes)) return;

        foreach ($account_sizes as $index => $size) {
            $slug = esc_attr($size['slug']); // e.g. 25k
            $name = esc_html($size['name']); // e.g. $25.000
            $description = esc_html($size['description']);
            $thumbnail = esc_url($size['thumbnail_url']);
            $price = ''; // this price is updated in the frontend based on selection [type][platform][size]
            $is_active = $index === 0 ? ' active' : '';

            ?>
            <div data-value="<?= $slug ?>" id="<?= $slug ?>" class="button<?= $is_active ?>">
                <img src="<?= $thumbnail ?>" alt="Icon" width="57" height="57">
                <span class="content">
                    <span class="title">
                        <span class="label"><?= $name ?></span>
                        <span class="mbadge d-none">On sale</span> 
                        <b><span class="a-price"><?= $price ?></span> / Monthly</b>
                    </span>
                    <span class="text"><?= $description ?></span>
                </span>
            </div>
            <?php
        }
    }
?>

<style>
    /* To Migrate to CSS */ 
    .mt-tabs {
    
    }
    .mt-tabs__container {
    
    }

    .mt-tabs__list {
        display: flex;
        gap: 2px;
        min-width: 100%;
    }

    .mt-tabs__item {
      color: var(--Text-Headings, white);
      font-size: 20px;
      font-family: Roboto;
      font-weight: 700;
      line-height: 32px;
      word-wrap: break-word;
      padding: 12px;

      border-radius: 16px 16px 0 0;
      border-style: solid;
      border-width: 1px;

      cursor: pointer;

      transition: none;
    }

    .mt-tabs__item[aria-selected="true"] {
      border-color: var(--Colors-Gray-700, #404040);
      border-bottom-color: transparent;
      background: var(--Surface-Page, #1E1E1E);
    }

    .mt-tabs__item[disabled],
    .mt-tabs__item[aria-selected="false"] {
      border-color: transparent;
      border-bottom-color: var(--Colors-Gray-700, #404040);
      background: transparent;
    }

    .mt-tabs__item[aria-selected="false"]:hover {
      background: var(--Surface-Page, #1E1E1E); 
    }

    .mt-tabs__item[disabled] {
      pointer-events: none;
      opacity: 0.40;
    }

    .mt-tabs__panel {
      margin-top: -1px;
      padding: 24px;
      border-radius: 16px;
      border-top-left-radius: 0;
      border: 1px solid var(--Colors-Gray-700, #404040);
      background: var(--Surface-Page, #1E1E1E);
    }

    .mt-tabs__panel.is-first {
      /* border-top-left-radius: 0; */
    }

    .mt-tabs__panel.is-hidden {
      display: none;
    }

</style>

<div class="choose-subscription">
    <div class="mt-tabs">
        <div class="mt-tabs__container">
            <div class="mt-tabs__list" role="tablist" aria-labelledby="tablist-1">
                <a id="tab-1" class="mt-tabs__item" role="tab" aria-selected="true" aria-controls="tabpanel-1" href="#">
                  <span class="focus">Futures</span>
                </a>
                <a id="tab-2" class="mt-tabs__item" role="tab" aria-selected="false" aria-controls="tabpanel-2" tabindex="-1" href="#">
                  <span class="focus">Forex</span>
                </a>
                <a id="tab-3" class="mt-tabs__item" role="tab" aria-selected="false" aria-controls="tabpanel-3" tabindex="-1" disabled  href="#">
                  <span class="focus">Crypto</span>
                </a>
            </div>
            <div id="tabpanel-1" class="mt-tabs__panel"role="tabpanel" aria-labelledby="tab-1">
                <p>
                Futures Content
                </p>
            </div>
            <div id="tabpanel-2" class="mt-tabs__panel is-hidden" role="tabpanel" aria-labelledby="tab-2">
                <p>
                Forex Content
                </p>
            </div>
            <div id="tabpanel-3" class="mt-tabs__panel is-hidden" role="tabpanel" aria-labelledby="tab-3">
                <p>
                Crypto Content
                </p>
            </div>
        </div>
    </div>
</div>

<script>
'use strict';

class MT_Tabs {
  constructor(groupNode) {
    this.tablistNode = groupNode;

    this.tabs = [];

    this.firstTab = null;
    this.lastTab = null;

    this.tabs = Array.from(this.tablistNode.querySelectorAll('[role=tab]:not([disabled])'));
    this.tabpanels = [];

    for (var i = 0; i < this.tabs.length; i += 1) {
      var tab = this.tabs[i];
      var tabpanel = document.getElementById(tab.getAttribute('aria-controls'));

      tab.tabIndex = -1;
      tab.setAttribute('aria-selected', 'false');
      this.tabpanels.push(tabpanel);

      tab.addEventListener('keydown', this.onKeydown.bind(this));
      tab.addEventListener('click', this.onClick.bind(this));

      if (!this.firstTab) {
        this.firstTab = tab;
        tabpanel.classList.add('is-first');
      }
      this.lastTab = tab;
    }

    this.setSelectedTab(this.firstTab, false);
  }

  setSelectedTab(currentTab, setFocus) {
    if(currentTab.getAttribute('disabled')){
      return;
    }
    if (typeof setFocus !== 'boolean') {
      setFocus = true;
    }
    for (var i = 0; i < this.tabs.length; i += 1) {
      var tab = this.tabs[i];
      if (currentTab === tab) {
        tab.setAttribute('aria-selected', 'true');
        tab.removeAttribute('tabindex');
        this.tabpanels[i].classList.remove('is-hidden');
        if (setFocus) {
          tab.focus();
        }
      } else {
        tab.setAttribute('aria-selected', 'false');
        tab.tabIndex = -1;
        this.tabpanels[i].classList.add('is-hidden');
      }
    }
  }

  setSelectedToPreviousTab(currentTab) {
    var index;

    if (currentTab === this.firstTab) {
      this.setSelectedTab(this.lastTab);
    } else {
      index = this.tabs.indexOf(currentTab);
      this.setSelectedTab(this.tabs[index - 1]);
    }
  }

  setSelectedToNextTab(currentTab) {
    var index;

    if (currentTab === this.lastTab) {
      this.setSelectedTab(this.firstTab);
    } else {
      index = this.tabs.indexOf(currentTab);
      this.setSelectedTab(this.tabs[index + 1]);
    }
  }

  /* EVENT HANDLERS */

  onKeydown(event) {
    var tgt = event.currentTarget,
      flag = false;

    switch (event.key) {
      case 'ArrowLeft':
        this.setSelectedToPreviousTab(tgt);
        flag = true;
        break;

      case 'ArrowRight':
        this.setSelectedToNextTab(tgt);
        flag = true;
        break;

      case 'Home':
        this.setSelectedTab(this.firstTab);
        flag = true;
        break;

      case 'End':
        this.setSelectedTab(this.lastTab);
        flag = true;
        break;

      default:
        break;
    }

    if (flag) {
      event.stopPropagation();
      event.preventDefault();
    }
  }

  onClick(event) {
    this.setSelectedTab(event.currentTarget);
  }
}

// Initialize tablist

window.addEventListener('load', function () {
    const tablists = document.querySelectorAll('.mt-tabs__list[role=tablist]');
    for (var i = 0; i < tablists.length; i++) {
        new MT_Tabs(tablists[i]);
    }
});

</script>

<div style="margin-top: 32px; width: 100%; height: 100%; flex-direction: column; justify-content: flex-start; align-items: center; display: inline-flex">
    <div style="width: 100%; max-width: 320px; padding-bottom: 32px; justify-content: space-between; align-items: center; display: inline-flex">
        <div data-showlefttrack="false" data-showrighttrack="true" data-status="active" style="flex: 1 1 0; justify-content: flex-start; align-items: center; gap: 8px; display: flex">
            <div style="width: 32px; height: 32px; position: relative; background: var(--Primary-400, #FFB34A); overflow: hidden; border-radius: 64px; outline: 2px var(--Surface-Primary, #FFB34A) solid; outline-offset: -2px">
                <div style="left: 11px; top: 4px; position: absolute; text-align: center; color: var(--Surface-Body, #131210); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">1</div>
            </div>
            <div style="text-align: center; color: var(--Text-Headings, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Set up</div>
            <div style="flex: 1 1 0; height: 2px; background: var(--Colors-Gray-700, #404040)"></div>
        </div>
        <div data-showlefttrack="true" data-showrighttrack="false" data-status="default" style="flex: 1 1 0; justify-content: flex-start; align-items: center; gap: 8px; display: flex">
            <div style="flex: 1 1 0; height: 2px; background: var(--Colors-Gray-700, #404040)"></div>
            <div style="width: 32px; height: 32px; position: relative; background: var(--Surface-Body, #131210); overflow: hidden; border-radius: 64px; outline: 2px var(--Text-Body, #A8A29E) solid; outline-offset: -2px">
                <div style="left: 11px; top: 4px; position: absolute; text-align: center; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 700; line-height: 24px; word-wrap: break-word">2</div>
            </div>
            <div style="text-align: center; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Review and Pay</div>
        </div>
    </div>
    <div style="align-self: stretch; justify-content: flex-start; align-items: center; display: inline-flex">
        <div style="flex: 1 1 0; padding-top: 12px; padding-bottom: 12px; background: var(--Surface-Page, #1E1E1E); box-shadow: 0px 2px 0px #1E1E1E; border-top-left-radius: 16px; border-top-right-radius: 16px; border-left: 1px var(--Colors-Gray-700, #404040) solid; border-top: 1px var(--Colors-Gray-700, #404040) solid; border-right: 1px var(--Colors-Gray-700, #404040) solid; flex-direction: column; justify-content: center; align-items: center; display: inline-flex">
            <div style="width: 24px; height: 24px; position: relative">
                <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                <div style="width: 19.77px; height: 19.77px; left: 2.17px; top: 2.38px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
            </div>
            <div style="align-self: stretch; text-align: center; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Futures</div>
            <div style="align-self: stretch; text-align: center; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Professional Futures trading</div>
        </div>
        <div style="flex: 1 1 0; padding-top: 12px; padding-bottom: 12px; opacity: 0.40; border-top-left-radius: 16px; border-top-right-radius: 16px; flex-direction: column; justify-content: center; align-items: center; display: inline-flex">
            <div style="width: 24px; height: 24px; position: relative">
                <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                <div style="width: 20px; height: 20px; left: 2px; top: 2px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
            </div>
            <div style="align-self: stretch; text-align: center; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Forex</div>
            <div style="align-self: stretch; text-align: center; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Raw spreads and low c...</div>
        </div>
        <div style="flex: 1 1 0; padding-top: 12px; padding-bottom: 12px; opacity: 0.40; border-top-left-radius: 16px; border-top-right-radius: 16px; flex-direction: column; justify-content: center; align-items: center; display: inline-flex">
            <div style="width: 24px; height: 24px; position: relative">
                <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                <div style="width: 11.48px; height: 15.20px; left: 6px; top: 4px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
            </div>
            <div style="align-self: stretch; text-align: center; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Crypto</div>
            <div style="align-self: stretch; text-align: center; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">50+ Crypto coins to transfer</div>
        </div>
    </div>
    <div style="align-self: stretch; padding: 24px; position: relative; background: var(--Surface-Page, #1E1E1E); border-top-right-radius: 16px; border-bottom-right-radius: 16px; border-bottom-left-radius: 16px; outline: 1px var(--Colors-Gray-700, #404040) solid; outline-offset: -1px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 32px; display: flex">
        <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
            <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                <div style="width: 30px; height: 30px; position: relative">
                    <div style="width: 30px; height: 30px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                    <div style="width: 25px; height: 20px; left: 2.50px; top: 5px; position: absolute; background: var(--Primary-400, #FFB34A)"></div>
                </div>
                <div style="flex: 1 1 0; color: white; font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Trading capital</div>
            </div>
            <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                <div style="flex: 1 1 0; padding: 16px; background: rgba(255, 179, 74, 0.20); border-radius: 16px; outline: 2px var(--Primary-400, #FFB34A) solid; outline-offset: -2px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                    <div style="width: 30px; height: 30px; position: relative">
                        <div style="width: 30px; height: 30px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                        <div style="width: 25px; height: 25px; left: 2.50px; top: 2.50px; position: absolute; background: var(--Surface-Primary, #FFB34A)"></div>
                    </div>
                    <div style="align-self: stretch; text-align: center; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">$25,000</div>
                </div>
                <div style="flex: 1 1 0; padding: 16px; background: var(--Surface-Body, #131210); border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                    <div style="width: 30px; height: 30px; position: relative">
                        <div style="width: 24px; height: 24px; left: 3px; top: 3px; position: absolute; background: var(--Colors-Gray-700, #404040); border-radius: 9999px"></div>
                    </div>
                    <div style="align-self: stretch; text-align: center; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">$50,000</div>
                </div>
                <div style="flex: 1 1 0; padding: 16px; background: var(--Surface-Body, #131210); border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                    <div style="width: 30px; height: 30px; position: relative">
                        <div style="width: 24px; height: 24px; left: 3px; top: 3px; position: absolute; background: var(--Colors-Gray-700, #404040); border-radius: 9999px"></div>
                    </div>
                    <div style="align-self: stretch; text-align: center; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">$100,000</div>
                </div>
                <div style="flex: 1 1 0; padding: 16px; background: var(--Surface-Body, #131210); border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 8px; display: inline-flex">
                    <div style="width: 30px; height: 30px; position: relative">
                        <div style="width: 24px; height: 24px; left: 3px; top: 3px; position: absolute; background: var(--Colors-Gray-700, #404040); border-radius: 9999px"></div>
                    </div>
                    <div style="align-self: stretch; text-align: center; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">$150,000</div>
                </div>
            </div>
        </div>
        <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
            <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                <div style="width: 30px; height: 30px; position: relative">
                    <div style="width: 30px; height: 30px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                    <div style="width: 20px; height: 25px; left: 5px; top: 2.50px; position: absolute; background: var(--Primary-400, #FFB34A)"></div>
                </div>
                <div style="flex: 1 1 0; color: white; font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Challenge type</div>
            </div>
            <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                <div style="flex: 1 1 0; padding: 24px; background: rgba(255, 179, 74, 0.20); border-radius: 16px; outline: 2px var(--Primary-400, #FFB34A) solid; outline-offset: -2px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                    <div style="align-self: stretch; justify-content: space-between; align-items: center; display: inline-flex">
                        <div style="width: 30px; height: 30px; position: relative">
                            <div style="width: 30px; height: 30px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                            <div style="width: 25px; height: 25px; left: 2.50px; top: 2.50px; position: absolute; background: var(--Surface-Primary, #FFB34A)"></div>
                        </div>
                        <div data-shape="Pill" data-size="sm" data-type="Primary" style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Primary-500, #F1A035); border-radius: 12px; justify-content: center; align-items: center; gap: 10px; display: flex">
                            <div style="color: var(--Surface-Body, #131210); font-size: 14px; font-family: Roboto; font-weight: 700; text-transform: uppercase; line-height: 16px; word-wrap: break-word">Popular</div>
                        </div>
                    </div>
                    <div style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                        <div style="flex: 1 1 0; flex-direction: column; justify-content: center; align-items: flex-start; gap: 8px; display: inline-flex">
                            <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 19.25px; height: 17.10px; left: 2.38px; top: 3px; position: absolute; background: var(--Primary-400, #FFB34A)"></div>
                                </div>
                                <div style="color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Elite Plan</div>
                            </div>
                            <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: flex">
                                <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                    <div style="width: 24px; height: 24px; position: relative">
                                        <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                        <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                    </div>
                                    <div style="flex: 1 1 0; color: var(--Basic-White, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Lowest Profit Target</div>
                                </div>
                                <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                    <div style="width: 24px; height: 24px; position: relative">
                                        <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                        <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                    </div>
                                    <div style="flex: 1 1 0; color: var(--Basic-White, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Up to 90% Profit</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="flex: 1 1 0; align-self: stretch; padding: 24px; background: var(--Surface-Body, #131210); border-radius: 16px; flex-direction: column; justify-content: flex-end; align-items: flex-start; gap: 16px; display: inline-flex">
                    <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 16px; display: inline-flex">
                        <div style="width: 30px; height: 30px; position: relative">
                            <div style="width: 24px; height: 24px; left: 3px; top: 3px; position: absolute; background: var(--Colors-Gray-700, #404040); border-radius: 9999px"></div>
                        </div>
                    </div>
                    <div style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                        <div style="flex: 1 1 0; flex-direction: column; justify-content: center; align-items: flex-start; gap: 8px; display: inline-flex">
                            <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 19.77px; height: 19.77px; left: 2.17px; top: 2.38px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Growth Plan</div>
                            </div>
                            <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: flex">
                                <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                    <div style="width: 24px; height: 24px; position: relative">
                                        <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                        <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                    </div>
                                    <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">1 Day to Pass</div>
                                </div>
                                <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                    <div style="width: 24px; height: 24px; position: relative">
                                        <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                        <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                    </div>
                                    <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">No Activation Fee</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="flex: 1 1 0; align-self: stretch; padding: 24px; background: var(--Surface-Body, #131210); border-radius: 16px; flex-direction: column; justify-content: flex-end; align-items: flex-end; gap: 16px; display: inline-flex">
                    <div style="align-self: stretch; justify-content: space-between; align-items: center; display: inline-flex">
                        <div style="width: 30px; height: 30px; position: relative">
                            <div style="width: 24px; height: 24px; left: 3px; top: 3px; position: absolute; background: var(--Colors-Gray-700, #404040); border-radius: 9999px"></div>
                        </div>
                        <div data-shape="Pill" data-size="sm" data-type="Secondary" style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Secondary-500, #14B8A6); border-radius: 12px; justify-content: center; align-items: center; gap: 10px; display: flex">
                            <div style="color: var(--Surface-Body, #131210); font-size: 14px; font-family: Roboto; font-weight: 700; text-transform: uppercase; line-height: 16px; word-wrap: break-word">New</div>
                        </div>
                    </div>
                    <div style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                        <div style="flex: 1 1 0; flex-direction: column; justify-content: center; align-items: flex-start; gap: 8px; display: inline-flex">
                            <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 20px; height: 20px; left: 2px; top: 1px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Funded Plan</div>
                            </div>
                            <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: flex">
                                <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                    <div style="width: 24px; height: 24px; position: relative">
                                        <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                        <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                    </div>
                                    <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Instant Funding</div>
                                </div>
                                <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                    <div style="width: 24px; height: 24px; position: relative">
                                        <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                        <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                    </div>
                                    <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">No Time Limits</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
            <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                <div style="width: 30px; height: 30px; position: relative">
                    <div style="width: 30px; height: 30px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                    <div style="width: 22.50px; height: 22.50px; left: 3.75px; top: 3.75px; position: absolute; background: var(--Primary-400, #FFB34A)"></div>
                </div>
                <div style="flex: 1 1 0; color: white; font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Platform</div>
            </div>
            <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                <div style="flex: 1 1 0; align-self: stretch; padding: 24px; background: rgba(255, 179, 74, 0.20); border-radius: 16px; outline: 2px var(--Primary-400, #FFB34A) solid; outline-offset: -2px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                    <div style="align-self: stretch; justify-content: space-between; align-items: center; display: inline-flex">
                        <div style="width: 30px; height: 30px; position: relative">
                            <div style="width: 30px; height: 30px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                            <div style="width: 25px; height: 25px; left: 2.50px; top: 2.50px; position: absolute; background: var(--Surface-Primary, #FFB34A)"></div>
                        </div>
                        <div style="width: 58px; height: 58px; position: relative">
                            <div style="width: 58px; height: 58px; left: 0px; top: 0px; position: absolute; background: #0062FF; border-radius: 9999px"></div>
                            <div style="width: 27px; height: 24px; left: 15px; top: 17px; position: absolute; background: white"></div>
                        </div>
                    </div>
                    <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                        <div style="align-self: stretch; height: 32px; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">MegaTraderX</div>
                        <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: flex">
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Basic-White, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Advanced Charting</div>
                            </div>
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Basic-White, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">One-Click Trading</div>
                            </div>
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Basic-White, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Customizable Layout</div>
                            </div>
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Basic-White, white); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Real-Time Data</div>
                            </div>
                        </div>
                        <div style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 4px; display: inline-flex; flex-wrap: wrap; align-content: flex-start">
                            <div style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Surface-Body, #131210); border-radius: 16px; justify-content: center; align-items: center; gap: 8px; display: flex">
                                <div style="width: 20px; height: 20px; position: relative">
                                    <div style="width: 20px; height: 20px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.67px; height: 16.67px; left: 1.67px; top: 1.67px; position: absolute; background: var(--Text-Body, #A8A29E)"></div>
                                </div>
                                <div style="color: var(--Text-Body, #A8A29E); font-size: 14px; font-family: Roboto; font-weight: 700; line-height: 20px; word-wrap: break-word">Web App</div>
                            </div>
                            <div style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Surface-Body, #131210); border-radius: 16px; justify-content: center; align-items: center; gap: 8px; display: flex">
                                <div style="width: 20px; height: 20px; position: relative; overflow: hidden">
                                    <div style="width: 13.52px; height: 16.67px; left: 3.22px; top: 1.67px; position: absolute; background: var(--Text-Body, #A8A29E)"></div>
                                </div>
                                <div style="color: var(--Text-Body, #A8A29E); font-size: 14px; font-family: Roboto; font-weight: 700; line-height: 20px; word-wrap: break-word">App Store</div>
                            </div>
                            <div style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Surface-Body, #131210); border-radius: 16px; justify-content: center; align-items: center; gap: 8px; display: flex">
                                <div style="width: 20px; height: 20px; position: relative">
                                    <div style="width: 10.21px; height: 7.62px; left: 2.93px; top: 1.54px; position: absolute; background: var(--Text-Body, #A8A29E)"></div>
                                    <div style="width: 7.07px; height: 5.64px; left: 11.39px; top: 7.18px; position: absolute; background: var(--Text-Body, #A8A29E)"></div>
                                    <div style="width: 8.22px; height: 15.96px; left: 1.54px; top: 2.05px; position: absolute; background: var(--Text-Body, #A8A29E)"></div>
                                    <div style="width: 10.21px; height: 7.62px; left: 2.93px; top: 10.85px; position: absolute; background: var(--Text-Body, #A8A29E)"></div>
                                </div>
                                <div style="color: var(--Text-Body, #A8A29E); font-size: 14px; font-family: Roboto; font-weight: 700; line-height: 20px; word-wrap: break-word">Google Play</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="flex: 1 1 0; align-self: stretch; padding: 24px; position: relative; background: var(--Colors-Gray-925, #131210); border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                    <div style="align-self: stretch; justify-content: space-between; align-items: center; display: inline-flex">
                        <div style="width: 30px; height: 30px; position: relative">
                            <div style="width: 24px; height: 24px; left: 3px; top: 3px; position: absolute; background: var(--Colors-Gray-700, #404040); border-radius: 9999px"></div>
                        </div>
                        <div data-size="lg" data-style="color" style="width: 58px; height: 58px; position: relative; background: #257FFF; overflow: hidden; border-radius: 64px">
                            <div style="width: 40px; height: 36px; left: 8px; top: 10.50px; position: absolute; background: white"></div>
                        </div>
                    </div>
                    <div style="align-self: stretch; opacity: 0.30; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                        <div style="align-self: stretch; height: 32px; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Tradovate</div>
                        <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: flex">
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Cloud-Based Platform</div>
                            </div>
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Commission-Free </div>
                            </div>
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">TradingView </div>
                            </div>
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Market Replay</div>
                            </div>
                        </div>
                        <div style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 4px; display: inline-flex; flex-wrap: wrap; align-content: flex-start">
                            <div style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Surface-Page, #1E1E1E); border-radius: 16px; justify-content: center; align-items: center; gap: 8px; display: flex">
                                <div style="width: 20px; height: 20px; position: relative">
                                    <div style="width: 20px; height: 20px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.67px; height: 16.67px; left: 1.67px; top: 1.67px; position: absolute; background: var(--Basic-White, white)"></div>
                                </div>
                                <div style="color: var(--Text-Headings, white); font-size: 14px; font-family: Roboto; font-weight: 700; line-height: 20px; word-wrap: break-word">Web App</div>
                            </div>
                            <div style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Surface-Page, #1E1E1E); border-radius: 16px; justify-content: center; align-items: center; gap: 8px; display: flex">
                                <div style="width: 20px; height: 20px; position: relative; overflow: hidden">
                                    <div style="width: 16.67px; height: 8.57px; left: 1.67px; top: 5.71px; position: absolute; background: var(--Basic-White, white)"></div>
                                </div>
                                <div style="color: var(--Text-Headings, white); font-size: 14px; font-family: Roboto; font-weight: 700; line-height: 20px; word-wrap: break-word">TradingView</div>
                            </div>
                            <div style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Surface-Page, #1E1E1E); border-radius: 16px; justify-content: center; align-items: center; gap: 8px; display: flex">
                                <div style="width: 20px; height: 20px; position: relative">
                                    <div style="width: 20px; height: 20px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.67px; height: 15px; left: 1.67px; top: 2.50px; position: absolute; background: var(--Basic-White, white)"></div>
                                </div>
                                <div style="color: var(--Text-Headings, white); font-size: 14px; font-family: Roboto; font-weight: 700; line-height: 20px; word-wrap: break-word">Desktop</div>
                            </div>
                        </div>
                    </div>
                    <div data-shape="Pill" data-size="sm" data-type="error" style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; left: 116px; top: -12px; position: absolute; background: var(--Error-500, #F43F5E); border-radius: 12px; justify-content: center; align-items: center; gap: 10px; display: inline-flex">
                        <div style="color: var(--Surface-Body, #131210); font-size: 14px; font-family: Roboto; font-weight: 700; text-transform: uppercase; line-height: 16px; word-wrap: break-word">unavailable</div>
                    </div>
                </div>
                <div style="flex: 1 1 0; align-self: stretch; padding: 24px; position: relative; background: var(--Colors-Gray-925, #131210); border-radius: 16px; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: inline-flex">
                    <div style="align-self: stretch; justify-content: space-between; align-items: center; display: inline-flex">
                        <div style="width: 30px; height: 30px; position: relative">
                            <div style="width: 24px; height: 24px; left: 3px; top: 3px; position: absolute; background: var(--Colors-Gray-700, #404040); border-radius: 9999px"></div>
                        </div>
                        <img data-size="lg" data-style="Color" style="width: 58px; height: 58px; position: relative; background: var(--Surface-Body, #131210); border-radius: 64px" src="https://placehold.co/58x58" />
                    </div>
                    <div style="align-self: stretch; opacity: 0.30; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
                        <div style="align-self: stretch; height: 32px; color: var(--Text-Headings, white); font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Ninjatrader</div>
                        <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: flex">
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Cloud-Based Platform</div>
                            </div>
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Commission-Free</div>
                            </div>
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">TradingView </div>
                            </div>
                            <div style="align-self: stretch; padding-top: 4px; padding-bottom: 4px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                                <div style="width: 24px; height: 24px; position: relative">
                                    <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.30px; height: 12.03px; left: 3.85px; top: 5.97px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                                </div>
                                <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Market Replay</div>
                            </div>
                        </div>
                        <div style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 4px; display: inline-flex; flex-wrap: wrap; align-content: flex-start">
                            <div style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Surface-Page, #1E1E1E); border-radius: 16px; justify-content: center; align-items: center; gap: 8px; display: flex">
                                <div style="width: 20px; height: 20px; position: relative">
                                    <div style="width: 20px; height: 20px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.67px; height: 16.67px; left: 1.67px; top: 1.67px; position: absolute; background: var(--Basic-White, white)"></div>
                                </div>
                                <div style="color: var(--Text-Headings, white); font-size: 14px; font-family: Roboto; font-weight: 700; line-height: 20px; word-wrap: break-word">Web App</div>
                            </div>
                            <div style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Surface-Page, #1E1E1E); border-radius: 16px; justify-content: center; align-items: center; gap: 8px; display: flex">
                                <div style="width: 20px; height: 20px; position: relative; overflow: hidden">
                                    <div style="width: 16.67px; height: 8.57px; left: 1.67px; top: 5.71px; position: absolute; background: var(--Basic-White, white)"></div>
                                </div>
                                <div style="color: var(--Text-Headings, white); font-size: 14px; font-family: Roboto; font-weight: 700; line-height: 20px; word-wrap: break-word">TradingView</div>
                            </div>
                            <div style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; background: var(--Surface-Page, #1E1E1E); border-radius: 16px; justify-content: center; align-items: center; gap: 8px; display: flex">
                                <div style="width: 20px; height: 20px; position: relative">
                                    <div style="width: 20px; height: 20px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                                    <div style="width: 16.67px; height: 15px; left: 1.67px; top: 2.50px; position: absolute; background: var(--Basic-White, white)"></div>
                                </div>
                                <div style="color: var(--Text-Headings, white); font-size: 14px; font-family: Roboto; font-weight: 700; line-height: 20px; word-wrap: break-word">Desktop</div>
                            </div>
                        </div>
                    </div>
                    <div data-shape="Pill" data-size="sm" data-type="light" style="padding-left: 8px; padding-right: 8px; padding-top: 4px; padding-bottom: 4px; left: 112px; top: -12px; position: absolute; background: var(--Colors-Gray-200, #E5E5E5); border-radius: 12px; justify-content: center; align-items: center; gap: 10px; display: inline-flex">
                        <div style="color: var(--Surface-Body, #131210); font-size: 14px; font-family: Roboto; font-weight: 700; text-transform: uppercase; line-height: 16px; word-wrap: break-word">Coming soon</div>
                    </div>
                </div>
            </div>
        </div>
        <div style="align-self: stretch; flex-direction: column; justify-content: flex-start; align-items: flex-start; gap: 16px; display: flex">
            <div style="align-self: stretch; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                <div style="flex: 1 1 0; color: white; font-size: 20px; font-family: Roboto; font-weight: 700; line-height: 32px; word-wrap: break-word">Included with your plan</div>
            </div>
            <div style="align-self: stretch; justify-content: flex-start; align-items: flex-start; gap: 24px; display: inline-flex">
                <div style="flex: 1 1 0; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: inline-flex">
                    <div style="align-self: stretch; padding-top: 8px; padding-bottom: 8px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                        <div style="width: 24px; height: 24px; position: relative">
                            <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                            <div style="width: 20px; height: 16px; left: 2px; top: 4px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                        </div>
                        <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Demo trading account with a starting balance of your choice (virtual money)</div>
                    </div>
                    <div style="align-self: stretch; padding-top: 8px; padding-bottom: 8px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                        <div style="width: 24px; height: 24px; position: relative">
                            <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                            <div style="width: 16px; height: 20px; left: 4px; top: 2px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                        </div>
                        <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Professional Trading Simulator utilizing real-time market data from liquidity providers</div>
                    </div>
                    <div style="align-self: stretch; padding-top: 8px; padding-bottom: 8px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                        <div style="width: 24px; height: 24px; position: relative">
                            <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                            <div style="width: 20px; height: 16px; left: 2px; top: 4px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                        </div>
                        <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Customer Support available 24/5</div>
                    </div>
                </div>
                <div style="flex: 1 1 0; flex-direction: column; justify-content: flex-start; align-items: flex-start; display: inline-flex">
                    <div style="align-self: stretch; padding-top: 8px; padding-bottom: 8px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                        <div style="width: 24px; height: 24px; position: relative">
                            <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                            <div style="width: 20px; height: 18px; left: 2px; top: 3px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                        </div>
                        <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Access to the Trading Platform of your choice</div>
                    </div>
                    <div style="align-self: stretch; padding-top: 8px; padding-bottom: 8px; justify-content: flex-start; align-items: center; gap: 8px; display: inline-flex">
                        <div style="width: 24px; height: 24px; position: relative">
                            <div style="width: 24px; height: 24px; left: 0px; top: 0px; position: absolute; background: #D9D9D9"></div>
                            <div style="width: 18px; height: 18px; left: 3px; top: 3px; position: absolute; background: var(--Icon-Primary, #FFB34A)"></div>
                        </div>
                        <div style="flex: 1 1 0; color: var(--Text-Body, #A8A29E); font-size: 16px; font-family: Roboto; font-weight: 500; line-height: 24px; word-wrap: break-word">Trading Journal and other supporting tools</div>
                    </div>
                </div>
            </div>
        </div>
        <div style="width: 736px; height: 0px; outline: 1px var(--Colors-Gray-700, #404040) solid; outline-offset: -0.50px"></div>
        <div data-icon-alignment="Default" data-size="md" data-status="Default" data-type="Primary" data-variant="Filled" style="align-self: stretch; height: 48px; padding-left: 16px; padding-right: 16px; padding-top: 12px; padding-bottom: 12px; background: var(--Surface-Primary, #FFB34A); border-radius: 12px; outline: 2px var(--Surface-Primary, #FFB34A) solid; outline-offset: -2px; justify-content: center; align-items: center; gap: 8px; display: inline-flex">
            <div style="color: var(--Text-Action-Primary-Solid, #020617); font-size: 16px; font-family: Roboto; font-weight: 500; text-transform: uppercase; line-height: 24px; word-wrap: break-word">Procede to checkout</div>
        </div>
        <div style="width: 261px; height: 0px; left: 0px; top: 1px; position: absolute; outline: 1px var(--Surface-Page, #1E1E1E) solid; outline-offset: -0.50px"></div>
    </div>
</div>