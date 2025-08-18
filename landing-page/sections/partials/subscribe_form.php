<div class="w-full alert-success hidden">
    <div class="p-4 bg-[#1e1e1e] rounded-lg justify-start items-start gap-4 flex overflow-hidden">
        <div class="w-6 h-6 flex items-center justify-center rounded-full bg-teal-400">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"
                 data-slot="icon" class="w-5 h-5 text-black">
                <path fill-rule="evenodd"
                      d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z"
                      clip-rule="evenodd"></path>
            </svg>
        </div>
        <div class="grow shrink basis-0 self-stretch text-teal-400 text-base font-medium leading-normal">
            Congratulations! You have successfully subscribed.
        </div>
    </div>
</div>

<div id="form-subscription" class="group">
    <form novalidate class="grid grid-rows-2 items-start gap-2 mb-4 lg:flex">
        <label class="w-full">
        <span class="flex items-center relative">
            <input required
                   type="email"
                   placeholder="Enter your email"
                   class="peer mgt-input mgt-input-ok"
                   autocomplete="off"
                   name="email">
        </span>
            <span id="email-error" class="hidden text-rose-500 text-xs mt-4 leading-tight">Invalid email</span>
        </label>
        <button type="submit" disabled class="btn-yellow-link rounded-xl h-12 px-4 py-3">
            SUBSCRIBE
        </button>
    </form>
    <input type="checkbox" class="sr-only peer" id="email_consent" name="email_consent">
    <div class="peer-checked:outline-primary">
        <label for="email_consent" class="flex items-start cursor-pointer">
        <span class="relative mt-1 mr-2">
            <span class="box-checked w-4 h-4 border-mgt-primary block border-2 rounded-sm justify-center">
                <svg class="w-3 h-3 bg-primary text-black hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </span>
        </span>
            <span class="select-none w-full text-white text-base font-medium leading-normal">I consent to the use of my email
            address to receive news, updates, and important notifications.
        </span>
        </label>
    </div>
</div>