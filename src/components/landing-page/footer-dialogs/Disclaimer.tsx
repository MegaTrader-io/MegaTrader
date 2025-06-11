'use client';

import React, {useEffect, useRef, useState} from 'react';
import clsx from "clsx";
import Select from "@/components/Select";

interface TabOption {
    id: string,
    title: string
}

const ITEMS: TabOption[] = [
    {id: 'general-information-disclaimer', title: 'General Information Disclaimer'},
    {id: 'educational-purpose-only', title: 'Educational Purpose Only'},
    {id: 'no-investment-advice', title: 'No Investment Advice'},
    {id: 'risk-of-trading', title: 'Risk of Trading'},
    {id: 'hypothetical-performance-warning', title: 'Hypothetical Performance Warning'},
    {id: 'payout-and-compensation-illustration', title: 'Payout and Compensation Illustration'},
    {id: 'user-responsibility-and-conduct', title: 'User Responsibility and Conduct'},
    {id: 'regulatory-status-and-legal-limits', title: 'Regulatory Status and Legal Limits'},
    {id: 'use-of-third-party-services', title: 'Use of Third-Party Services'},
    {id: 'modification-and-policy-updates', title: 'Modification and Policy Updates'},
]

function Disclaimer() {
    const [tab, setTab] = useState<TabOption>(ITEMS[0]);
    const sectionsRef = useRef<Record<string, HTMLElement | null>>({});

    useEffect(() => {
        const observer = new IntersectionObserver(
            entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        const tabSelected = ITEMS.find(t => t.id === id);
                        console.info('eligibility', tabSelected, id)
                        if (tabSelected) {
                            setTab(tabSelected);
                        }
                    }
                });
            },
            {
                rootMargin: '0% 0px -50% 0px',
                threshold: 0
            }
        );

        ITEMS.forEach(item => {
            const el = document.getElementById(item.id);
            if (el) {
                sectionsRef.current[item.id] = el;
                observer.observe(el);
            }
        });

        return () => {
            observer.disconnect();
        };
    }, []);

    function changeTab(tabOption: TabOption) {
        setTab(tabOption);
        const el = sectionsRef.current[tabOption.id];
        if (el) {
            el.scrollIntoView({behavior: 'smooth', block: 'start'});
        }
    }

    function onChange(e: React.ChangeEvent<HTMLSelectElement>) {
        const value = e.target.value;
        const tabSelected = ITEMS.find(tab => tab.id === value);
        if (!tabSelected) {
            return;
        }
        changeTab(tabSelected);
    }

    return (
        <div className='md:grid md:grid-cols-[300px_32px_1fr] my-1'>
            <div className="h-full">
                <div className="space-y-4 hidden sticky top-1 md:block">
                    {ITEMS.map((item, index) => (
                        <button
                            key={index}
                            onClick={() => changeTab(item)}
                            className={clsx('w-full text-left tracking-tight leading-normal font-medium relative', [
                                tab.id === item.id ?
                                    'text-[#ffd78a] font-medium scroll-bar' : 'text-stone-400'
                            ])}>
                            {item.title}
                        </button>
                    ))}
                </div>

                <div className="block md:hidden">
                    <Select value={tab.id} onChange={onChange}>
                        {ITEMS.map((item, index) => (
                            <option key={index} value={item.id}>{item.title}</option>
                        ))}
                    </Select>
                </div>
            </div>
            <div className="flex justify-center">
                <div
                    className="sm:border-r-2 sm:border-r-[#404040] mb-8 w-full h-full md:w-0 md:my-0 "></div>
            </div>
            <div className="text-white space-y-12 lg:mx-4">
                <div className="space-y-4" id='general-information-disclaimer'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        General Information Disclaimer
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader Holdings Inc. (&quot;MegaTrader&quot;) offers simulated trading programs and educational services designed to evaluate users’ trading abilities in a risk-free environment. Our platform does not involve real financial instruments or investment transactions. No part of our website, dashboard, support materials, or communications should be interpreted as financial, investment, tax, or legal advice. While the content we provide is informative and instructional in nature, it is not personalized or tailored to any specific individual, portfolio, or financial situation. We strongly advise users to consult with licensed professionals before making any trading decisions outside of our simulated environment. Accessing our platform in violation of any applicable law, including securities or gambling laws in your jurisdiction, is strictly prohibited.
                    </p>
                </div>
                <div className="space-y-4" id='educational-purpose-only'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Educational Purpose Only</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        All MegaTrader services, products, and programs are intended solely for educational purposes. We do not provide brokerage services or facilitate actual trading in the financial markets. Our evaluation programs and simulations use virtual funds and fictitious trading environments to assess user performance. Any resemblance to real market conditions is coincidental and for training reference only. Participation in our programs should not be seen as a substitute for formal financial education or experience. The use of the term &quot;funded account&quot; refers to a reward simulation based on user performance, not a real-money trading account. No capital is being invested or withdrawn during the simulated evaluation period.
                    </p>
                </div>
                <div className="space-y-4" id='no-investment-advice'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">No Investment Advice</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader does not provide investment, trading, or financial planning advice. Any strategies, examples, or analyses found on our platform are generic and are not recommendations for buying, selling, or holding specific financial instruments. Users should not interpret our educational tools, indicators, or performance dashboards as advice or guarantees of success. We are not licensed under any regulatory authority to offer financial advisory services. Decisions to engage in live trading based on any knowledge or practice gained from MegaTrader are made solely at the user’s discretion and risk. We accept no liability for losses or damages resulting from real-world trading activity.
                    </p>
                </div>
                <div className="space-y-4" id='risk-of-trading'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Risk of Trading</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Trading financial markets carries substantial risk. Although MegaTrader uses virtual accounts with no real financial exposure, simulated success does not guarantee actual profitability. Real trading involves emotional factors, slippage, latency, and liquidity challenges that cannot be perfectly mirrored in a simulated environment. Users who transition to live trading should understand the full risk of loss. MegaTrader disclaims responsibility for losses incurred in real markets, even if simulated strategies appear successful on our platform. You should only trade with capital you can afford to lose and after gaining sufficient experience and education.
                    </p>
                </div>
                <div className="space-y-4" id='hypothetical-performance-warning'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Hypothetical Performance Warning</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        All performance results displayed on the MegaTrader platform are based on hypothetical, back-tested, or simulated trading activity. These outcomes do not reflect actual trading results or client experiences in the real market. Hypothetical results have many inherent limitations, including the inability to predict liquidity conditions, order execution delays, or emotional decision-making. No representation is made that any user will achieve results similar to those shown. Testimonials and promotional content on our site represent individual experiences and are not necessarily representative or predictive of average user outcomes. Reliance on hypothetical results for investment decisions is discouraged.
                    </p>
                </div>
                <div className="space-y-4" id='payout-and-compensation-illustration'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Payout and Compensation Illustration</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Any references to payouts, funded accounts, or profit-sharing percentages shown on the platform are illustrative only. These figures are meant to demonstrate how our simulated reward structure operates and are not promises or guarantees of actual financial gain. Payout eligibility is subject to strict program rules, including compliance with consistency, drawdown, and risk management policies. Failure to meet these standards may disqualify users from receiving any compensation. MegaTrader reserves full discretion to approve, deny, or delay payout requests based on account review, compliance concerns, or suspected misconduct.
                    </p>
                </div>
                <div className="space-y-4" id='user-responsibility-and-conduct'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">User Responsibility and Conduct</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        By using MegaTrader{'\''}s platform, users accept full responsibility for their own actions, decisions, and performance. You agree not to misuse the simulation tools, engage in manipulative practices, or create multiple accounts to bypass program rules. The platform is intended for individual educational use only. Attempts to game the system, automate activity, or exploit simulated conditions may result in disqualification or permanent suspension. Users should maintain a good-faith effort to comply with the platform’s intended purpose and follow all written and posted rules. MegaTrader reserves the right to monitor account activity for compliance and enforce disciplinary actions when needed.
                    </p>
                </div>
                <div className="space-y-4" id='regulatory-status-and-legal-limits'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Regulatory Status and Legal Limits</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader is a U.S.-registered company providing educational and simulation-based services. We are not a registered broker-dealer, futures commission merchant, investment advisor, or financial institution under any state or federal jurisdiction. As such, our platform is not regulated by the SEC, FINRA, CFTC, or other regulatory agencies that govern actual trading. Nothing on the platform constitutes a solicitation to invest in securities, commodities, or derivatives. We make no representation that our services are appropriate or lawful in all jurisdictions. Users are responsible for ensuring their use of the platform complies with their local laws and financial regulations.
                    </p>
                </div>
                <div className="space-y-4" id='use-of-third-party-services'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Use of Third-Party Services</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader partners with third-party vendors to provide identity verification, payment processing, charting, data feeds, and communication tools. While we strive to work only with reputable service providers, we do not control or guarantee the accuracy, performance, or policies of these third parties. Users acknowledge that these vendors may collect and process information independently under their own privacy policies. MegaTrader is not liable for any issues, delays, or disputes arising from third-party services unless explicitly stated. Users should review any external terms and contact the vendor directly for support related to those services.
                    </p>
                </div>
                <div className="space-y-4" id='modification-and-policy-updates'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Modification and Policy Updates</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader may revise this Disclosure Statement at any time to reflect changes in our services, legal obligations, or operational practices. Updated disclosures will be posted on our website with a revised effective date. Continued use of the platform after such updates constitutes acceptance of the revised terms. It is your responsibility to review these disclosures periodically and stay informed of changes. We may also issue separate notices or email communications for significant updates affecting eligibility, payouts, risk policies, or compliance requirements. Archived versions of prior disclosures are available upon request for reference or regulatory documentation.
                    </p>
                </div>
            </div>
        </div>
    );
}

export default Disclaimer;