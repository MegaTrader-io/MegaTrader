<?php
$email = 'support@megatrader.io';
?>
<div class="mgt-card space-y-4 mt-8 border-none border-transparent">
    <div
            class="justify-start text-white text-xl font-medium leading-8">Still
        have questions?
    </div>

    <div
            class="justify-start text-stone-400 text-base font-medium leading-8">Contact
        our support team and we will make sure everything is clear and intuitive for you!
    </div>

    <div>
        <div class="w-full px-4 py-3 bg-[#1e1e1e]/70 rounded-xl outline outline-1 outline-offset-[-1px] outline-neutral-700 inline-flex justify-start items-center gap-2">
            <div class="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">
                <?= $email ?>
            </div>
            <button type="button"
                    data-copy-text="<?= $email ?>"
                    class="btn-copy relative disabled:text-stone-600 disabled:cursor-not-allowed text-[#ffd78a]">
                <div class="flex items-center gap-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="clipboard-mask" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                              width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9"></rect>
                        </mask>
                        <g mask="url(#clipboard-mask)">
                            <path d="M9 18C8.45 18 7.97917 17.8042 7.5875 17.4125C7.19583 17.0208 7 16.55 7 16V4C7 3.45 7.19583 2.97917 7.5875 2.5875C7.97917 2.19583 8.45 2 9 2H18C18.55 2 19.0208 2.19583 19.4125 2.5875C19.8042 2.97917 20 3.45 20 4V16C20 16.55 19.8042 17.0208 19.4125 17.4125C19.0208 17.8042 18.55 18 18 18H9ZM5 22C4.45 22 3.97917 21.8042 3.5875 21.4125C3.19583 21.0208 3 20.55 3 20V6H5V20H16V22H5Z"
                                  fill="currentColor"></path>
                        </g>
                    </svg>
                    <div class="text-right justify-start text-[#ffd78a] text-sm font-medium  uppercase leading-tight">
                        Copy
                    </div>
                </div>
                <span class="absolute top-[-20px] -translate-x-[45%] text-xs text-gray-400 hidden mgt-copy-tooltip">copied</span>
            </button>
        </div>
    </div>
</div>