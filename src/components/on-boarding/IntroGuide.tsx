"use client";

import {useEffect, useRef} from "react";
import "intro.js/introjs.css";
import introJs from "intro.js";
import {IntroStep} from "intro.js/src/core/steps";
import "../../app/introGuide.css";
import {IntroJs} from "intro.js/src/intro";

interface IntroGuideProps {
    currentPath: string;
}

export default function IntroGuide({currentPath}: IntroGuideProps) {
    const introRef = useRef<IntroJs | null>(null);
    const btnPrevRef = useRef<HTMLButtonElement | null>(null);
    const btnNextRef = useRef<HTMLButtonElement | null>(null);

    const adjustTooltipSize = () => {
        setTimeout(() => {
            const introjsHelperLayer = document.querySelector(".introjs-helperLayer") as HTMLDivElement || null;
            const tooltips = document.querySelectorAll(".introjs-tooltip");
            const overlay = document.querySelector(".introjs-overlay") as HTMLDivElement || null;

            if (overlay) {
                overlay.remove();
            }

            if (introjsHelperLayer) {
                introjsHelperLayer.style.boxShadow = '';
                introjsHelperLayer.style.borderRadius = '1rem';
            }

            tooltips.forEach((tooltip) => {
                const element = tooltip.parentElement?.querySelector(".introjs-tooltip-reference");
                if (element) {
                    const rect = element.getBoundingClientRect();

                    (tooltip as HTMLElement).style.width = `${rect.width}px`;
                    (tooltip as HTMLElement).style.height = `${rect.height}px`;
                    (tooltip as HTMLElement).style.boxSizing = "border-box";
                }
            });
        }, 50);
    };

    const updateButtonStyles = () => {
        if (!introRef.current) return;

        const totalSteps = introRef.current._introItems.length - 1;
        const currentStep = introRef.current.currentStep();

        if (btnPrevRef.current) {
            btnPrevRef.current.style.color = currentStep === 0 ? "#57534E" : "white";
        }

        if (btnNextRef.current) {
            btnNextRef.current.style.color = currentStep === totalSteps ? "#57534E" : "white";
        }
    };

    const initializeButtons = () => {
        btnPrevRef.current = document.querySelector(".custom-prev-mirror") as HTMLButtonElement;
        btnNextRef.current = document.querySelector(".custom-next-mirror") as HTMLButtonElement;

        if (!btnPrevRef.current || !btnNextRef.current) {
            setTimeout(initializeButtons, 100);
        } else {
            // console.info("btn in memory:", {btnPrev: btnPrevRef.current, btnNext: btnNextRef.current});
        }
    };

    const observeTooltipChanges = () => {
        const isMobile = window.innerWidth <= 768

        const observer = new MutationObserver(() => {
            const tooltip = document.querySelector(".introjs-tooltip") as HTMLElement | null;
            if (!tooltip) return;

            const tooltipRect = tooltip.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            const navbarHeight = 100;
            const scrollPadding = 186;

            if (!introRef.current) {
                return;
            }

            const steps = getStepsForPath(currentPath);
            const introScreen = steps[introRef.current.currentStep()];
            if (!introScreen) {
                return;
            }

            let stepElementSelector = introScreen.element;

            if (introRef.current._direction === 'backward') {
                if (isMobile) {
                    if (stepElementSelector === '#account-overview') {
                        window.scrollTo({
                            top: 76,
                            behavior: "smooth"
                        });

                        return;
                    } else if (stepElementSelector === '#challenge-payout-objectives') {
                        window.scrollTo({
                            top: 412,
                            behavior: "smooth"
                        });

                        return;
                    } else if (stepElementSelector === '#rules-compliance') {
                        window.scrollTo({
                            top: 697,
                            behavior: "smooth"
                        });

                        return;
                    }
                } else {
                    if (stepElementSelector === '#rules-compliance') {
                        stepElementSelector = '#challenge-payout-objectives';
                    } else if (stepElementSelector === '#challenge-payout-objectives') {
                        stepElementSelector = '#account-overview';
                    }
                }

                const stepElement = document.querySelector(stepElementSelector) as HTMLElement | null;
                if (!stepElement) return;

                const stepRect = stepElement.getBoundingClientRect();

                if (stepRect.top < navbarHeight) {
                    window.scrollBy({
                        top: stepRect.top - navbarHeight - scrollPadding - 100,
                        behavior: "smooth",
                    });
                }

                return;
            }

            if (introRef.current._direction === 'forward') {
                if (stepElementSelector === '#platform-access') {
                    console.info('tooltipRect.top - navbarHeight - scrollPadding', tooltipRect.top - navbarHeight - scrollPadding);
                    window.scrollBy({
                        top: tooltipRect.top - navbarHeight - scrollPadding,
                        behavior: "smooth",
                    });

                    return;
                }
            }

            if (tooltipRect.top < navbarHeight) {
                window.scrollBy({
                    top: tooltipRect.top - navbarHeight - scrollPadding,
                    behavior: "smooth",
                });
            } else if (tooltipRect.bottom > windowHeight) {
                window.scrollBy({
                    top: tooltipRect.bottom - windowHeight + scrollPadding,
                    behavior: "smooth",
                });
            }
        });

        observer.observe(document.body, {childList: true, subtree: true});

        return observer;
    };

    useEffect(() => {
        let observerTooltip: MutationObserver, observer: MutationObserver;

        try {
            const hasSeenIntro = localStorage.getItem(`hasSeenIntro-${currentPath}`);

            if (!hasSeenIntro) {
                const steps = getStepsForPath(currentPath);
                if (steps.length === 0) return;

                const intro: IntroJs = introJs();
                introRef.current = intro;

                intro.setOptions({
                    helperElementPadding: 0,
                    overlayOpacity: 0.95,
                    steps: steps.map((step, index) => ({
                        title: step.title,
                        element: step.element,
                        intro: `
                            <div class="intro-content">
                                <p>${step.intro}</p>
                                <div class="intro-buttons grid grid-cols-[48px_1fr_48px] items-center h-12 gap-2">
                                    ${customPrevButtonMirror()}
                                    ${index === steps.length - 1 ? customFinishButton() : customSkipButton()}
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
                    showButtons: false,
                    nextLabel: customNextButton(),
                    prevLabel: customPrevButton(),
                    tooltipClass: "custom-intro-tooltip",
                });

                intro.onexit(() => {
                    document.querySelectorAll('.bg-card-onboarding,.without-bg-card-onboarding,.bg-btn-onboarding').forEach((element => {
                        element.classList.remove('bg-card-onboarding');
                        element.classList.remove('without-bg-card-onboarding');
                        element.classList.remove('bg-btn-onboarding');
                    }));
                })

                intro.onafterchange(() => {
                    updateButtonStyles();
                    adjustTooltipSize()
                    const currentElementID = steps[intro.currentStep()].element as string || null;
                    console.info('currentElementID ', currentElementID);

                    steps.forEach(step => {
                        const elementId = step.element.replace('#', '');

                        if (step.element === currentElementID) {
                            return;
                        }

                        const element = document.getElementById(elementId) as HTMLDivElement || null;

                        if (elementId === 'account-overview' || elementId === 'challenge-payout-objectives') {
                            const parentElement = element?.parentElement?.parentElement as HTMLDivElement || null;
                            if (parentElement) {
                                parentElement.classList.add('bg-card-onboarding');
                            }

                            return;
                        }

                        if (elementId === 'rules-compliance') {
                            return;
                        }

                        if (elementId === 'market-performance-tabs') {
                            element.classList.add('without-bg-card-onboarding')
                            const btns = element?.querySelectorAll<HTMLButtonElement>('.btn-metric,.btn-scroll-right,.btn-scroll-left');
                            if (btns && btns.length > 0) {
                                btns.forEach(btn => {
                                    btn.classList.add('bg-btn-onboarding');
                                })
                            }

                            return;
                        }

                        if (element) {
                            element.classList.add('bg-card-onboarding')
                            if (step.className) {
                                element.classList.add(step.className)
                            }
                        }
                    });


                    if (currentElementID) {
                        console.info(intro._direction);
                        const elementId = currentElementID.replace('#', '');
                        const element = document.getElementById(elementId) as HTMLDivElement || null;

                        if (element) {
                            if (elementId === 'account-overview' || elementId === 'challenge-payout-objectives') {
                                const parentElement = element?.parentElement?.parentElement as HTMLDivElement || null;
                                if (parentElement) {
                                    parentElement.classList.remove('bg-card-onboarding', 'introjs-relativePosition');
                                }

                                return;
                            }

                            if (elementId === 'rules-compliance') {
                                const parentElement = element.parentElement?.parentElement?.parentElement?.parentElement?.parentElement as HTMLDivElement || null;
                                if (parentElement) {
                                    parentElement.classList.remove('bg-card-onboarding', 'introjs-relativePosition');
                                }

                                return;
                            }

                            if (elementId === 'market-performance-tabs') {
                                element.classList.remove('without-bg-card-onboarding')
                                const btns = element?.querySelectorAll<HTMLButtonElement>('.btn-metric,.btn-scroll-right,.btn-scroll-left');
                                if (btns && btns.length > 0) {
                                    btns.forEach(btn => {
                                        btn.classList.remove('bg-btn-onboarding');
                                    })
                                }

                                const profitabilityMetrics = document.getElementById('profitability-metrics') as HTMLDivElement || null;
                                if (profitabilityMetrics) {
                                    profitabilityMetrics.classList.remove('bg-card-onboarding', 'introjs-relativePosition');
                                    profitabilityMetrics.classList.add('without-bg-card-onboarding');
                                }

                                return;
                            }

                            if (elementId === 'profitability-metrics') {
                                const marketPerformanceTabs = document.getElementById('market-performance-tabs') as HTMLDivElement || null;
                                if (marketPerformanceTabs) {
                                    const btns = marketPerformanceTabs.querySelectorAll<HTMLButtonElement>('.btn-metric,.btn-scroll-right,.btn-scroll-left');
                                    if (btns && btns.length > 0) {
                                        btns.forEach(btn => {
                                            btn.classList.remove('bg-btn-onboarding');
                                        })
                                    }
                                }

                                element.classList.remove('without-bg-card-onboarding', 'bg-card-onboarding', 'introjs-relativePosition')
                                return;
                            }

                            element.classList.remove('bg-card-onboarding', 'introjs-relativePosition')
                        }
                    }
                });

                observerTooltip = observeTooltipChanges();

                observer = new MutationObserver(() => {
                    initializeButtons();
                    updateButtonStyles();
                    adjustTooltipSize()
                });

                observer.observe(document.body, {childList: true, subtree: true});

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

                        if (target?.closest(".custom-skip") || target?.closest(".custom-finish")) {
                            introRef.current?.exit(true);
                            // localStorage.setItem(`hasSeenIntro-${currentPath}`, "1");
                        }
                    });

                    adjustTooltipSize();
                }, 501);
            }
        } catch (error) {
            console.error("Unable to active Intro.js:", error);
        }

        return () => {
            if (introRef.current) {
                introRef.current?.exit(true);
            }

            if (observer) {
                observer.disconnect();
            }

            if (observerTooltip) {
                observerTooltip.disconnect();
            }
        };
    }, [currentPath, introRef.current]);

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
    const stepsMap: Record<string, {
        title: string;
        element: string;
        intro: string;
        position?: string,
        className?: string
    }[]> = {
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
                position: "bottom",
                className: 'after:!rounded-lg'
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
    return `<button class="btn-dark-link w-full rounded-sm font-['Roboto'] text-[14px] px-3 py-0.5 custom-intro-button custom-finish">FINISH</button>`;
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
