<?php

class Label
{
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
 
}
