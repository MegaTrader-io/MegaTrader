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
?>
<div class="mt-card mt-card-dark h😀-auto space-y-3">
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

    <div>
        <button type="button" disabled class="mega-btn-md mega-btn-secondary-md">
            GET VERIFIED NOW
        </button>
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

    @media (min-width: 768px) {
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
</style>