<div class="tw-w-full alert-success tw-hidden">
    <div class="tw-p-4 tw-bg-[#1e1e1e] tw-rounded-lg tw-justify-start tw-items-start tw-gap-4 tw-flex tw-overflow-hidden">
        <div class="tw-w-6 tw-h-6 tw-flex tw-items-center tw-justify-center tw-rounded-full tw-bg-teal-400">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"
                 data-slot="icon" class="tw-w-5 tw-h-5 tw-text-black">
                <path fill-rule="evenodd"
                      d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z"
                      clip-rule="evenodd"></path>
            </svg>
        </div>
        <div class="tw-grow tw-shrink tw-basis-0 tw-text-teal-400 tw-text-base tw-font-medium tw-leading-normal">
            Congratulations! You have successfully subscribed.
        </div>
    </div>
</div>

<div id="form-subscription" class="tw-group">
    <form novalidate class="tw-grid tw-grid-rows-2 tw-items-start tw-gap-2 tw-mb-4 lg:tw-flex">
        <label class="tw-w-full">
        <span class="tw-flex tw-items-center tw-relative">
            <input required
                   type="email"
                   placeholder="Enter your email"
                   class="tw-peer mgt-input mgt-input-ok"
                   autocomplete="off"
                   name="email">
        </span>
            <span id="email-error"
                  class="tw-hidden tw-text-rose-500 tw-text-xs tw-mt-4 tw-leading-tight">Invalid email</span>
        </label>
        <button type="submit" disabled class="btn-yellow-link tw-rounded-xl tw-h-12 tw-px-4 tw-py-3">
            SUBSCRIBE
        </button>
    </form>
    <input type="checkbox" class="tw-sr-only tw-peer" id="email_consent" name="email_consent">
    <div class="peer-checked:tw-outline-primary">
        <label for="email_consent" class="tw-flex tw-items-start tw-cursor-pointer">
        <span class="tw-relative tw-mt-1 tw-mr-2">
            <span class="box-checked tw-w-4 tw-h-4 tw-border-mgt-primary tw-block tw-border-2 tw-rounded-sm tw-justify-center">
                <svg class="tw-w-3 tw-h-3 tw-bg-primary tw-text-black tw-hidden" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor"
                     stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </span>
        </span>
            <span class="tw-select-none tw-w-full tw-text-white tw-text-base tw-font-medium tw-leading-normal">I consent to the use of my email
            address to receive news, updates, and important notifications.
        </span>
        </label>
    </div>
</div>