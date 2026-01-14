<?php
$zero_plan_rules = [
        [
                'value' => '2%',
                'title' => 'Profit Target',
                'text' => 'Simple two percent goal to pass the challenge.'
        ],
        [
                'value' => '1 day',
                'title' => 'Min Trading Days',
                'text' => 'Qualify in as little as one trading day.'
        ],
        [
                'value' => 'None',
                'title' => 'Daily Loss Limit',
                'text' => 'No daily loss limits restrict your trading activity.'
        ],
        [
                'value' => 'EoD',
                'title' => 'Drawdown Mode',
                'text' => 'Risk is calculated at the end of each day.'
        ],
        [
                'value' => '90%',
                'title' => 'Profit Split',
                'text' => 'Keep ninety percent of profits once funded.'
        ]
];
?>

<section id="how-it-works" class="how-it-works-zero-plan">
    <div class="landing-bs-container">
        <header class="section-header text-center">
            <h2 class="section-header__title">
                <span>ZERO PLAN</span> PROGRAM RULES
            </h2>
            <p class="section-header__subtitle mb-0">
                Designed to remove barriers while keeping trading rules simple and disciplined.
                Start for just $10 and earn funding by meeting clear, achievable goals.
            </p>
        </header>

        <div class="zero-plan-grid">
            <?php if (!empty($zero_plan_rules) && is_array($zero_plan_rules)): ?>
                <?php foreach ($zero_plan_rules as $rule): ?>
                    <?php
                    $value = isset($rule['value']) ? esc_html($rule['value']) : '';
                    $title = isset($rule['title']) ? esc_html($rule['title']) : '';
                    $text = isset($rule['text']) ? esc_html($rule['text']) : '';
                    ?>
                    <div class="zero-plan-grid__item">
                        <?php if ($value): ?>
                            <div class="zero-plan-grid__value"><?= $value; ?></div>
                        <?php endif; ?>

                        <?php if ($title): ?>
                            <div class="zero-plan-grid__title"><?= $title; ?></div>
                        <?php endif; ?>

                        <?php if ($text): ?>
                            <div class="zero-plan-grid__text"><?= $text; ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted">No program rules available at this time.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
