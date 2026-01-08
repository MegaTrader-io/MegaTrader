<?php
$faqs_list = [
        [
                "question" => "How do I sign up for an account?",
                "answer" => "Choose your plan, create an account using your email, and get immediate access to your evaluation account.",
                "isOpen" => true
        ],
        [
                "question" => "What do I need to pass the evaluation?",
                "answer" => "Maintain the minimum trading days and reach the profit target while respecting the maximum drawdown limits.",
                "isOpen" => false
        ],
        [
                "question" => "Can I start right after payment?",
                "answer" => "Yes, once the payment is confirmed, your credentials will be sent to your email immediately.",
                "isOpen" => false
        ],
        [
                "question" => "Is this suitable for beginners?",
                "answer" => "While we provide tools and support, basic knowledge of trading and risk management is highly recommended.",
                "isOpen" => false
        ]
];
?>

<section class="faqs-bs" id="faqs-bs">
    <header class="section-header text-center">
        <h2 class="section-header__title">
            FREQUENTLY ASKED <span>QUESTIONS</span>
        </h2>
    </header>

    <div class="accordion">
        <?php foreach ($faqs_list as $faq): ?>
            <div class="accordion__item <?= $faq['isOpen'] ? 'accordion__item--active' : '' ?>">
                <div class="accordion__header">
                    <div class="accordion__question">
                        <?= htmlspecialchars($faq['question']) ?>
                    </div>
                    <div class="accordion__icon-wrapper">
                        <svg class="accordion__icon icon-minus" width="30" height="30" viewBox="0 0 30 30"
                             fill="none">
                            <mask style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="30" height="30">
                                <rect width="30" height="30" fill="#D9D9D9"></rect>
                            </mask>
                            <g>
                                <path d="M6.25 16.25V13.75H23.75V16.25H6.25Z" fill="white"></path>
                            </g>
                        </svg>

                        <svg class="accordion__icon icon-plus" width="30" height="30" viewBox="0 0 30 30"
                             fill="none">
                            <mask style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="30" height="30">
                                <rect width="30" height="30" fill="#D9D9D9"></rect>
                            </mask>
                            <g>
                                <path d="M13.75 16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75V16.25Z"
                                      fill="white"></path>
                            </g>
                        </svg>
                    </div>
                </div>

                <div class="accordion__content">
                    <?= htmlspecialchars($faq['answer']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="support-card">
        <div class="support-card__header">
            <div class="support-card__subtitle">Have more questions?</div>
            <div class="support-card__title">Get lightning fast support</div>
        </div>
        <div class="support-card__body">
            <div class="support-card__description">
                Find instant answers in our Help Center, or ask us on Discord.
            </div>
            <div class="support-card__actions">
                <a href="https://help.megatrader.io/en/" class="mega-btn-md mega-btn-secondary-md w-100">OPEN HELP CENTER</a>
                <a href="https://discord.com/invite/megatrader" class="mega-btn-md mega-btn-default-md w-100">CHECK DISCORD</a>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const accordionItems = document.querySelectorAll('.accordion__item');

        accordionItems.forEach(item => {
            const header = item.querySelector('.accordion__header');

            header.addEventListener('click', () => {
                const isActive = item.classList.contains('accordion__item--active');

                // 1. Opcional: Cerrar todos los demás antes de abrir el actual
                accordionItems.forEach(otherItem => {
                    otherItem.classList.remove('accordion__item--active');
                });

                // 2. Si el que clickeamos no estaba activo, lo activamos
                if (!isActive) {
                    item.classList.add('accordion__item--active');
                }
            });
        });
    });
</script>