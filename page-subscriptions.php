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
            <div class="mt-ubscriptions">

                <?php render_step_selector(false); ?>


                <?php render_tabs($tabs); ?>

            </div>
        </div>
    </div>
</div>

<?php
get_footer();