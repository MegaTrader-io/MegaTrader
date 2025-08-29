<?php

$buttons = [];
foreach (mgt_footer_links() as $index => $link) {
    $modalId = $link['id'];
    $title = $link['title'];
    $buttons[] = <<<HTML
<button data-dialog-id="{$modalId}" class="btn-dialog tw-text-stone-400 tw-bg-transparent tw-text-sm tw-font-medium tw-underline tw-leading-tight">
    {$title}
</button>
HTML;
}

$btnRows = array_chunk($buttons, 2);
?>
    <footer
            class="tw-w-full tw-max-w-7xl tw-flex-1 tw-h-dvh tw-mx-auto tw-px-4 tw-pb-8  tw-flex tw-items-center tw-justify-between tw-flex-col tw-space-y-8">
        <div
                class="tw-w-full tw-p-8 bg-[#131210] tw-rounded-[20px] tw-outline tw-outline-1 tw-outline-neutral-700">
            <div class="tw-w-full tw-grid tw-grid-cols-2 tw-space-y-8 lg:tw-space-y-0 lg:tw-space-x-8">
                <div class="tw-space-y-4 tw-col-span-2 lg:tw-col-span-1">
                    <div class="tw-flex tw-gap-4 tw-items-center">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/logo-mt.svg"
                             width="60" height="60" alt="Logo MegaTraderX"/>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/megatrader-original.svg"
                             alt="Logo"
                             width="200"
                             height="45"
                        />
                    </div>
                    <div
                            class="tw-justify-start tw-text-stone-400 tw-text-sm tw-font-medium tw-leading-tight">From
                        evaluation to funding, we're redefining the trader journey with performance-driven
                        solutions and transparency.
                    </div>
                    <?php require_once 'sections/partials/social_media.php' ?>
                </div>
                <div class="tw-col-span-2 tw-space-y-4 lg:tw-col-span-1">
                    <?php require_once 'sections/partials/subscribe_form.php' ?>
                </div>
            </div>
            <div class="tw-my-8 tw-col-span-2">
                <div class="tw-h-0 tw-border-t-[0.5px] tw-border-t-neutral-700"></div>
            </div>
            <div
                    class="tw-grid tw-grid-cols-4 tw-gap-4 lg:tw-inline-flex lg:tw-justify-start lg:tw-items-start lg:tw-gap-8 lg:tw-w-full">
                <div
                        class="tw-col-span-full tw-text-center lg:tw-text-left lg:tw-flex-1 tw-text-stone-400 tw-text-sm tw-font-medium tw-leading-tight">
                    © <?= date('Y') ?> MegaTraderX
                </div>
                <nav
                        class="tw-col-span-full tw-flex-col tw-space-y-4 sm:tw-space-y-0 sm:tw-text-center sm:tw-flex-none sm:tw-justify-center sm:tw-gap-4 lg:tw-contents">
                    <?php foreach ($btnRows as $index => $buttons) : ?>
                        <div class="tw-flex tw-justify-center tw-space-x-4 lg:tw-space-x-0 sm:tw-contents">
                            <?php foreach ($buttons as $button) : ?>
                                <?= $button ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </nav>
            </div>
        </div>
    </footer>

<?php require_once 'sections/footer_modals.php' ?>