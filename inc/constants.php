<?php

enum LayoutType: string
{
    case MyAccount = 'my_account';
    case LandingPage = 'landing_page';
}

class Label
{
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_BREACHED = 'BREACHED';
    public const STATUS_PASSED = 'PASSED';
    public const STATUS_UPGRADED = 'UPGRADED';
    public const STATUS_PENDING_ACTIVATION = 'PENDING_ACTIVATION';
    public const STATUS_RESET = 'RESET';

    public const PLAN_RULES_URLS = [
        'Funded' => 'https://help.megatrader.io/en/articles/11372568-funded-plan-rules-risk-parameters',
        'Growth' => 'https://help.megatrader.io/en/articles/10753687-growth-plan-rules-risk-parameters',
        'Elite' => 'https://help.megatrader.io/en/articles/10753681-elite-plan-rules-risk-parameters',
        'Consistency' => 'https://help.megatrader.io/en/articles/10753780-consistency-rule-for-payouts',
    ];


    public const ACCOUNT_STATUS_MAP = [
        'ACTIVE' => self::STATUS_ACTIVE,
        'BREACHED' => self::STATUS_BREACHED,
        'PASSED' => self::STATUS_PASSED,
        'UPGRADED' => self::STATUS_UPGRADED,
        'PENDING_ACTIVATION' => self::STATUS_PENDING_ACTIVATION,
        'RESET' => self::STATUS_RESET,
    ];

    public const PRODUCT_META = [
        "profit_target" => "Profit Target",
        "max_contracts" => "Max Contracts",
        "daily_loss_limit" => "Daily Loss Limit",
        "daily_loss_limit_soft_breach" => "Daily Loss Limit (Soft Breach)",
        "trailing_max_drawdown" => "Trailing Max Drawdown",
        "drawdown_mode" => "Drawdown Mode",
        "min_trading_days" => "Min Trading Days Pass",
        "min_trading_days_to_payout" => "Min Trading Days Payout",
        "reset_fee" => "Reset Fee",
        "activation_fee" => "Activation Fee",
        "consistency" => "Consistency",
        "max_accounts" => "Max Accounts",
        "objectives_rules" => "Objectives & Rules",
    ];

    public const PLATFORM = [
        "title" => "Platform"
    ];

    public const PRICE = [
        "price_sufix" => "Buying Power",
    ];

    public const FUTURES = [
        'size_section_title' => 'Trading capital',
        'plan_section_title' => 'Challenge type',
        'platform_section_title' => 'Platform',
        'plan_includes_title' => 'Included with your plan',
        'plan_includes_list' => [
            [
                'icon' => 'wallet',
                'text' => 'Practice trading with chosen virtual funds',
            ],
            [
                'icon' => 'desktop',
                'text' => 'Access your preferred trading platform',
            ],
            [
                'icon' => 'lightning',
                'text' => 'Professional trading using real-time data',

            ],
            [
                'icon' => 'pencil',
                'text' => 'Trading Journal and other supporting tools',
            ],
            [
                'icon' => 'mail',
                'text' => 'Customer Support available 24/5',
            ],
        ],
        'submit_btn_text' => 'Procede to checkout',
    ];

    public const PLANS_FEATURES = [
        'profit_target',
        'max_contracts',
        'daily_loss_limit',
        'daily_loss_limit_soft_breach',
        'trailing_max_drawdown',
        'drawdown_mode',
        'min_trading_days',
        'min_trading_days_to_payout',
        'reset_fee',
        'activation_fee',
        'consistency',
        'max_accounts',
    ];

    public const PRODUCT_META_ICONS = [
        'profit_target' => 'mt-icon_profit',
        'max_contracts' => 'mt-icon_max-contract',
        'daily_loss_limit' => 'mt-icon_daily-loss-limit',
        'daily_loss_limit_soft_breach' => 'mt-icon_daily-loss-limit',
        'trailing_max_drawdown' => 'mt-icon_max-drawdown',
        'drawdown_mode' => 'mt-icon_drawdown-mode',
        'min_trading_days' => 'mt-icon_calendar',
        'min_trading_days_to_payout' => 'mt-icon_calendar',
        'reset_fee' => 'mt-icon_reset-fee',
        'activation_fee' => 'mt-icon_lightning',
        'consistency' => 'mt-icon_checkmark-solid',
        'max_accounts' => 'mt-icon_arrow-circle-solid',
        'account' => 'mt-icon_account',
        'mail' => 'mt-icon_mail',
        'phone' => 'mt-icon_phone',
        'home' => 'mt-icon_home',
    ];

    public const THANKYOU_META = [
        "success" => "success",
        "order_successful" => "Order Successful!",
        "trading_challenge_ready" => "Your trading challenge is ready.",
        "order" => "Order",
        "order_summary" => "Order Summary",
        "addons" => "Add Ons",
        "total_paid" => "Total Paid",
        "payment_method" => "Payment Method",
        "confirmation_email_sent" => "Confirmation email sent – Account activating now.",
        "go_to_my_account" => "Go to My Account",
    ];

    public const CHECKOUT_META = [
        "plan_option_title" => "Customize Your Plan (Optional)",
        "billing_title" => "Billing Details",
        "edit_billing" => "Edit Billing",
        "payment_title" => "Payment Method",
        "payment_disclaimer" => "All payments are secured and encrypted.",
    ];

    public const SIDEBAR_META = [
        "launch_button" => "Launch MegatraderX",
        "plan_title" => "Explore the plans",
        "plan_description" => "Find the perfect plan to enhance experience.",
        "plan_button" => "Buy a new challenge",
    ];

    public const META_RESET = [
        ['key' => 'restart_evaluation', 'label' => 'Restart Evaluation', 'value' => 'Restart anytime challenge', 'icon' => 'mt-icon_autorenew'],
        ['key' => 'one_time_fee', 'label' => 'One-Time Fee', 'value' => 'Single payment only', 'icon' => 'mt-icon_paid'],
        ['key' => 'keep_trading', 'label' => 'Keep Trading', 'value' => 'Trade without pause', 'icon' => 'mt-icon_play-arrow'],
        ['key' => 'ongoing_support', 'label' => 'Ongoing Support', 'value' => 'Always-on help', 'icon' => 'mt-icon_call'],
    ];

    public const META_ACTIVATION = [
        ['key' => 'active_account', 'label' => 'Active Account', 'value' => 'Instantly trade ready', 'icon' => 'mt-icon_lightning'],
        ['key' => 'one_time_fee', 'label' => 'One-Time Fee', 'value' => 'One single cost', 'icon' => 'mt-icon_paid'],
        ['key' => 'start_trading', 'label' => 'Start Trading', 'value' => 'Begin live trades', 'icon' => 'mt-icon_play-arrow'],
        ['key' => 'priority_support', 'label' => 'Priority Support', 'value' => 'Fast expert help', 'icon' => 'mt-icon_call'],
    ];

    public const META_ACCOUNT_OVERVIEW = [
        "page_title_overview" => "Account Overview",
        "page_subtitle_overview" => "View your trading metrics, account progress, and performance insights in one place",
        "account_feedback_title" => "How did it feel today?",
        "account_feedback_question" => "Did I follow my trading plan today?",
        "account_feedback_yes" => "Yes",
        "account_feedback_no" => "No",
        "account_feedback_save" => "Save",
        "account_feedback_edit" => "Edit",
        "account_feedback_placeholder" => "Summarize today’s trading",
        "web_app" => "Web App",
        "app_store" => "App Store",
        "play_store" => "Play Store",
        "account_data_title" => "Account Data",
        "account_platform_description" => "Access the platform",
        "account_login" => "Login",
        "account_password" => "Password",
        "account_server" => "Server",
        "performance_title_left" => "Overall performance",
        "performance_title_right_evaluation" => "Your Challenge Objective",
        "performance_title_right_funded" => "Your Payout Objectives",
        "performance_account_balance" => "Account Balance",
        "performance_total_profit" => "Total Profit",
        "performance_trading_days" => "Trading Days",
        "performance_current_equity" => "Current Equity",
        "performance_daily_loss_limit" => "Daily Loss Limit",
        "performance_daily_net_pl" => "Daily Net P&L",
        "performance_profit_target" => "Profit Target",
        "performance_payout_target" => "Payout Target",
        "performance_days_traded" => "Days Traded",
        "performance_rules" => "Rules",
        "performance_rules_description" => "Keep your Account Balance above",
        "performance_rules_description_funded_start" => "No day may exceed",
        "performance_rules_description_funded_ended" => "of total profits.",
        "performance_rules_link_text" => "Maximum Loss Limit",
        "performance_consistency_link_text" => "See Consistency Rule",
        "performance_dll_tooltip_title" => "Daily Loss Limit (DLL)",
        "performance_dll_tooltip_description" => "Reaching the DLL pauses trading for the day. It’s removed once a profit milestone is met.",
        "performance_dpl_tooltip_title" => "Daily P&L (DPL)",
        "performance_dpl_tooltip_description" => "Realised P&L amount at any time during the trading week (Sunday 5:00 PM - Friday 3:10 PM CT)",
        "performance_no_data" => "No performance data to render.",
        "feature_content_winning_trades" => "Winning Trades",
        "feature_content_losing_trades" => "Losing Trades",
        "feature_content_avg_winning_trade" => "Avg. Win",
        "feature_content_avg_losing_trade" => "Avg. Loss",
        "feature_content_risk_reward_ratio" => "Reward-to-Risk Ratio",
        "feature_content_risk_reward_ratio_tooltip_title" => "Reward-to-risk ratio",
        'feature_content_risk_reward_ratio_tooltip_description' =>
            '<p>Measures the potential reward (profit) you achieve per trade VS the risk (losses) you take</p>
     <span class="text-white">Tip:</span> One of the most important metrics to successful trading! Less risk
     and more reward increases your probability of continued profitability.',
        "feature_content_no_data" => "NO DATA AVAILABLE",
        "feature_content_reward" => "Reward",
        "feature_content_risk" => "Risk",
        "agreement_modal_title" => "Market Data Agreement required",
        "agreement_modal_body_title" => "OOPS!",
        "agreement_modal_body_description" => "Please sign data agreement!",
        "agreement_modal_body_subtitle" => "Before you can start trading you need to sign the data agreement.",
        "agreement_modal_button" => "Review & Sign",
        "breach_modal_title" => "Breach alert!",
        "breach_modal_body_title" => "OOPS!",
        "breach_modal_body_description" => "Your evaluation has failed!",
        "breach_modal_body_subtitle" => "In order to continue trading you need to reset your account.",
        "breach_modal_button" => "Reset account",
        'passed_modal_body_title' => 'Congrats!',
        'passed_modal_body_description' => 'Your account has passed the evaluation',
        'passed_modal_button' => 'Activation Pending',
        'passed_modal_title' => 'Account Passed',
        
        'passed_modal_note_status_passed_w_activation_id' => 'Activation will open once your account is ready.',
        'passed_modal_note_status_passed_no_activation_id' => 'Your account activates automatically once it’s ready.',
        
        'passed_modal_body_subtitle_default' => "Pay the activation fee below to activate your funded account.",
        'passed_modal_body_subtitle_w_activation_id' => "Your account is being prepared. You’ll be able to activate it soon.",
        'passed_modal_body_subtitle_no_activation_id' => "Your account is being prepared and will unlock automatically once ready.",

        'account_chart_overlay_no_data' => 'There is not enough data to generate the graph.',
        'account_daily_journal_overlay_no_data' => 'There is not enough data to display this tablet.',

    ];

}
