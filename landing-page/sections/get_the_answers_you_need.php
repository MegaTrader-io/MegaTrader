<?php

$faqsGroup = [
    [
        "id" => "getting-started",
        "category" => "Getting Started",
        "faqs" => [
            [
                "question" => "How do I sign up for an account?",
                "answer" => "Choose your plan, create an account using your email, and get immediate access to your evaluation account."
            ],
            [
                "question" => "What do I need to pass the evaluation?",
                "answer" => "Reach the profit target while staying within risk limits such as daily loss and trailing drawdown. Consistency is key."
            ],
            [
                "question" => "Can I start right after payment?",
                "answer" => "Yes. As soon as your payment is processed, your evaluation account is instantly activated and ready for trading."
            ],
            [
                "question" => "Is this suitable for beginners?",
                "answer" => "The program is built for traders with some experience, but motivated beginners who understand the basics are welcome to join."
            ]
        ]
    ],
    [
        "id" => "pricing-payouts",
        "category" => "Pricing & Payouts",
        "faqs" => [
            [
                "question" => "How much does it cost to join?",
                "answer" => "Plans start at $50.00/month depending on account size. Each plan includes access to the evaluation system and trading tools."
            ],
            [
                "question" => "What is the profit split?",
                "answer" => "Funded traders keep 90% of their profits—among the most competitive payouts in the industry."
            ],
            [
                "question" => "How do I receive my payouts?",
                "answer" => "Payouts are processed weekly and sent via RiseWorks, Bitcoin, or Ethereum—based on your selected method."
            ],
            [
                "question" => "Are there any hidden or recurring fees?",
                "answer" => "No hidden fees. You only pay for your plan, plus optional fees for resets or activation if applicable."
            ]
        ]
    ],
    [
        "id" => "affiliate-program",
        "category" => "Affiliate Program",
        "faqs" => [
            [
                "question" => "What is the MegaTrader affiliate program?",
                "answer" => "It’s a referral program where you earn recurring commissions for bringing new traders to the platform."
            ],
            [
                "question" => "How do I earn commission?",
                "answer" => "Share your custom affiliate link. You earn every time someone signs up and remains active through your referral."
            ],
            [
                "question" => "Can traders also become affiliates?",
                "answer" => "Absolutely. Many of our best affiliates are also funded traders building both income streams."
            ],
            [
                "question" => "What’s the earning potential?",
                "answer" => "There’s no cap. Affiliates can earn from hundreds to thousands monthly, depending on referral volume and activity."
            ]
        ]
    ],
    [
        "id" => "platform-features",
        "category" => "Platform & Features",
        "faqs" => [
            [
                "question" => "Which trading platforms can I use?",
                "answer" => "We support NinjaTrader, Quantower, Tradovate, and more—ensuring seamless and professional trading experiences."
            ],
            [
                "question" => "Can I use automated trading systems?",
                "answer" => "Yes. Automation is allowed as long as your strategy stays within our risk parameters and trading rules."
            ],
            [
                "question" => "Do I get a performance dashboard?",
                "answer" => "Yes. You’ll have access to a real-time dashboard tracking metrics like profit/loss, win rate, drawdown, and consistency."
            ],
            [
                "question" => "Is there a mobile trading option?",
                "answer" => "Yes. Most supported platforms offer mobile apps for monitoring and managing trades on the go."
            ]
        ]
    ],
    [
        "id" => "account-compliance",
        "category" => "Account & Compliance",
        "faqs" => [
            [
                "question" => "What happens if I break a rule?",
                "answer" => "Violating rules—such as exceeding max drawdown—will invalidate your evaluation. You may restart by purchasing a reset."
            ],
            [
                "question" => "How do I verify my identity for payouts?",
                "answer" => "You’ll complete a one-time KYC process before your first withdrawal to comply with payout and regulatory requirements."
            ],
            [
                "question" => "Can I change my plan after signing up?",
                "answer" => "No. Plans are fixed once chosen. To use a different account size, start a new evaluation under the desired plan."
            ],
            [
                "question" => "Is my data secure on your platform?",
                "answer" => "Yes. We implement enterprise-level encryption and strict data protocols to safeguard your personal and financial information."
            ]
        ]
    ]
];

$categories = get_faq_categories($faqsGroup);
$defaultCategoryId = $faqsGroup[0]['id'];

?>

<section id="faq" class="space-y-12 px-4">
    <div class="w-full space-y-4">
        <div
                class="justify-start text-center text-white text-[40px] font-light uppercase leading-[48px]">
            Get the Answers You Need
        </div>
        <div
                class="mx-auto max-w-[700px] text-center text-xl leading-8 font-medium text-stone-400 md:max-w-[860px]">
            From your first question to your last concern, we’ve answered it all—transparent policies, fast
            support, and a process built for trader success.
        </div>
    </div>

    <div class="space-y-12 md:space-y-0 md:grid md:grid-cols-2 md:gap-12">
        <div class="space-y-12">
            <div
                    class="justify-start text-white text-[40px] font-light uppercase leading-[48px]">
                Faqs
            </div>
            <div
                    class="justify-start text-stone-400 text-xl font-medium leading-8">
                Everything you need to know about features, membership, and troubleshooting.
            </div>

            <div>
                <div class="lg:inline">
                    <?php
                    $index = 0;
                    foreach ($categories as $id => $description): ?>
                        <?php
                        $button_id = "button_" . $id;
                        ?>
                        <div class="<?= $index > 1 ? 'md:contents' : 'contents' ?>">
                            <label
                                    for="<?= $button_id ?>"
                                    class="group px-3 py-2 rounded-[64px] cursor-pointer inline-flex justify-start items-center gap-2 mr-2 mb-2 bg-mgt-dark text-teal-400 has-[:checked]:text-[#1e1e1e] has-[:checked]:bg-teal-500 has-[:checked]:active">
                                <input type="radio" name="category_option" id="<?= $button_id ?>" value="<?= $id ?>"
                                       class="hidden" <?= $id == $defaultCategoryId ? 'checked' : '' ?> />
                                <div class="justify-start text-base font-medium leading-normal">
                                    <?= $description ?>
                                </div>
                            </label>
                        </div>
                        <?php
                        $index++;
                        ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div>
            <div class="faqs-by-category">
                <?php render_faqs($defaultCategoryId, $faqsGroup); ?>
            </div>

            <?php require 'partials/still_have_questions.php'; ?>
        </div>
    </div>
</section>

