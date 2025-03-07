"use client";

import {useEffect} from "react";
import "intro.js/introjs.css";
import introJs from "intro.js";
import {IntroStep} from "intro.js/src/core/steps";
import "../app/introGuide.css";

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
                    steps: steps.map(step => ({
                        title: step.title,
                        element: step.element,
                        intro: step.intro,
                        position: adjustPosition(step.position || "bottom"),
                    })) as Partial<IntroStep>[],
                    scrollToElement: true, // Hace scroll automático si el tooltip está fuera de pantalla
                    positionPrecedence: ["bottom", "top", "right", "left"], // Prioriza ubicaciones disponibles
                    showProgress: false,
                    showBullets: false,
                    exitOnOverlayClick: false,
                    showStepNumbers: false,
                    disableInteraction: true,
                    hidePrev: true,
                    hideNext: true,
                    showButtons: true,
                    doneLabel: customSkipButton(),
                    nextLabel: customNextButton(),
                    prevLabel: customPrevButton(),
                    tooltipClass: "custom-intro-tooltip",
                });

                setTimeout(() => {
                    void intro.start();
                }, 800);
            }
        } catch (error) {
            console.error("Error iniciando Intro.js:", error);
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
        ]
    };

    return stepsMap[path] || [];
}

function customSkipButton() {
    return `<button class="custom-intro-button custom-skip">SKIP</button>`;
}

function customNextButton() {
    return `<button class="custom-intro-button custom-next">→</button>`;
}

function customPrevButton() {
    return `<button class="custom-intro-button custom-prev">←</button>`;
}
