import {useEffect} from 'react';
import {driver, DriveStep, Config as DriverConfig, State as DriverState, PopoverDOM} from 'driver.js';
import 'driver.js/dist/driver.css';
import '../../../src/app/driveGuide.css'

interface IntroGuideProps {
    currentPath: string;
}

export default function DriverGuide({currentPath}: IntroGuideProps) {
    useEffect(() => {
        const steps = getStepsForPath(currentPath);
        if (steps.length === 0) return;

        const driverObj = driver({
            showButtons: ['next', 'previous'],
            steps,
            stagePadding: 0,
            popoverClass: 'driverjs-megatrader-theme',
            overlayOpacity: 0,
            onPopoverRender: (popover: PopoverDOM, {config, state}: { config: DriverConfig; state: DriverState }) => {
                const currentElementID = steps[state.activeIndex || 0].element?.toString().replace('#', '');
                if (!currentElementID) {
                    return;
                }

                console.info('currentElementID', currentElementID);
                console.info('config', config);
                console.info('state', state);

                const skipButton = document.createElement('div');
                skipButton.innerHTML = customSkipButton();
                skipButton.classList.add('btn-dark-link')
                popover.footerButtons.querySelector('.driver-popover-prev-btn')?.after(skipButton);

                skipButton.addEventListener('click', () => {
                    driverObj.destroy();
                });

                const nextButton = popover.footerButtons.querySelector('.driver-popover-next-btn');
                if (nextButton) {
                    nextButton.innerHTML = customNextButton();
                    nextButton.addEventListener('click', (e) => {
                        console.info('Next clicked', e);
                        driverObj.moveNext();
                    });
                }

                // Botón "Previous" personalizado
                const prevButton = popover.footerButtons.querySelector('.driver-popover-prev-btn');
                if (prevButton) {
                    prevButton.innerHTML = customPrevButton();
                    prevButton.addEventListener('click', (e) => {
                        console.info('Previous clicked', e);
                        driverObj.movePrevious();
                    });
                }

                steps.forEach(step => {
                    const elementId = step.element?.toString().replace('#', '');

                    if (!elementId || elementId === currentElementID) {
                        return;
                    }

                    const element = document.getElementById(elementId) as HTMLDivElement || null;

                    if (!element) {
                        return;
                    }

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

                    if (elementId === 'request-withdrawal-button') {
                        element.classList.add('without-bg-card-onboarding')
                        return;
                    }

                    if (elementId === 'available-payment-methods') {
                        element?.parentElement?.parentElement?.classList.add('bg-card-onboarding')
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
                    }
                });

                if (currentElementID) {
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

                        if (elementId === 'available-payment-methods') {
                            element.parentElement?.parentElement?.classList.remove('bg-card-onboarding', 'introjs-relativePosition');
                        }

                        if (elementId === 'request-withdrawal-button') {
                            element.parentElement?.classList.remove('bg-card-onboarding', 'introjs-relativePosition');
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
            },
            onHighlightStarted: (element: Element | undefined, step: DriveStep) => {
                console.info('onHighlightStarted.step', step);
                if (!element) {
                    return;
                }
                // Aquí puedes personalizar el recuadro de resaltado
                // element.style.border = '2px solid red';  // Estilo del borde
                // element.style.zIndex = '9999';  // Asegurarse de que se muestra por encima de otros elementos

                // Si deseas mover el recuadro al contenedor superior
                // const parent = element.parentElement;
                // if (parent) {
                //     parent.style.position = 'relative';
                //     parent.style.border = '2px solid blue';  // Estilo del borde en el contenedor superior
                // }
            },
            onPrevClick: () => {
                driverObj.movePrevious();
            },
            onNextClick: () => {
                driverObj.moveNext();
            },
        });

        driverObj.drive();
    }, [currentPath]);

    return null;
}

function getStepsForPath(path: string) {
    const stepsMap: Record<string, DriveStep[]> = {
        '/account-overview': [
            {
                element: '#manage-subscription',
                popover: {
                    title: 'Top Navigation',
                    description:
                        'Manage and switch between accounts, subscriptions, and challenges easily. Reset your challenge or create a new account when needed.',
                    align: 'center',
                    side: 'bottom',
                },
            },
            {
                element: '#platform-access',
                popover: {
                    title: 'Platform Access',
                    description:
                        'Displays the logo of your selected trading platform. View and copy your login credentials for quick access.',
                    align: 'center',
                    side: 'bottom',
                },
            },
            {
                element: '#account-overview',
                popover: {
                    title: 'Account Overview',
                    description:
                        'Monitor your balance, profit, and trading days. Stay within risk limits by tracking your loss limit and current equity.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#challenge-payout-objectives',
                popover: {
                    title: 'Challenge & Payout Objectives',
                    description:
                        'Track your progress toward profit targets, trading day requirements, and consistency rules. Stay updated on payout eligibility.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#rules-compliance',
                popover: {
                    title: 'Rules & Compliance',
                    description:
                        'Keep your account above the required balance and follow risk management guidelines to stay eligible.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#balance-graph',
                popover: {
                    title: 'Balance Graph',
                    description:
                        'Visualize your account balance over time, track profit targets, and ensure you stay above the minimum balance requirement.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#market-performance-tabs',
                popover: {
                    title: 'Market Performance Tabs',
                    description:
                        'Filter and analyze your trading performance based on different instruments like E-mini S&P 500, NASDAQ 100, and more.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#profitability-metrics',
                popover: {
                    title: 'Profitability Metrics',
                    description:
                        'Check average profit/loss per trade, win percentage, and risk-to-reward ratio to optimize your trading strategy.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#daily-journal',
                popover: {
                    title: 'Daily Journal',
                    description:
                        'Log daily trade performance, emotions, and plan adherence. Use the journal pop-up to reflect on your trading day.',
                    align: 'center',
                    side: 'top',
                },
            },
        ],
        '/affiliates': [
            {
                element: '#affiliate-summary',
                popover: {
                    title: 'Affiliate Summary',
                    description:
                        'Track your total earnings, active referrals, and sales performance. Stay updated on your commission progress and referral activity.',
                    align: 'center',
                    side: 'bottom',
                },
            },
            {
                element: '#available-payment-methods',
                popover: {
                    title: 'Available Payment Methods',
                    description:
                        'View all supported withdrawal methods, including bank transfers and crypto, and select the best option for your needs.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#request-withdrawal-button',
                popover: {
                    title: 'Request Withdrawal Button',
                    description:
                        'Click the button to submit a withdrawal request instantly when you meet the eligibility requirements for payouts.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#referral-program',
                popover: {
                    title: 'Referral Program',
                    description:
                        'Earn rewards by inviting friends. Send invitations, generate commissions from sign-ups, and use your earnings to trade for free.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#invite-your-friends',
                popover: {
                    title: 'Invite Your Friends',
                    description:
                        'Send referral invites via email or copy your unique referral link to share on social media or directly with others.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#performance-analysis',
                popover: {
                    title: 'Performance Overview',
                    description:
                        'Monitor your weekly visits and conversions to identify trends, improve engagement, and optimize your strategy.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#traffic-conversion-table',
                popover: {
                    title: 'Traffic & Conversion Table',
                    description:
                        'Monitor referral traffic, source URLs, and conversion rates to track visitor engagement and successful sign-ups.',
                    align: 'center',
                    side: 'bottom',
                },
            },
        ],
        '/payouts': [
            {
                element: '#payout-summary',
                popover: {
                    title: 'Payout Summary',
                    description:
                        'Track your withdrawable profit, total earnings, profit share percentage, and next payout date to plan your withdrawals efficiently.',
                    align: 'center',
                    side: 'bottom',
                },
            },
            {
                element: '#available-payment-methods',
                popover: {
                    title: 'Available Payment Methods',
                    description:
                        'View all supported withdrawal methods, including bank transfers and crypto, and select the best option for your needs.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#request-withdrawal-button',
                popover: {
                    title: 'Request Withdrawal Button',
                    description:
                        'Click the button to submit a withdrawal request instantly when you meet the eligibility requirements for payouts.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#income-tracker',
                popover: {
                    title: 'Income Tracker',
                    description:
                        'Analyze your earnings over time, track daily income trends, and compare weekly growth to optimize your financial performance.',
                    align: 'center',
                    side: 'top',
                },
            },
            {
                element: '#traffic-conversion-table',
                popover: {
                    title: 'Payouts Overview',
                    description:
                        'Track the status of your payouts, view approved, pending, and rejected transactions, and manage payment methods efficiently.',
                    align: 'center',
                    side: 'top',
                },
            },
        ],
    };

    return stepsMap[path] || [];
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

