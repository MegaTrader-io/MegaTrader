'use client';

import React, {useState, useRef, useEffect} from 'react';
import Select from "@/components/Select";

interface TabOption {
    id: string,
    title: string
}

const ITEMS: TabOption[] = [
    {
        "title": "Nature of MegaTrader Services",
        "id": "nature-of-megatrader-services"
    },
    {
        "title": "Limitations of Simulated Results",
        "id": "limitations-of-simulated-results"
    },
    {
        "title": "Conditions for Earnings and Payouts",
        "id": "conditions-for-earnings-and-payouts"
    },
    {
        "title": "Disclaimer on Financial Advice",
        "id": "disclaimer-on-financial-advice"
    },
    {
        "title": "Legal and Regulatory Status",
        "id": "legal-and-regulatory-status"
    },
    {
        "title": "Risk Considerations",
        "id": "risk-considerations"
    },
    {
        "title": "Participation Terms and Disclaimers",
        "id": "participation-terms-and-disclaimers"
    },
    {
        "title": "Use of User Results and Publicity",
        "id": "use-of-user-results-and-publicity"
    },
    {
        "title": "No Affiliation with Exchanges or Brokers",
        "id": "no-affiliation-with-exchanges-or-brokers"
    },
    {
        "title": "Restricted Jurisdictions",
        "id": "restricted-jurisdictions"
    },
    {
        "title": "Payout Guarantee Limitations",
        "id": "payout-guarantee-limitations"
    },
    {
        "title": "Conversion of Simulated Accounts",
        "id": "conversion-of-simulated-accounts"
    },
    {
        "title": "Reporting Concerns or Inaccuracies",
        "id": "reporting-concerns-or-inaccuracies"
    }
]

function TermsOfService() {
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
        <div className='md:grid my-1'>
            <div className="block md:hidden">
                <Select value={tab.id} onChange={onChange}>
                    <option value="0" disabled={true}>Table of contents</option>
                    {ITEMS.map((item, index) => (
                        <option key={index} value={item.id}>{item.title}</option>
                    ))}
                </Select>
            </div>
            <div className="flex justify-center">
                <div
                    className="sm:border-r-2 sm:border-r-[#404040] mb-8 w-full h-full md:w-0 md:my-0 "></div>
            </div>
            <div className="text-white space-y-12 lg:mr-4">
                <div className="space-y-4">
                    <h3 className="self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        MegaTrader Disclosure Policy
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        This Disclosure Policy outlines important information regarding the nature of MegaTrader’s
                        services, limitations of our simulated trading environment, and disclosures required under
                        applicable laws and internal operating principles. By using MegaTrader, you agree that you have
                        read, understood, and accepted the disclosures outlined below.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="nature-of-megatrader-services"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Nature of MegaTrader Services
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader provides a simulated futures trading platform intended solely for educational,
                        skill-development, and entertainment purposes. It is not a live brokerage or trading venue and
                        does not connect to any real financial exchanges or execution venues. Users trade using
                        simulated funds within a closed-loop environment designed to replicate market-like conditions
                        for evaluation purposes.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Key service characteristics include:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Simulation-Only Environment</span>: All
                                trading takes place within a simulated environment using virtual capital and market
                                data. No real trades are executed in any live market.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Non-Investment Functionality</span>:
                                MegaTrader does not offer investment services or act as a broker, advisor, or portfolio
                                manager. It does not engage in fiduciary activities.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Educational Orientation</span>: The primary
                                intent is to provide users with a platform where they can build confidence, discipline,
                                and performance habits by simulating real-world market scenarios.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">Performance-Based
                                Incentives</span>: While some users may become eligible for performance-based rewards
                                (e.g.,
                                payouts), this is not guaranteed and is conditional upon meeting rigorous criteria.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Participation in MegaTrader should be approached as an educational tool rather than a source of
                        investment return. Any rewards earned within the platform are based solely on simulated outcomes
                        and compliance with platform rules. Users should not expect their account activity to result in
                        ownership of actual securities or positions.
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                MegaTrader is not a broker-dealer, financial advisor, or investment platform.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We emphasize that participation in simulations does not equate to real-market exposure, and no
                        real money is deposited into or traded from live markets.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="limitations-of-simulated-results"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Limitations of Simulated Results
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        No. While MegaTrader is designed to closely mimic the behavior of real futures markets, results
                        achieved in a simulated environment should never be interpreted as accurate predictions of live
                        trading outcomes. Numerous variables affect real-market trading that are either minimized or
                        excluded in simulations.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Differences include:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Emotional Detachment</span>: Simulated
                                trading lacks the psychological pressure that comes with risking real money, which can
                                greatly affect decision-making.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">No Real Execution Costs</span>:
                                Slippage, partial fills, and liquidity limitations are either simplified or absent in
                                the simulation.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Unrealistic Consistency</span>: Users may
                                develop habits or take risks in simulations that they would not attempt in live trading
                                due to fear, uncertainty, or capital exposure.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">No Market Impact</span>: In a simulation, large
                                positions do not affect price movement or book depth, unlike in real-world trading where
                                order flow dynamics can change execution quality.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader urges users to view the simulation as an exercise in building good habits rather than
                        measuring investable skills. While success on the platform can be a confidence booster, it
                        should not replace comprehensive risk training or professional financial education. Users are
                        strongly discouraged from making real-money trading decisions based solely on simulated success.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Performance in a controlled environment should not be used to make financial or investment
                        decisions. Simulated success does not imply or guarantee success in real trading environments.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="conditions-for-earnings-and-payouts"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Conditions for Earnings and Payouts
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader offers real monetary rewards in certain cases—but only under specific conditions.
                        Payouts and other incentives are provided to users who complete simulation-based evaluations
                        according to established platform rules. Not all users will earn rewards, and there are no
                        guarantees that trading performance will result in a payout.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Key points to understand:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Conditional Eligibility</span>: All real
                                rewards are subject to completion of verification procedures, rule compliance, and
                                successful evaluation within the simulation.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Performance-Based Structure</span>:
                                Payouts are based on measurable performance factors including strategy consistency, risk
                                discipline, and adherence to account-specific rules.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Manual Review Required</span>: All payout
                                requests are reviewed by MegaTrader{'\\'}s compliance team before disbursement. We may
                                request additional documentation or perform audits prior to releasing funds.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">No Passive Earnings</span>: Users are not
                                compensated for time, participation, or platform usage alone. Rewards must be earned and
                                are not fixed or recurring.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader maintains full discretion over reward eligibility and retains the right to deny
                        payouts to users who violate terms, exhibit manipulative behavior, or fail verification. While
                        payouts may carry real monetary value, they are derived from performance within a simulated
                        system—not from returns on real capital.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="disclaimer-on-financial-advice"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Disclaimer on Financial Advice
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        No. MegaTrader does not offer or imply the delivery of financial advice, portfolio management
                        services, or investment recommendations. All content and tools available on our platform are
                        provided strictly for informational and educational purposes. Any analytics, performance
                        tracking, strategy feedback, or tutorials are meant to help users better understand the
                        simulated environment—not guide actual investment decisions.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Important distinctions:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">No Suitability Analysis</span>: We do not
                                assess your financial background, trading knowledge, or risk tolerance to recommend any
                                strategy or action.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">No Fiduciary Relationship</span>:
                                There is no legal, fiduciary, or advisory relationship created between you and
                                MegaTrader by your use of the simulation tools or any associated material.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">General Information Only</span>: Articles,
                                tutorials, dashboards, and account statistics are general in nature and not tailored to
                                individual needs or goals.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        You are solely responsible for interpreting your simulated performance and deciding how (or
                        whether) to act on any insights gained through the platform. We strongly encourage users to
                        consult a licensed financial advisor before making any real-money decisions based on their
                        experiences within MegaTrader. The platform’s tools are not substitutes for formal financial
                        planning, investment research, or regulated trading advice.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="legal-and-regulatory-status"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Legal and Regulatory Status
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader operates entirely outside of regulated financial markets and does not fall under the
                        supervision of securities or commodities authorities such as the SEC, CFTC, FINRA, or NFA. Our
                        services are designed for simulation, not for trading or investing, and do not constitute
                        regulated financial activity under current U.S. or international law.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Clarifications:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">No Regulatory Licensure</span>: MegaTrader is
                                not licensed as a broker, advisor, or investment dealer and does not offer execution,
                                clearing, or custodial services.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Not a Financial Institution</span>:
                                We do not accept or manage customer funds for investment purposes, nor do we provide
                                margin accounts or clearing functions.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Voluntary Participation</span>: Your use of
                                MegaTrader is entirely voluntary and does not form a contract or investor relationship
                                with our company.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">No Securities Offered</span>: No shares,
                                tokens, or investment instruments are offered or sold through the platform.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        By accessing our services, you acknowledge that MegaTrader is a private platform for performance
                        evaluation and educational simulation. Users are responsible for understanding that our services
                        do not confer legal standing as a trading firm, asset manager, or capital provider. We advise
                        against confusing MegaTrader’s functionality with any regulated investment activity.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="risk-considerations"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Risk Considerations
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Yes. Although MegaTrader operates in a simulation-only environment, users are still exposed to
                        psychological, behavioral, and emotional risks that can affect their trading mindset. The
                        absence of financial loss does not eliminate risk—particularly the risk of developing false
                        confidence or unsustainable trading habits.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Consider the following:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Psychological Exposure</span>: Engaging in
                                simulations can foster overconfidence if users assume that simulated success guarantees
                                real-world profitability.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Risk Misjudgment</span>:
                                Simulated environments often lack consequences for poor risk management, which may lead
                                users to ignore key principles like drawdown control or position sizing.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Time Investment</span>: Users may devote
                                significant time and effort to simulation without realistic expectations or
                                understanding of live market complexities.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Spillover Effects</span>: Behaviors formed in
                                a simulation—such as overtrading or ignoring stop-losses—can carry over into real-money
                                trading and result in losses.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader urges users to treat the simulation seriously while recognizing its limitations.
                        Always supplement your experience with additional education and maintain a conservative outlook
                        if transitioning to real-world market participation. Trading involves substantial risk, and
                        there are no guarantees of success in any environment.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="participation-terms-and-disclaimers"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Participation Terms and Disclaimers
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        No. Your use of the MegaTrader platform does not create any contractual rights, obligations, or
                        guarantees of future outcomes. Participation is granted on a discretionary basis and can be
                        modified, revoked, or limited at any time without notice.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Clarifications:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">No Employment Relationship</span>: Engaging
                                with MegaTrader does not create an offer of employment, internship, or partnership. You
                                are not entitled to compensation unless formally awarded through a verified achievement.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Non-Binding Terms</span>:
                                Platform features, including payout programs and prize mechanisms, are subject to change
                                and are not guaranteed in perpetuity.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Discretionary Rewards</span>: All awards,
                                including potential payouts or recognition, are conditional upon platform-defined
                                criteria and eligibility checks.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We retain the right to amend rules, disqualify participants, or suspend features at our
                        discretion. By participating, you agree to abide by evolving platform policies without asserting
                        entitlement to fixed outcomes. MegaTrader is a private environment for skills evaluation and
                        does not offer enforceable contractual benefits to participants.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="use-of-user-results-and-publicity"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Use of User Results and Publicity
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Yes, MegaTrader may use user results and simulation achievements for marketing purposes, but
                        only under specific circumstances and always in accordance with user privacy rights. Showcasing
                        participant milestones is part of our effort to highlight platform potential and encourage a
                        competitive, transparent environment.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Key disclosures include:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Leaderboard Participation</span>:
                                Top-performing users may appear on public or internal leaderboards as part of platform
                                gamification features. These are updated regularly and reflect current simulated
                                metrics.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Testimonial Features</span>:
                                We may request voluntary testimonials from successful users to share experiences or
                                highlight educational value. All published testimonials require consent.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Social Media Showcases</span>: MegaTrader may
                                highlight notable trading streaks, simulation completion milestones, or other user
                                events on social platforms with anonymization or user permission.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Content Creation</span>: From time to time,
                                anonymized case studies or performance breakdowns may be used for internal content, blog
                                articles, or presentations to help educate new users.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        You are never obligated to participate in marketing initiatives, and we will never misrepresent
                        your performance or use your image without explicit permission where legally required.
                        Participation in simulations does not imply a public endorsement or professional competency. You
                        may opt out of any publicity efforts by contacting <a href="mailto:support@megatrader.io"
                                                                              className="text-[#ffb54d]">support@megatrader.io</a>.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="no-affiliation-with-exchanges-or-brokers"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        No Affiliation with Exchanges or Brokers
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        No. MegaTrader is an independent simulation provider and is not affiliated, endorsed, or
                        partnered with any regulated exchange, clearinghouse, or brokerage. Our systems do not interact
                        with, connect to, or mirror any real execution infrastructure such as CME Group, NYSE, NASDAQ,
                        or other global venues.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Additional clarifications:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">No Routing or Clearing</span>:
                                We do not route simulated orders through live brokers, and no clearing or settlement
                                occurs.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">No Data Licensing</span>:
                                Any live-like pricing visible on the platform is either delayed or approximated for
                                educational display. MegaTrader does not purchase or distribute real-time market data.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Educational Emulation Only</span>: While the
                                simulation may mimic patterns seen in actual market conditions, the price feeds, fills,
                                and liquidity responses are not based on live exchange APIs.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We emphasize that participation on our platform does not grant access to real-market
                        infrastructure. Any references to market behavior are illustrative only and are meant to serve
                        as a learning tool—not a representation of actual execution conditions. Users are responsible
                        for understanding that MegaTrader operates in an entirely closed-loop training environment.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="restricted-jurisdictions"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Restricted Jurisdictions
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader complies with international sanctions and U.S. trade restrictions by limiting access
                        to users from certain high-risk jurisdictions. We proactively restrict access from sanctioned
                        territories and monitor usage to prevent circumvention.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        The following countries are currently restricted:
                    </p>
                    <p className="self-stretch justify-start text-stone-300 text-base font-bold leading-normal">
                        Afghanistan, Central African Republic, Congo (Brazzaville), Congo (Kinshasa), Cuba,
                        Guinea-Bissau, Iran, Iraq, North Korea, Libya, Mali, Russian Federation, Somalia, South Sudan,
                        Sudan, Syria, Yemen, Venezuela
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Key notes:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">No VPN Circumvention</span>:
                                Attempting to access the platform from a prohibited location using VPNs, proxies, or
                                other anonymization tools is a violation of our Terms of Service.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Automated Monitoring</span>:
                                Our systems use geolocation, IP filtering, and account metadata to detect unauthorized
                                access attempts from restricted jurisdictions.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Enforcement Measures</span>: Accounts found
                                to be in violation will be suspended or terminated, and no payout eligibility will be
                                honored.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We enforce these restrictions strictly to comply with legal obligations, protect our
                        infrastructure, and maintain platform integrity. Users must confirm they are accessing the
                        platform from an authorized region and agree not to bypass these controls under any
                        circumstance.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="payout-guarantee-limitations"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Payout Guarantee Limitations
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        No. MegaTrader does not guarantee that any user will receive a payout, bonus, or other form of
                        compensation, regardless of time spent or simulation performance. All payouts are discretionary,
                        conditional, and subject to rigorous review processes to ensure compliance and authenticity.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Important clarifications:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Performance-Dependent</span>:
                                Payouts are awarded only to users who meet clearly defined simulation milestones and
                                risk management criteria.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Manual Review Process</span>:
                                Each payout request is manually reviewed for compliance with platform rules, trading
                                integrity, and identification verification.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Rule Adherence Required</span>: Any breach of
                                rules—even if performance is strong—can disqualify a user from eligibility.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Non-Recurring</span>: A successful payout in
                                one cycle does not entitle a user to future payouts without repeating the full
                                evaluation.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader reserves full discretion to deny, delay, or revoke payout access for any reason,
                        including but not limited to fraud detection, duplicate account activity, or abuse of simulated
                        mechanics. Users should approach simulation as a structured training opportunity—not as a
                        guaranteed income stream.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="conversion-of-simulated-accounts"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Conversion of Simulated Accounts
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        No. MegaTrader does not offer direct account conversions into live trading environments. The
                        platform does not provide brokerage services, and simulation performance does not entitle users
                        to real capital allocation or access to trading accounts connected to live markets.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        However, users may be offered performance-based incentives such as additional evaluations,
                        platform bonuses, or recognition if they meet specific simulation benchmarks. These incentives
                        do not represent or resemble live trading privileges.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Points of clarification:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">No Real Brokerage Transition</span>:
                                We are not affiliated with any brokerages or funding firms that would facilitate such
                                conversions.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Simulation Purposes Only</span>: The platform
                                exists purely for skill assessment, discipline training, and educational analysis.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Potential Offers Are Internal</span>: If
                                additional evaluation paths or opportunities are presented, they remain within the
                                simulated ecosystem.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Any statements referring to “funded opportunities” or advanced access refer to internal
                        simulation tiers or expanded testing features. We do not provide capital or connect users to
                        real trading infrastructure under any circumstance.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="reporting-concerns-or-inaccuracies"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Reporting Concerns or Inaccuracies
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader is committed to transparency and continuous improvement. If you believe any aspect of
                        this Disclosure Policy is unclear, misleading, incomplete, or out of date, we encourage you to
                        contact us. Your feedback helps us enhance the platform and ensure full compliance with evolving
                        user expectations and legal standards.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        You may report issues through the following methods:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Live Chat Support</span>:
                                Available directly through our website or user dashboard. A support agent can assist in
                                escalating concerns to our legal or compliance team.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Email Communication</span>:
                                Submit detailed inquiries to <a href="mailto:support@megatrader.io"
                                                                className="text-[#ffb54d]">support@megatrader.io</a>.
                                Please include your registered
                                email, account ID (if applicable), and a brief description of the issue.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        All reports are reviewed by our compliance team and addressed in the order received. While some
                        updates may require internal review, legal consultation, or policy revision cycles, we aim to
                        respond to all feedback within a reasonable time frame. If a correction is warranted, we will
                        revise the document and update the &quot;Last Updated&quot; date accordingly.
                    </p>
                </div>
            </div>
        </div>
    );
}

export default TermsOfService;
