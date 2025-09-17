<?php
$email = 'support@megatrader.io';
?>
<div class="mgt-card tw-space-y-4 tw-mt-8 tw-border-none tw-border-transparent">
    <div
            class="tw-justify-start tw-text-white tw-text-xl tw-font-medium tw-leading-8">Still
        have questions?
    </div>

    <div
            class="tw-justify-start tw-text-stone-400 tw-text-base tw-font-medium tw-leading-8">Contact
        our support team and we will make sure everything is clear and intuitive for you!
    </div>

    <div>
        <div class="tw-w-full tw-px-4 tw-py-3 tw-bg-[#1e1e1e]/70 tw-rounded-xl tw-outline tw-outline-1 tw-outline-offset-[-1px] tw-outline-neutral-700 tw-inline-flex tw-justify-start tw-items-center tw-gap-2">
            <div class="tw-flex-1 tw-justify-start tw-text-stone-400 tw-text-base tw-font-medium tw-leading-normal">
                <?= $email ?>
            </div>
            <button type="button"
                    data-copy-text="<?= $email ?>"
                    class="btn-copy tw-bg-transparent tw-relative disabled:tw-text-stone-600 disabled:tw-cursor-not-allowed tw-text-[#ffd78a]">
                <div class="tw-flex tw-items-center tw-gap-2">
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
                    <div class="tw-text-right tw-justify-start tw-text-[#ffd78a] tw-text-sm tw-font-medium  tw-uppercase tw-leading-tight">
                        Copy
                    </div>
                </div>
                <span class="tw-absolute tw-top-[-20px] -tw-translate-x-[45%] tw-text-xs tw-text-gray-400 tw-hidden mgt-copy-tooltip">copied</span>
            </button>
        </div>
    </div>
</div>