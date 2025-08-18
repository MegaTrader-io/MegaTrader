<div id="hero-section" class="px-4">
    <div class="py-12 space-y-12">
        <div class="flex justify-center">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/megatraderX.svg"
                 width="250"
                 height="41" alt="logo megatrader x"/>
        </div>

        <div class="space-y-4">
            <div class="self-stretch text-center justify-start text-white text-6xl font-light uppercase leading-[72px]">
                Start Your Futures Journey
            </div>
            <div class="w-full max-w-[612px] mx-auto text-center justify-start text-stone-400 text-xl font-medium leading-8">
                Empowering traders with innovative solutions, unmatched reliability, and tools designed to elevate
                your
                trading journey to new heights.
            </div>
        </div>

        <div class="space-y-4 md:space-y-0 md:flex md:justify-center md:gap-4">
            <a href="https://subscriptions.megatrader.io/" class="btn-yellow-link rounded-xl h-12 px-4 py-3">
                Start trading
            </a>
            <a href="https://discord.com/invite/megatrader" target="_blank"
               class="btn-dark-link rounded-xl h-12 px-4 py-3">
                Join Discord
            </a>
        </div>

        <div class="grid grid-cols-1 space-y-4 lg:space-y-0 lg:flex lg:gap-4 justify-center">
            <a href="https://megatrader.io" target="_blank"
               class="pl-3 pr-4 py-3 rounded-xl outline outline-2 outline-offset-[-2px] outline-neutral-700 inline-flex justify-center items-center gap-2">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/try-on-web.svg"
                     width="30"
                     height="30" alt="web icon"/>
                <div class="flex flex-col items-start">
                    <span class="text-stone-400 text-sm font-medium leading-tight">Try the</span>
                    <span class="text-white text-xl font-medium leading-8">WEB APP</span>
                </div>
            </a>

            <a href="https://megatrader.io" target="_blank"
               class="pl-3 pr-4 py-3 rounded-xl outline outline-2 outline-offset-[-2px] outline-neutral-700 inline-flex justify-center items-center gap-2">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/try-on-apple.svg"
                     width="30" height="30" alt="apple icon"/>
                <div class="flex flex-col items-start">
                    <span class="text-stone-400 text-sm font-medium leading-tight">Downloaded on the</span>
                    <span class="text-white text-xl font-medium leading-8">APP STORE</span>
                </div>
            </a>

            <a href="https://megatrader.io" target="_blank"
               class="pl-3 pr-4 py-3 rounded-xl outline outline-2 outline-offset-[-2px] outline-neutral-700 inline-flex justify-center items-center gap-2">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/try-on-google-play.svg"
                     width="30" height="30" alt="play icon"/>
                <div class="flex flex-col items-start">
                    <span class="text-stone-400 text-sm font-medium leading-tight">Get it on</span>
                    <span class="text-white text-xl font-medium leading-8">GOOGLE PLAY</span>
                </div>
            </a>
        </div>

        <div class="mx-auto flex justify-center items-center border-t-8 border-b-8 md:border-8 lg:rounded-lg border-[#3C383A] w-fit bg-[#3C383A]">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/landing-page/metrics2.jpeg"
                 alt="window tablet" width="1200" height="670" class="lg:rounded-lg"/>
        </div>

        <?php require 'partials/feature_highlights.php'; ?>
    </div>
</div>