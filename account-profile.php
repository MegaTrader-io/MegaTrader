<?php
/**
 * Template Name: Account Profile
 */

defined('ABSPATH') || exit;

get_header();
?>

    <div class="container">
        <div class="mt-page">
            <div class="mt-page__sidebar">
                <?php if (function_exists('render_sidebar')) {
                    render_sidebar();
                } ?>
            </div>
            <div class="mt-page__main">
                <div class="account-settings">
                    <div class="section-header mb-4">
                        <div class="text-white text-size-20 fw-medium text-uppercase">ACCOUNT SETTINGS</div>
                        <span class="text-a8a29e text-14px-line-20px fw-medium">Manage your personal information, security and verification settings.</span>
                    </div>

                    <div class="account-settings__sections-wrapper" id="accordionExample">

                        <div class="mt-card account-settings__section toggle-panel toggle-panel--expanded">
                            <h2 class="toggle-panel__title-wrapper">
                                <button class="toggle-panel__header" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <div class="toggle-panel__icon">
                                        <svg width="26" height="26" viewBox="0 0 26 26" fill="none"
                                             xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5.3125 19.375C6.375 18.5625 7.5625 17.9219 8.875 17.4531C10.1875 16.9844 11.5625 16.75 13 16.75C14.4375 16.75 15.8125 16.9844 17.125 17.4531C18.4375 17.9219 19.625 18.5625 20.6875 19.375C21.4167 18.5208 21.9844 17.5521 22.3906 16.4688C22.7969 15.3854 23 14.2292 23 13C23 10.2292 22.026 7.86979 20.0781 5.92188C18.1302 3.97396 15.7708 3 13 3C10.2292 3 7.86979 3.97396 5.92188 5.92188C3.97396 7.86979 3 10.2292 3 13C3 14.2292 3.20312 15.3854 3.60937 16.4688C4.01562 17.5521 4.58333 18.5208 5.3125 19.375ZM13 14.25C11.7708 14.25 10.7344 13.8281 9.89063 12.9844C9.04688 12.1406 8.625 11.1042 8.625 9.875C8.625 8.64583 9.04688 7.60938 9.89063 6.76563C10.7344 5.92188 11.7708 5.5 13 5.5C14.2292 5.5 15.2656 5.92188 16.1094 6.76563C16.9531 7.60938 17.375 8.64583 17.375 9.875C17.375 11.1042 16.9531 12.1406 16.1094 12.9844C15.2656 13.8281 14.2292 14.25 13 14.25ZM13 25.5C11.2708 25.5 9.64583 25.1719 8.125 24.5156C6.60417 23.8594 5.28125 22.9688 4.15625 21.8438C3.03125 20.7188 2.14063 19.3958 1.48438 17.875C0.828125 16.3542 0.5 14.7292 0.5 13C0.5 11.2708 0.828125 9.64583 1.48438 8.125C2.14063 6.60417 3.03125 5.28125 4.15625 4.15625C5.28125 3.03125 6.60417 2.14063 8.125 1.48438C9.64583 0.828125 11.2708 0.5 13 0.5C14.7292 0.5 16.3542 0.828125 17.875 1.48438C19.3958 2.14063 20.7188 3.03125 21.8438 4.15625C22.9688 5.28125 23.8594 6.60417 24.5156 8.125C25.1719 9.64583 25.5 11.2708 25.5 13C25.5 14.7292 25.1719 16.3542 24.5156 17.875C23.8594 19.3958 22.9688 20.7188 21.8438 21.8438C20.7188 22.9688 19.3958 23.8594 17.875 24.5156C16.3542 25.1719 14.7292 25.5 13 25.5Z"
                                                  fill="white"/>
                                        </svg>
                                    </div>
                                    <div class="toggle-panel__details">
                                        <div class="toggle-panel__title">Personal information</div>
                                        <div class="toggle-panel__description">Update your profile details and contact
                                            information
                                        </div>
                                    </div>
                                    <div class="toggle-panel__arrow">
                                        <i class="mt-icon mt-icon-white mt-icon_caret-up-solid"></i>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseOne" class="toggle-panel__content collapse show"
                                 data-bs-parent="#accordionExample">
                                <div class="toggle-panel__body">
                                    <?php get_template_part("template-parts/account/account-settings-personal-information"); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-card account-settings__section toggle-panel toggle-panel--unverified">
                            <h2 class="toggle-panel__title-wrapper">
                                <button class="toggle-panel__header toggle-panel__header--collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                                        aria-controls="collapseTwo">
                                    <div class="toggle-panel__icon">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <mask id="mask0_15865_49246" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="30" height="30">
                                                <rect width="30" height="30" fill="#D9D9D9"/>
                                            </mask>
                                            <g mask="url(#mask0_15865_49246)">
                                                <path d="M13.25 20.75L22.0625 11.9375L20.3125 10.1875L13.25 17.25L9.6875 13.6875L7.9375 15.4375L13.25 20.75ZM15 27.5C13.2708 27.5 11.6458 27.1719 10.125 26.5156C8.60417 25.8594 7.28125 24.9688 6.15625 23.8438C5.03125 22.7188 4.14063 21.3958 3.48438 19.875C2.82812 18.3542 2.5 16.7292 2.5 15C2.5 13.2708 2.82812 11.6458 3.48438 10.125C4.14063 8.60417 5.03125 7.28125 6.15625 6.15625C7.28125 5.03125 8.60417 4.14063 10.125 3.48438C11.6458 2.82812 13.2708 2.5 15 2.5C16.7292 2.5 18.3542 2.82812 19.875 3.48438C21.3958 4.14063 22.7188 5.03125 23.8438 6.15625C24.9688 7.28125 25.8594 8.60417 26.5156 10.125C27.1719 11.6458 27.5 13.2708 27.5 15C27.5 16.7292 27.1719 18.3542 26.5156 19.875C25.8594 21.3958 24.9688 22.7188 23.8438 23.8438C22.7188 24.9688 21.3958 25.8594 19.875 26.5156C18.3542 27.1719 16.7292 27.5 15 27.5Z" fill="white"/>
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="toggle-panel__details">
                                        <div class="toggle-panel__title">Verification</div>
                                        <div class="toggle-panel__description">
                                            Verify your identity to unlock full account features
                                        </div>
                                    </div>
                                    <div class="toggle-panel__arrow">
                                        <i class="mt-icon mt-icon-white mt-icon_caret-up-solid"></i>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseTwo" class="toggle-panel__content collapse"
                                 data-bs-parent="#accordionExample">
                                <div class="toggle-panel__body">
                                    <?php get_template_part("template-parts/account/account-settings-verification"); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-card account-settings__section toggle-panel">
                            <h2 class="toggle-panel__title-wrapper">
                                <button class="toggle-panel__header toggle-panel__header--collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                    <div class="toggle-panel__icon">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <mask id="mask0_15865_49307" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="30" height="30">
                                                <rect width="30" height="30" fill="#D9D9D9"/>
                                            </mask>
                                            <g mask="url(#mask0_15865_49307)">
                                                <path d="M13.6875 19.4375L20.75 12.375L18.9688 10.5938L13.6875 15.875L11.0625 13.25L9.28125 15.0313L13.6875 19.4375ZM15 27.5C12.1042 26.7708 9.71354 25.1094 7.82812 22.5156C5.94271 19.9219 5 17.0417 5 13.875V6.25L15 2.5L25 6.25V13.875C25 17.0417 24.0573 19.9219 22.1719 22.5156C20.2865 25.1094 17.8958 26.7708 15 27.5Z" fill="white"/>
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="toggle-panel__details">
                                        <div class="toggle-panel__title">Password</div>
                                        <div class="toggle-panel__description">
                                            Change your account password
                                        </div>
                                    </div>
                                    <div class="toggle-panel__arrow">
                                        <i class="mt-icon mt-icon-white mt-icon_caret-up-solid"></i>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseThree" class="toggle-panel__content collapse"
                                 data-bs-parent="#accordionExample">
                                <div class="toggle-panel__body">
                                    <?php get_template_part("template-parts/account/account-settings-password"); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-card account-settings__section toggle-panel toggle-panel--disabled">
                            <h2 class="toggle-panel__title-wrapper">
                                <button class="toggle-panel__header toggle-panel__header--collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false"
                                        aria-controls="collapseFour">
                                    <div class="toggle-panel__icon">
                                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <mask id="mask0_15865_49314" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="30" height="30">
                                                <rect width="30" height="30" fill="#D9D9D9"/>
                                            </mask>
                                            <g mask="url(#mask0_15865_49314)">
                                                <path d="M15 27.5313C14.6667 27.5313 14.349 27.4688 14.0469 27.3438C13.7448 27.2188 13.4688 27.0417 13.2188 26.8125L3.1875 16.7813C2.95833 16.5313 2.78125 16.2552 2.65625 15.9531C2.53125 15.651 2.46875 15.3333 2.46875 15C2.46875 14.6667 2.53125 14.3438 2.65625 14.0313C2.78125 13.7188 2.95833 13.4479 3.1875 13.2188L13.2188 3.1875C13.4688 2.9375 13.7448 2.75521 14.0469 2.64063C14.349 2.52604 14.6667 2.46875 15 2.46875C15.3333 2.46875 15.6563 2.52604 15.9688 2.64063C16.2813 2.75521 16.5521 2.9375 16.7813 3.1875L26.8125 13.2188C27.0625 13.4479 27.2448 13.7188 27.3594 14.0313C27.474 14.3438 27.5313 14.6667 27.5313 15C27.5313 15.3333 27.474 15.651 27.3594 15.9531C27.2448 16.2552 27.0625 16.5313 26.8125 16.7813L16.7813 26.8125C16.5521 27.0417 16.2813 27.2188 15.9688 27.3438C15.6563 27.4688 15.3333 27.5313 15 27.5313ZM13.75 16.25H16.25V8.75H13.75V16.25ZM15 20C15.3542 20 15.651 19.8802 15.8906 19.6406C16.1302 19.401 16.25 19.1042 16.25 18.75C16.25 18.3958 16.1302 18.099 15.8906 17.8594C15.651 17.6198 15.3542 17.5 15 17.5C14.6458 17.5 14.349 17.6198 14.1094 17.8594C13.8698 18.099 13.75 18.3958 13.75 18.75C13.75 19.1042 13.8698 19.401 14.1094 19.6406C14.349 19.8802 14.6458 20 15 20Z" fill="white"/>
                                            </g>
                                        </svg>
                                    </div>
                                    <div class="toggle-panel__details">
                                        <div class="toggle-panel__title">Two-Factor Authentication</div>
                                        <div class="toggle-panel__description">
                                            Add an extra layer of security to your account
                                        </div>
                                    </div>
                                    <div class="toggle-panel__arrow">
                                        <i class="mt-icon mt-icon-white mt-icon_caret-up-solid"></i>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseFour" class="toggle-panel__content collapse"
                                 data-bs-parent="#accordionExample">
                                <div class="toggle-panel__body">
                                    <?php get_template_part("template-parts/account/account-settings-two-factor-authentication"); ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php get_footer(); ?>