<?php
/**
 * Template Name: Subscription Template
 * 
 * The template for displaying front page
 * The Home template file
 *
 *
 * @package megatrader
 */

get_header();

    $products_data = get_products_with_attributes();
    $attributes = $products_data['attributes'] ?? [];

    $market_type = [];

    foreach ($attributes as $attr) {
        switch ($attr['taxonomy']) {
            case 'pa_market-type':
                $market_type[] = $attr;
                break;
        }
    }

    function load_tab_content($template_path): ?string {
        
        if (!locate_template($template_path . '.php')) {
            return null;
        }

        ob_start();
        get_template_part($template_path);
        return ob_get_clean();
    }

    $tabs = array_map(function($item) {
        $parsed = parse_attribute_meta($item['attribute_meta'] ?? []);

        $is_disabled = isset($parsed['config']['status']) && $parsed['config']['status'] === 'disabled';

        return [
            'id'        => $item['slug'] . $item['id'] . '_tab',
            'panel_id'  => $item['slug'] . $item['id'] . '_panel',
            'icon'      => $item['thumbnail_url'],
            'title'     => $item['name'],
            'subtitle'  => $item['description'],
            'disabled'  => $is_disabled,
            'content'   => ! $is_disabled ? load_tab_content('template-parts/tabs/content-' . $item['slug']) : null,
        ];
    }, $market_type);

?>

<div class="container">
    <div class="two-columns my-account flex-column flex-lg-row m-auto pt-32 pb-32">
        <div class="two-columns__col">
            <?php render_sidebar(); ?>
        </div>
        <div class="two-columns__col">
            <div class="product-selection" style="margin-top: 32px; width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: flex-start; align-items: center;">

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


                <?php render_tabs($tabs); ?>

            </div>
        </div>
    </div>
</div>

<?php
get_footer();