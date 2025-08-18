<?php

$buttons = [];
foreach (mgt_footer_links() as $index => $link) {
    $modalId = $link['id'];
    $title = $link['title'];
    $buttons[] = <<<HTML
<button data-dialog-id="{$modalId}" class="btn-dialog text-stone-400 text-sm font-medium underline leading-tight">
    {$title}
</button>
HTML;
}

$btnRows = array_chunk($buttons, 2);
?>
    <footer
            class="w-full max-w-7xl flex-1 h-dvh mx-auto px-4 pb-8  flex items-center justify-between flex-col space-y-8">
        <div
                class="w-full p-8 bg-[#131210] rounded-[20px] outline outline-1 outline-neutral-700">
            <div class="w-full grid grid-cols-2 space-y-8 lg:space-y-0 lg:space-x-8">
                <div class="space-y-4 col-span-2 lg:col-span-1">
                    <div class="flex gap-4 items-center">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/logo-mt.svg"
                             width="60" height="60" alt="Logo MegaTraderX"/>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/megatrader-original.svg"
                             alt="Logo"
                             width="200"
                             height="45"
                        />
                    </div>
                    <div
                            class="justify-start text-stone-400 text-sm font-medium leading-tight">From
                        evaluation to funding, we're redefining the trader journey with performance-driven
                        solutions and transparency.
                    </div>
                    <?php require_once 'sections/partials/social_media.php' ?>
                </div>
                <div class="col-span-2 space-y-4 lg:col-span-1">
                    <?php require_once 'sections/partials/subscribe_form.php' ?>
                </div>
            </div>
            <div class="my-8 col-span-2">
                <div class="h-0 border-t-[0.5px] border-t-neutral-700"></div>
            </div>
            <div
                    class="grid grid-cols-4 gap-4 lg:inline-flex lg:justify-start lg:items-start lg:gap-8 lg:w-full">
                <div
                        class="col-span-full text-center lg:text-left lg:flex-1 text-stone-400 text-sm font-medium leading-tight">
                    © <?= date('Y') ?> MegaTraderX
                </div>
                <nav
                        class="col-span-full flex-col space-y-4 sm:space-y-0 sm:text-center sm:flex-none sm:justify-center sm:gap-4 lg:contents">
                    <?php foreach ($btnRows as $index => $buttons) : ?>
                        <div class="flex justify-center space-x-4 lg:space-x-0 sm:contents">
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