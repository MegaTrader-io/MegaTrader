<div id="hero-section" class="tw-px-4">
    <div class="tw-py-12 tw-space-y-12">
        <div class="tw-px-4 tw-space-y-12 tw-relative">
            <div class="tw-flex tw-justify-center">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/megatraderX-blue.svg"
                     width="250"
                     height="41" alt="logo megatrader x"/>
            </div>

            <div class="tw-space-y-4">
                <div class="tw-self-stretch tw-text-center tw-justify-start tw-text-white tw-text-6xl tw-font-light tw-uppercase tw-leading-[72px]">
                    Start Your Futures Journey
                </div>
                <div class="tw-w-full tw-max-w-[612px] tw-mx-auto tw-text-center tw-justify-start tw-text-stone-400 tw-text-xl tw-font-medium tw-leading-8">
                    Empowering traders with innovative solutions, unmatched reliability, and tools designed to elevate
                    your
                    trading journey to new heights.
                </div>
            </div>

            <div class="tw-space-y-4 md:tw-space-y-0 md:tw-flex md:tw-justify-center md:tw-gap-4">
                <a href="<?= home_url('/auth/register') ?>"
                   class="btn-yellow-link tw-rounded-xl tw-h-12 tw-px-4 tw-py-3">
                    Start trading
                </a>
                <a href="https://discord.com/invite/megatrader" target="_blank"
                   class="btn-dark-link tw-rounded-xl tw-h-12 tw-px-4 tw-py-3">
                    Join Discord
                </a>
            </div>

            <div class="tw-grid tw-grid-cols-1 tw-space-y-4 lg:tw-space-y-0 lg:tw-flex lg:tw-gap-4 tw-justify-center">
                <a href="https://megatrader.io" target="_blank"
                   class="tw-pl-3 tw-pr-4 tw-py-3 tw-rounded-xl tw-border-2 tw-border-neutral-700 tw-inline-flex tw-justify-center tw-items-center tw-gap-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/try-on-web.svg"
                         width="30"
                         height="30"
                         alt="web icon"/>
                    <div class="tw-flex tw-flex-col tw-items-start">
                        <span class="tw-text-stone-400 tw-text-sm tw-font-medium tw-leading-tight">Try the</span>
                        <span class="tw-text-white tw-text-xl tw-font-medium tw-leading-8">WEB APP</span>
                    </div>
                </a>

                <a href="https://megatrader.io" target="_blank"
                   class="tw-pl-3 tw-pr-4 tw-py-3 tw-rounded-xl tw-border-2 tw-border-neutral-700 tw-inline-flex tw-justify-center tw-items-center tw-gap-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/try-on-apple.svg"
                         width="30" height="30" alt="apple icon"/>
                    <div class="tw-flex tw-flex-col tw-items-start">
                        <span class="tw-text-stone-400 tw-text-sm tw-font-medium tw-leading-tight">Downloaded on the</span>
                        <span class="tw-text-white tw-text-xl tw-font-medium tw-leading-8">APP STORE</span>
                    </div>
                </a>

                <a href="https://megatrader.io" target="_blank"
                   class="tw-pl-3 tw-pr-4 tw-py-3 tw-rounded-xl tw-border-2 tw-border-neutral-700 tw-inline-flex tw-justify-center tw-items-center tw-gap-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/try-on-google-play.svg"
                         width="30" height="30" alt="play icon"/>
                    <div class="tw-flex tw-flex-col tw-items-start">
                        <span class="tw-text-stone-400 tw-text-sm tw-font-medium tw-leading-tight">Get it on</span>
                        <span class="tw-text-white tw-text-xl tw-font-medium tw-leading-8">GOOGLE PLAY</span>
                    </div>
                </a>
            </div>


            <div class="tw-mx-auto tw-flex tw-justify-center tw-items-center tw-rounded-lg tw-outline tw-outline-2 md:tw-outline-[10px]  tw-outline-[#3b3739] tw-border-[#3C383A] tw-w-fit tw-bg-[#3C383A]">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/metrics22.jpg"
                     alt="window tablet" class="tw-max-w-full lg:tw-w-[1043px] tw-h-auto tw-rounded lg:tw-rounded-lg"/>
            </div>

            <div class="lg:tw-absolute lg:tw-left-0 lg:tw-bottom-0 tw-mx-auto tw-flex tw-justify-center tw-items-center tw-rounded-lg tw-outline tw-outline-2 md:tw-outline-[10px]  tw-outline-[#3b3739] tw-border-[#3C383A] tw-w-fit tw-bg-[#3C383A]">
                <div class="tw-group tw-w-full tw-p-6 tw-space-y-4 tw-bg-mgt-dark">
                    <div class="tw-space-y-2">
                        <div class="self-stretch text-center justify-start text-white text-xl font-bold leading-loose">Platform Life Demo</div>
                        <div class="self-stretch text-center justify-start text-stone-400 text-base font-medium leading-normal">ID: support@futuresfortraders.com</div>
                        <div class="self-stretch text-center justify-start text-stone-400 text-base font-medium leading-normal">Password: FUTURESFT25</div>
                    </div>
                    <button type="button" class="btn-dark-link tw-rounded-xl tw-w-full tw-h-12 tw-px-4 tw-py-3">
                        Open Platform
                    </button>
                </div>
            </div>

            <div class="lg:tw-absolute lg:tw-right-0 lg:tw-bottom-0 tw-mx-auto tw-flex tw-justify-center tw-items-center tw-rounded-lg tw-outline tw-outline-2 md:tw-outline-[10px]  tw-outline-[#3b3739] tw-border-[#3C383A] tw-w-fit tw-bg-[#3C383A]">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/for-tarders-mobile1.jpg"
                     alt="window tablet" class="tw-max-w-full lg:tw-w-[194px] tw-h-auto tw-rounded lg:tw-rounded-lg"/>
            </div>

        </div>


        <?php require 'partials/feature_highlights.php'; ?>
    </div>
</div>
