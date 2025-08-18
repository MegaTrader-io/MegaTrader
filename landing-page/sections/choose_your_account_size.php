<?php
$products_data = get_products_with_attributes();
$attributes = $products_data['attributes'] ?? [];

$account_sizes = [];
$account_types = [];

foreach ($attributes as $attr) {
    switch ($attr['taxonomy']) {
        case 'pa_account-size':
            $account_sizes[] = $attr['slug'];
            break;
        case 'pa_account-types':
            $account_types[] = $attr;
            break;
    }
}

$addons = get_saved_challenge_addons();
$defaultSlug = 'elite-plan';
$defaultPlatform = 'megatraderx';

$product = array_find($products_data['products'], function ($product) use ($defaultSlug) {
    return $product['slug'] == $defaultSlug;
});

$variation_fields = bmc_get_custom_variation_fields();
$mostPopular = '150k';
?>

<section id="pricing" class="px-4">
    <div class="pb-4 self-stretch text-center text-white text-[40px] font-light uppercase leading-[48px]">
        Choose your account size
    </div>

    <div class="mx-auto pb-8 max-w-[760px] text-center text-xl leading-8 font-medium text-stone-400 md:max-w-[ 860px]">
        Choose from flexible account sizes and plans tailored to your trading style—whether you're growing your skills
        or ready to trade real capital with confidence
    </div>

    <div class="space-y-2 flex-1 md:space-y-0 md:flex gap-2 mb-4">
        <?php foreach ($account_types as $index => $account_type) : ?>
            <button data-value="<?= $account_type['slug'] ?>"
                    class="btn-account-type group relative w-full rounded-2xl p-6 text-left account-type <?= $index === 0 ? 'account-active' : '' ?>">
                <div class="inline-flex justify-start items-start gap-4">
                    <div class="text-primary group-[.account-active]:text-black mt-1 group-[.account-active]:filter group-[.account-active]:brightness-[5] group-[.account-active]:invert">
                        <img src="<?= $account_type['thumbnail_url'] ?>" class="w-9 h-9" alt="Icon">
                    </div>
                    <div class="flex-1 inline-flex flex-col justify-center items-start gap-2">
                        <div class="self-stretch inline-flex justify-start items-start gap-1">
                            <div class="justify-start text-white group-[.account-active]:text-black text-xl font-bold leading-loose">
                                <?= $account_type['name'] ?>
                            </div>
                        </div>
                        <div class="self-stretch justify-start group-[.account-active]:opacity-60 group-[.account-active]:text-black text-stone-400 text-base font-bold leading-normal group-[.plan-selected]:opacity-60 group-[.plan-selected]:text-black">
                            <?= $account_type['description'] ?>
                        </div>
                    </div>
                    <div class="w-[30px] h-[30px] right-[8px] top-[8px] absolute group-[.account-active]:block">
                        <svg width="31" height="30" viewBox="0 0 31 30" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_11266_797" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="31" height="30">
                                <rect x="0.5" width="30" height="30" fill="#D9D9D9"></rect>
                            </mask>
                            <g mask="url(#mask0_11266_797)">
                                <path d="M13.75 20.75L22.5625 11.9375L20.8125 10.1875L13.75 17.25L10.1875 13.6875L8.4375 15.4375L13.75 20.75ZM15.5 27.5C13.7708 27.5 12.1458 27.1719 10.625 26.5156C9.10417 25.8594 7.78125 24.9688 6.65625 23.8438C5.53125 22.7188 4.64063 21.3958 3.98438 19.875C3.32812 18.3542 3 16.7292 3 15C3 13.2708 3.32812 11.6458 3.98438 10.125C4.64063 8.60417 5.53125 7.28125 6.65625 6.15625C7.78125 5.03125 9.10417 4.14063 10.625 3.48438C12.1458 2.82812 13.7708 2.5 15.5 2.5C17.2292 2.5 18.8542 2.82812 20.375 3.48438C21.8958 4.14063 23.2188 5.03125 24.3438 6.15625C25.4688 7.28125 26.3594 8.60417 27.0156 10.125C27.6719 11.6458 28 13.2708 28 15C28 16.7292 27.6719 18.3542 27.0156 19.875C26.3594 21.3958 25.4688 22.7188 24.3438 23.8438C23.2188 24.9688 21.8958 25.8594 20.375 26.5156C18.8542 27.1719 17.2292 27.5 15.5 27.5Z"
                                      fill="#131210"></path>
                            </g>
                        </svg>
                    </div>
                </div>
            </button>
        <?php endforeach; ?>
    </div>

    <div class="space-y-2 md:space-y-0 md:grid md:grid-cols-2 lg:flex lg:items-center mb-4">
        <?php foreach ($account_sizes as $index => $size) : ?>
            <?php
            $properties = $product[$defaultSlug][$size][$defaultSlug][$defaultPlatform];
            $id = -1;
            $price = '0.00';
            $metaInfoList = [];
            foreach ($properties as $property) {
                foreach ($property as $key => $arrayProperties) {
                    switch ($key) {
                        case 'id':
                            $id = $arrayProperties;
                            break;
                        case 'price-monthly':
                            $price = intval(str_replace('$', '', $arrayProperties));
                            break;
                        case 'meta-info':
                            $metaInfoList = $arrayProperties;
                            break;
                    }
                }
            }
            ?>
            <div class="<?= $mostPopular == $size
                ? 'most-popular bg-[#131210] pb-8 flex flex-col border-2 border-primary rounded-2xl'
                : 'bg-mgt-dark w-full border-t-2 border-b-2 border-stone-800 px-0 first:rounded-tl-2xl first:rounded-bl-2xl first:border-l-2 last:rounded-tr-2xl last:rounded-br-2xl last:border-r-2'
            ?> group  <?= $mostPopular == $size ? 'last-element' : '' ?>">
                <div class="px-4 bg-[#131210] <?= $mostPopular == $size ? 'py-4 flex flex-col space-y-2 rounded-[inherit]' : 'py-4 uppercase text-white font-medium first:rounded-tl-[inherit] rounded-tr-[inherit]' ?>">
                    <?php if ($mostPopular == $size): ?>
                        <div class="inline-flex">
                            <span class="justify-start text-black inline-flex rounded-xl text-sm font-bold uppercase leading-[normal] py-1 px-2 bg-primary">
                            Most popular
                        </span>
                        </div>
                    <?php endif; ?>
                    <div class="justify-start text-white text-2xl font-medium uppercase leading-7"><?= $size ?>
                        Account
                    </div>
                </div>
                <div class="px-4 py-3 flex border-r-2 group-[.last-element]:border-r-0 border-stone-800">
                    <div class="text-[#ffb34a] font-medium">
                        <span class="text-4xl leading-[48px] price-plan"
                              data-price="<?= $size ?>">$<?= number_format($price) ?></span>
                        <span class="text-xl frequency-plan"
                              data-price="<?= $size ?>">/ <?= $defaultSlug !== 'funded-plan' ? 'Month' : 'One-Time Fee' ?></span>
                    </div>
                </div>
                <div class="px-6 border-r-2 group-[.last-element]:border-r-0 border-stone-800 metaInfo"
                     data-price="<?= $size ?>">
                    <?php foreach ($metaInfoList as $field => $value): ?>
                        <?php
                        if (!$value) {
                            continue;
                        }

                        $label = $variation_fields[$field];
                        if ($label == 'Max Contracts') {
                            $label = 'Max<br>Contracts';
                        }

                        ?>
                        <div class="w-full pb-2 pt-[6px] border-t border-stone-800 inline-flex justify-start items-center gap-2 <?= $field ?>">
                            <div class="flex-1 justify-start text-stone-400 text-base font-medium">
                                <div class="grid grid-cols-[1fr_auto]">
                                    <div class="col-span-1 leading-6"
                                         title="<?= $field ?>"><?= $label; ?></div>
                                    <div class="col-auto no-wrap content-center text-right leading-6 metaValue"><?= $value ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="px-6 py-3 border-r-2 group-[.last-element]:border-r-0 border-stone-800">
                    <a target="_blank" class="btn-dark-link rounded-xl h-12 px-4 py-3"
                       href="https://subscriptions.megatrader.io/">
                        GET PLAN
                    </a>
                </div>
            </div>

            <?php if (($index + 1) % 2 == 0): ?>
                <div class="h-2 hidden sm:block col-span-2 lg:contents"></div>
            <?php endif ?>
        <?php endforeach ?>
    </div>
</section>