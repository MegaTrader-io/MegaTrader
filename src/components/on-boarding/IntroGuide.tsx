"use client";

import {useEffect} from "react";
import "intro.js/introjs.css";
import introJs from "intro.js";
import {IntroStep} from "intro.js/src/core/steps";
import "../../app/introGuide.css";

interface IntroGuideProps {
    currentPath: string;
}

export default function IntroGuide({currentPath}: IntroGuideProps) {
    useEffect(() => {
        try {
            const hasSeenIntro = localStorage.getItem(`hasSeenIntro-${currentPath}`);

            if (!hasSeenIntro) {
                const steps = getStepsForPath(currentPath);
                if (steps.length === 0) return;

                const intro = introJs();
                intro.setOptions({
                    steps: steps.map((step, index) => ({
                        title: step.title,
                        element: step.element,
                        intro: `
                            <div class="intro-content">
                                <p>${step.intro}</p>
                                <div class="intro-buttons grid grid-cols-[48px_1fr_48px] items-center h-12 gap-2">
                                    ${customPrevButtonMirror()}
                                    ${customSkipButton()}
                                    ${index === steps.length - 1 ? customFinishButton() : ""}
                                    ${customNextButtonMirror()}
                                </div>
                            </div>`,
                        position: adjustPosition(step.position || "bottom"),
                    })) as Partial<IntroStep>[],
                    scrollToElement: true,
                    positionPrecedence: ["bottom", "top", "right", "left"],
                    showProgress: false,
                    showBullets: false,
                    exitOnOverlayClick: false,
                    showStepNumbers: false,
                    disableInteraction: true,
                    hidePrev: true,
                    hideNext: true,
                    showButtons: true,
                    nextLabel: customNextButton(),
                    prevLabel: customPrevButton(),
                    tooltipClass: "custom-intro-tooltip",
                });

                setTimeout(() => {
                    void intro.start();

                    document.querySelector('body')?.addEventListener('click', function (event: MouseEvent) {
                        const target = event.target as Element | null;

                        if (target?.closest('.custom-prev-mirror')) {
                            const btn = document.querySelector('.introjs-prevbutton') as HTMLButtonElement | null;
                            btn?.dispatchEvent(new Event('click'));
                        }

                        if (target?.closest('.custom-next-mirror')) {
                            const btn = document.querySelector('.introjs-nextbutton') as HTMLButtonElement | null;
                            btn?.dispatchEvent(new Event('click'));
                        }
                    });
                }, 800);
            }
        } catch (error) {
            console.error("Unable to active Intro.js:", error);
        }
    }, [currentPath]);

    return null;
}

function adjustPosition(position: string): string {
    const positionMap: Record<string, string> = {
        bottom: "bottom",
        top: "top",
        left: "left",
        right: "right",
    };
    return positionMap[position] || position;
}

function getStepsForPath(path: string) {
    const stepsMap: Record<string, { title: string; element: string; intro: string; position?: string }[]> = {
        "/account-overview": [
            {
                title: "Top Navigation",
                element: "#manage-subscription",
                intro: "Manage and switch between accounts, subscriptions, and challenges easily. Reset your challenge or create a new account when needed.",
                position: "bottom"
            },
            {
                title: "Platform Access",
                element: "#platform-access",
                intro: "Displays the logo of your selected trading platform. View and copy your login credentials for quick access.",
                position: "top"
            },
            {
                title: "Account Overview",
                element: "#account-overview",
                intro: "Monitor your balance, profit, and trading days. Stay within risk limits by tracking your loss limit and current equity.",
                position: "top"
            },
            {
                title: "Challenge & Payout Objectives",
                element: "#challenge-payout-objectives",
                intro: "Track your progress toward profit targets, trading day requirements, and consistency rules. Stay updated on payout eligibility.",
                position: "top"
            },
            {
                title: "Rules & Compliance",
                element: "#rules-compliance",
                intro: "Keep your account above the required balance and follow risk management guidelines to stay eligible.",
                position: "top"
            },
            {
                title: "Balance Graph",
                element: "#balance-graph",
                intro: "Visualize your account balance over time, track profit targets, and ensure you stay above the minimum balance requirement.",
                position: "top"
            },
            {
                title: "Market Performance Tabs",
                element: "#market-performance-tabs",
                intro: "Filter and analyze your trading performance based on different instruments like E-mini S&P 500, NASDAQ 100, and more.",
                position: "top"
            },
            {
                title: "Profitability Metrics",
                element: "#profitability-metrics",
                intro: "Check average profit/loss per trade, win percentage, and risk-to-reward ratio to optimize your trading strategy.",
                position: "top"
            },
            {
                title: "Daily Journal",
                element: "#daily-journal",
                intro: "Log daily trade performance, emotions, and plan adherence. Use the journal pop-up to reflect on your trading day.",
                position: "top"
            },
        ],
        "/affiliates": [
            {
                title: "Affiliate Summary",
                element: "#affiliate-summary",
                intro: "Track your total earnings, active referrals, and sales performance. Stay updated on your commission progress and referral activity.",
                position: "bottom"
            },
            {
                title: "Referral Program",
                element: "#referral-program",
                intro: "Earn rewards by inviting friends. Send invitations, generate commissions from sign-ups, and use your earnings to trade for free.",
                position: "bottom"
            },
            {
                title: "Invite Your Friends",
                element: "#invite-your-friends",
                intro: "Send referral invites via email or copy your unique referral link to share on social media or directly with others.",
                position: "bottom"
            },
            {
                title: "Earnings Over Time",
                element: "#earnings-over-time",
                intro: "Analyze your affiliate earnings with a performance graph showing trends over a selected time period.",
                position: "bottom"
            },
            {
                title: "Traffic & Conversion Table",
                element: "#traffic-conversion-table",
                intro: "Monitor referral traffic, source URLs, and conversion rates to track visitor engagement and successful sign-ups.",
                position: "bottom"
            },
        ],
        "/payouts": [
            {
                title: "Payout Summary",
                element: "#payout-summary",
                intro: "Track your withdrawable profit, total earnings, profit share percentage, and next payout date to plan your withdrawals efficiently.",
                position: "bottom"
            },
            {
                title: "Available Payment Methods",
                element: "#available-payment-methods",
                intro: "View all supported withdrawal methods, including bank transfers and crypto, and select the best option for your needs.",
                position: "bottom"
            },
            {
                title: "Request Withdrawal Button",
                element: "#request-withdrawal-button",
                intro: "Click the button to submit a withdrawal request instantly when you meet the eligibility requirements for payouts.",
                position: "bottom"
            },
            {
                title: "Payout History Table",
                element: "#payout-history-table",
                intro: "Review your past and pending payout requests, including approval status, payment method, and transaction details in one place.",
                position: "bottom"
            },
            {
                title: "Purpose of the Payouts Page",
                element: "#purpose-of-the-payouts-page",
                intro: "Easily manage withdrawals, monitor payout progress, and stay informed about your earnings and available balance at all times.",
                position: "bottom"
            },
        ]
    };

    return stepsMap[path] || [];
}

function customFinishButton() {
    return `<button class="custom-intro-button custom-finish">FINISH</button>`;
}

function customSkipButton() {
    return `<button class="btn-dark-link w-full rounded-sm font-['Roboto'] text-[14px] px-3 py-0.5 custom-intro-button custom-skip">SKIP</button>`;
}

function customNextButton() {
    return `<button class="custom-intro-button custom-next">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_7151_429" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="-1" y="0" width="25" height="24">
                <rect x="-0.5" width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_7151_429)">
                <path d="M12.1 12L7.5 7.4L8.9 6L14.9 12L8.9 18L7.5 16.6L12.1 12Z" fill="currentColor"/>
            </g>
        </svg>
</button>`;
}


function customPrevButton() {
    return `<button class="custom-intro-button custom-prev">
 <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <mask id="mask0_7158_4863" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="25" height="24">
            <rect width="24" height="24" transform="matrix(-1 0 0 1 24.5 0)" fill="#D9D9D9"/>
        </mask>
        <g mask="url(#mask0_7158_4863)">
            <path d="M11.9 12L16.5 7.4L15.1 6L9.1 12L15.1 18L16.5 16.6L11.9 12Z" fill="currentColor"/>
        </g>
    </svg>
</button>`;
}


function customNextButtonMirror() {
    return `<button class="custom-intro-button custom-next-mirror flex w-full h-full justify-center items-center">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_7151_429" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="-1" y="0" width="25" height="24">
                <rect x="-0.5" width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_7151_429)">
                <path d="M12.1 12L7.5 7.4L8.9 6L14.9 12L8.9 18L7.5 16.6L12.1 12Z" fill="currentColor"/>
            </g>
        </svg>
</button>`;
}

function customPrevButtonMirror() {
    return `<button class="custom-intro-button custom-prev-mirror flex w-full h-full justify-center items-center">
 <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <mask id="mask0_7158_4863" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="25" height="24">
            <rect width="24" height="24" transform="matrix(-1 0 0 1 24.5 0)" fill="#D9D9D9"/>
        </mask>
        <g mask="url(#mask0_7158_4863)">
            <path d="M11.9 12L16.5 7.4L15.1 6L9.1 12L15.1 18L16.5 16.6L11.9 12Z" fill="currentColor"/>
        </g>
    </svg>
</button>`;
}
