<?php
$verified = $args['verified'] ?? false;
?>

<div class="mt-verified" data-verified="<?= $verified ? '1' : '0' ?>">
    <div class="mt-verified__status-icon">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_15865_48540" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                  width="20" height="20">
                <rect width="20" height="20" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_15865_48540)">
                <path d="M8.83366 13.8332L14.7087 7.95817L13.542 6.7915L8.83366 11.4998L6.45866 9.12484L5.29199 10.2915L8.83366 13.8332ZM10.0003 18.3332C8.84755 18.3332 7.76421 18.1144 6.75033 17.6769C5.73644 17.2394 4.85449 16.6457 4.10449 15.8957C3.35449 15.1457 2.76074 14.2637 2.32324 13.2498C1.88574 12.2359 1.66699 11.1526 1.66699 9.99984C1.66699 8.84706 1.88574 7.76373 2.32324 6.74984C2.76074 5.73595 3.35449 4.854 4.10449 4.104C4.85449 3.354 5.73644 2.76025 6.75033 2.32275C7.76421 1.88525 8.84755 1.6665 10.0003 1.6665C11.1531 1.6665 12.2364 1.88525 13.2503 2.32275C14.2642 2.76025 15.1462 3.354 15.8962 4.104C16.6462 4.854 17.2399 5.73595 17.6774 6.74984C18.1149 7.76373 18.3337 8.84706 18.3337 9.99984C18.3337 11.1526 18.1149 12.2359 17.6774 13.2498C17.2399 14.2637 16.6462 15.1457 15.8962 15.8957C15.1462 16.6457 14.2642 17.2394 13.2503 17.6769C12.2364 18.1144 11.1531 18.3332 10.0003 18.3332Z"
                      fill="currentColor"/>
            </g>
        </svg>
    </div>
    <div class="mt-verified__status-text mt-verified__status-text_verified">
        VERIFIED
    </div>
    <div class="mt-verified__status-text mt-verified__status-text_unverified">
        UNVERIFIED
    </div>
</div>