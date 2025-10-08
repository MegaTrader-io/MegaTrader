<?php
$features = [
        [
                'icon' => <<<SVG
<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_15865_49161" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
    <rect width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_15865_49161)">
    <path d="M10.95 15.55L16.6 9.9L15.175 8.475L10.95 12.7L8.85 10.6L7.425 12.025L10.95 15.55ZM12 22C9.68333 21.4167 7.77083 20.0875 6.2625 18.0125C4.75417 15.9375 4 13.6333 4 11.1V5L12 2L20 5V11.1C20 13.6333 19.2458 15.9375 17.7375 18.0125C16.2292 20.0875 14.3167 21.4167 12 22Z" fill="#FFB34A"/>
    </g>
</svg>
SVG,
                'title' => '100% Safe',
                'description' => 'Your data is secured by AES-grade encryption.',
        ],
        [
                'icon' => <<<SVG
<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_15865_49167" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="25" height="24">
    <rect x="0.333008" width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_15865_49167)">
    <path d="M8.33301 22L9.33301 15H4.33301L13.333 2H15.333L14.333 10H20.333L10.333 22H8.33301Z" fill="#FFB34A"/>
    </g>
</svg>
SVG,
                'title' => 'Fast Process',
                'description' => 'Identity check takes only a couple of minutes.',
        ],
        [
                'icon' => <<<SVG
<svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_15865_49173" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="25" height="24">
    <rect x="0.666992" width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_15865_49173)">
    <path d="M6.66699 20C5.56699 20 4.62533 19.6083 3.84199 18.825C3.05866 18.0417 2.66699 17.1 2.66699 16V8C2.66699 6.9 3.05866 5.95833 3.84199 5.175C4.62533 4.39167 5.56699 4 6.66699 4H18.667C19.767 4 20.7087 4.39167 21.492 5.175C22.2753 5.95833 22.667 6.9 22.667 8V16C22.667 17.1 22.2753 18.0417 21.492 18.825C20.7087 19.6083 19.767 20 18.667 20H6.66699ZM6.66699 8H18.667C19.0337 8 19.3837 8.04167 19.717 8.125C20.0503 8.20833 20.367 8.34167 20.667 8.525V8C20.667 7.45 20.4712 6.97917 20.0795 6.5875C19.6878 6.19583 19.217 6 18.667 6H6.66699C6.11699 6 5.64616 6.19583 5.25449 6.5875C4.86283 6.97917 4.66699 7.45 4.66699 8V8.525C4.96699 8.34167 5.28366 8.20833 5.61699 8.125C5.95033 8.04167 6.30033 8 6.66699 8ZM4.81699 11.25L15.942 13.95C16.092 13.9833 16.242 13.9833 16.392 13.95C16.542 13.9167 16.6837 13.85 16.817 13.75L20.292 10.85C20.1087 10.6 19.8753 10.3958 19.592 10.2375C19.3087 10.0792 19.0003 10 18.667 10H6.66699C6.23366 10 5.85449 10.1125 5.52949 10.3375C5.20449 10.5625 4.96699 10.8667 4.81699 11.25Z" fill="#FFB34A"/>
    </g>
</svg>
SVG,
                'title' => 'Contract Ready',
                'description' => 'Skip KYC steps. Get your contract right away',
        ],
];


$is_verified = get_query_var('mt_is_verified');

?>

<div id="veriff-container"></div>

<div class="space-y-3">
    <div class="mt-card mt-card-dark gap-32 gap-md-3 mb-3 outline-dark outline-offset-1px">
        <div class="features">
            <?php foreach ($features as $feature): ?>
                <div class="features__item">
                    <div class="mt-card__icon-wrapper space-y-3">
                        <div class="d-flex align-items-center gap-1 text-white">
                            <?= $feature['icon'] ?> <span><?= $feature['title'] ?></span>
                        </div>
                        <div class="mt-card__item-text">
                            <?= $feature['description'] ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-md-flex align-items-center justify-content-between">
            <button type="button" <?= $is_verified ? 'disabled' : '' ?> id="get-verified-btn"
                    class="mega-btn-md mega-btn-secondary-md">
                GET VERIFIED NOW
            </button>

            <div class="mt-32 mt-md-0">
                <svg width="118" height="34" viewBox="0 0 118 34" fill="none" xmlns="http://www.w3.org/2000/svg"
                     xmlns:xlink="http://www.w3.org/1999/xlink">
                    <mask id="mask0_15723_44657" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                          width="118"
                          height="34">
                        <rect y="0.5" width="118" height="33" fill="url(#pattern0_15723_44657)"/>
                    </mask>
                    <g mask="url(#mask0_15723_44657)">
                        <rect y="0.5" width="92" height="33" fill="#A8A29E"/>
                        <rect width="38" height="33" transform="matrix(-1 0 0 1 130 0.5)" fill="#A8A29E"/>
                    </g>
                    <defs>
                        <pattern id="pattern0_15723_44657" patternContentUnits="objectBoundingBox" width="1" height="1">
                            <use xlink:href="#image0_15723_44657"
                                 transform="matrix(0.00347222 0 0 0.0124158 0 -0.00284091)"/>
                        </pattern>
                        <image id="image0_15723_44657" width="288" height="81" preserveAspectRatio="none"
                               xlink:href="data:image/png;base64,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"/>
                    </defs>
                </svg>
            </div>
        </div>
    </div>
    <div>
        <div class="verification-process">
            <div class="verification-process__container">
                <h2 class="verification-process__title">Verification process</h2>

                <div class="verification-step">
                    <div class="verification-step__icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_15723_44814" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                  width="24" height="24">
                                <rect width="24" height="24" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_15723_44814)">
                                <path d="M17.55 12L14 8.45L15.425 7.05L17.55 9.175L21.8 4.925L23.2 6.35L17.55 12ZM9 12C7.9 12 6.95833 11.6083 6.175 10.825C5.39167 10.0417 5 9.1 5 8C5 6.9 5.39167 5.95833 6.175 5.175C6.95833 4.39167 7.9 4 9 4C10.1 4 11.0417 4.39167 11.825 5.175C12.6083 5.95833 13 6.9 13 8C13 9.1 12.6083 10.0417 11.825 10.825C11.0417 11.6083 10.1 12 9 12ZM1 20V17.2C1 16.6333 1.14583 16.1125 1.4375 15.6375C1.72917 15.1625 2.11667 14.8 2.6 14.55C3.63333 14.0333 4.68333 13.6458 5.75 13.3875C6.81667 13.1292 7.9 13 9 13C10.1 13 11.1833 13.1292 12.25 13.3875C13.3167 13.6458 14.3667 14.0333 15.4 14.55C15.8833 14.8 16.2708 15.1625 16.5625 15.6375C16.8542 16.1125 17 16.6333 17 17.2V20H1Z"
                                      fill="#FFB34A"/>
                            </g>
                        </svg>
                    </div>
                    <div class="verification-step__content">
                        <h3 class="verification-step__title">Check your account information</h3>
                        <p class="verification-step__description">
                            Please check that your account information matches with your government-issued ID to avoid
                            any inconveniences
                            during verification. You will not be able to change this information afterward.
                        </p>
                    </div>
                </div>

                <div class="verification-step">
                    <div class="verification-step__icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_15723_44820" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                  width="24" height="24">
                                <rect width="24" height="24" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_15723_44820)">
                                <path d="M6 20C4.9 20 3.95833 19.6083 3.175 18.825C2.39167 18.0417 2 17.1 2 16V8C2 6.9 2.39167 5.95833 3.175 5.175C3.95833 4.39167 4.9 4 6 4H18C19.1 4 20.0417 4.39167 20.825 5.175C21.6083 5.95833 22 6.9 22 8V16C22 17.1 21.6083 18.0417 20.825 18.825C20.0417 19.6083 19.1 20 18 20H6ZM6 8H18C18.3667 8 18.7167 8.04167 19.05 8.125C19.3833 8.20833 19.7 8.34167 20 8.525V8C20 7.45 19.8042 6.97917 19.4125 6.5875C19.0208 6.19583 18.55 6 18 6H6C5.45 6 4.97917 6.19583 4.5875 6.5875C4.19583 6.97917 4 7.45 4 8V8.525C4.3 8.34167 4.61667 8.20833 4.95 8.125C5.28333 8.04167 5.63333 8 6 8ZM4.15 11.25L15.275 13.95C15.425 13.9833 15.575 13.9833 15.725 13.95C15.875 13.9167 16.0167 13.85 16.15 13.75L19.625 10.85C19.4417 10.6 19.2083 10.3958 18.925 10.2375C18.6417 10.0792 18.3333 10 18 10H6C5.56667 10 5.1875 10.1125 4.8625 10.3375C4.5375 10.5625 4.3 10.8667 4.15 11.25Z"
                                      fill="#FFB34A"/>
                            </g>
                        </svg>
                    </div>
                    <div class="verification-step__content">
                        <h3 class="verification-step__title">Prepare your physical ID cards</h3>
                        <p class="verification-step__description">
                            You will be asked to take a photo of either your ID card, driving license, or other
                            government-issued cards.
                            Make sure you take a photo of your physical ID. Copies, screenshots, or other forms will be
                            declined.
                        </p>
                    </div>
                </div>

                <div class="verification-step">
                    <div class="verification-step__icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_15723_44826" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                  width="24" height="24">
                                <rect width="24" height="24" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_15723_44826)">
                                <path d="M12 17.5C13.25 17.5 14.3125 17.0625 15.1875 16.1875C16.0625 15.3125 16.5 14.25 16.5 13C16.5 11.75 16.0625 10.6875 15.1875 9.8125C14.3125 8.9375 13.25 8.5 12 8.5C10.75 8.5 9.6875 8.9375 8.8125 9.8125C7.9375 10.6875 7.5 11.75 7.5 13C7.5 14.25 7.9375 15.3125 8.8125 16.1875C9.6875 17.0625 10.75 17.5 12 17.5ZM12 15.5C11.3 15.5 10.7083 15.2583 10.225 14.775C9.74167 14.2917 9.5 13.7 9.5 13C9.5 12.3 9.74167 11.7083 10.225 11.225C10.7083 10.7417 11.3 10.5 12 10.5C12.7 10.5 13.2917 10.7417 13.775 11.225C14.2583 11.7083 14.5 12.3 14.5 13C14.5 13.7 14.2583 14.2917 13.775 14.775C13.2917 15.2583 12.7 15.5 12 15.5ZM4 21C3.45 21 2.97917 20.8042 2.5875 20.4125C2.19583 20.0208 2 19.55 2 19V7C2 6.45 2.19583 5.97917 2.5875 5.5875C2.97917 5.19583 3.45 5 4 5H7.15L9 3H15L16.85 5H20C20.55 5 21.0208 5.19583 21.4125 5.5875C21.8042 5.97917 22 6.45 22 7V19C22 19.55 21.8042 20.0208 21.4125 20.4125C21.0208 20.8042 20.55 21 20 21H4Z"
                                      fill="#FFB34A"/>
                            </g>
                        </svg>

                    </div>
                    <div class="verification-step__content">
                        <h3 class="verification-step__title">Begin the verification process</h3>
                        <p class="verification-step__description">
                            Click the button below to start the verification process. You will be asked to take a photo
                            of your ID and
                            yourself.
                        </p>
                    </div>
                </div>

                <div class="verification-step">
                    <div class="verification-step__icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_15723_44832" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                                  width="24" height="24">
                                <rect width="24" height="24" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_15723_44832)">
                                <path d="M12 11C13.1 11 14.0417 10.6083 14.825 9.825C15.6083 9.04167 16 8.1 16 7V4H8V7C8 8.1 8.39167 9.04167 9.175 9.825C9.95833 10.6083 10.9 11 12 11ZM4 22V20H6V17C6 15.9833 6.2375 15.0292 6.7125 14.1375C7.1875 13.2458 7.85 12.5333 8.7 12C7.85 11.4667 7.1875 10.7542 6.7125 9.8625C6.2375 8.97083 6 8.01667 6 7V4H4V2H20V4H18V7C18 8.01667 17.7625 8.97083 17.2875 9.8625C16.8125 10.7542 16.15 11.4667 15.3 12C16.15 12.5333 16.8125 13.2458 17.2875 14.1375C17.7625 15.0292 18 15.9833 18 17V20H20V22H4Z"
                                      fill="#FFB34A"/>
                            </g>
                        </svg>
                    </div>
                    <div class="verification-step__content">
                        <h3 class="verification-step__title">Wait for confirmation</h3>
                        <p class="verification-step__description">
                            You will be notified that your account has been verified. Usually it takes under a minute.
                            All accounts
                            waiting for verification will be released immediately.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .features {
        gap: 16px;
        justify-content: space-between;
        align-items: center;
    }

    .features > * + * {
        margin-top: 16px;
    }

    .mt-32 {
        margin-top: 32px;
    }

    @media (min-width: 768px) {
        .gap-md-3 {
            gap: 16px;
        }

        .features {
            display: flex;
        }

        .features > * + * {
            margin-top: 0;
        }
    }

    .features__item {
        display: flex;
        align-items: center;
    }

    .verification-process {
        display: flex;
    }

    .verification-process__container {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding: 16px 0;
        border-radius: 16px;
    }

    .verification-process__title {
        font-size: 20px;
        margin-bottom: 0;
        font-weight: 300;
        text-transform: uppercase;
        line-height: 24px;
    }

    .verification-step {
        display: flex;
        position: relative;
        align-items: flex-start;
        gap: 16px;
    }

    .verification-step:after {
        content: ' ';
        width: 2px;
        height: calc(100% - 20px);
        position: absolute;
        background: #404040;
        top: 40px;
        left: 20px;
    }

    .verification-step:last-child:after {
        content: none;
    }

    .verification-step__icon {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 8px;
        background: #1e1e1e;
        border-radius: 20px;
        outline: 4px solid #404040;
        flex-shrink: 0;
    }

    .verification-step__content {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .verification-step__title {
        color: #fff;
        margin-bottom: 0;
        font-size: 20px;
        font-weight: 500;
        text-transform: uppercase;
        line-height: 24px;
    }

    .verification-step__description {
        color: #a8a29e;
        margin-bottom: 0;
        max-width: 696px;
        font-size: 16px;
        font-weight: 500;
        line-height: 24px;
    }
</style>