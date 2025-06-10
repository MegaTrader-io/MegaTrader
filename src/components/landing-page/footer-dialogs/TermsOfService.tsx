'use client';

import React, {useState, useRef, useEffect} from 'react';
import clsx from "clsx";
import Select from "@/components/Select";

interface TabOption {
    id: string,
    title: string
}

const ITEMS: TabOption[] = [
    {"title": "Introduction", "id": "introduction"},
    {"title": "Eligibility", "id": "eligibility"},
    {"title": "Nature of Services", "id": "nature-of-services"},
    {"title": "Account Registration and Security", "id": "account-registration-and-security"},
    {"title": "Evaluation and Funded Programs", "id": "evaluation-and-funded-programs"},
    {"title": "Fees, Payments, and Refunds", "id": "fees-payments-and-refunds"},
    {"title": "Compliance and Prohibited Conduct", "id": "compliance-and-prohibited-conduct"},
    {"title": "Intellectual Property", "id": "intellectual-property"},
    {"title": "Disclaimers", "id": "disclaimers"},
    {"title": "Limitation of Liability", "id": "limitation-of-liability"},
    {"title": "Account Suspension or Termination", "id": "account-suspension-or-termination"},
    {"title": "Changes to Terms", "id": "changes-to-terms"},
    {"title": "Governing Law and Dispute Resolution", "id": "governing-law-and-dispute-resolution"},
    {"title": "Restricted Countries and Regions", "id": "restricted-countries-and-regions"}
];

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
        <div className='md:grid md:grid-cols-[300px_32px_1fr] my-1'>
            <div className="h-full">
                <div className="space-y-4 hidden sticky top-1 md:block">
                    {ITEMS.map((item, index) => (
                        <button
                            key={index}
                            onClick={() => changeTab(item)}
                            className={clsx('w-full text-left tracking-tight leading-normal font-medium relative', [
                                tab.id === item.id ?
                                    'text-[#ffd78a] scroll-bar' : 'text-stone-400'
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
                <div className="space-y-4" id='introduction'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Introduction</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        These Terms of Service (&#34;Terms&#34;) govern your access to and use of the MegaTrader
                        platform
                        and
                        associated services (the “Services”). By using MegaTrader, you agree to be legally bound by
                        these
                        Terms, our Privacy Policy, and all applicable laws and regulations.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader offers access to a simulated proprietary trading environment where users can evaluate
                        their trading abilities in a risk-free setting. These Terms serve to define the rights,
                        responsibilities, and limitations of users engaging with our platform, and to ensure a safe,
                        compliant, and fair trading ecosystem for all participants.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Please read these Terms carefully before using our Services. If you do not agree to these Terms,
                        you
                        may not access or use the MegaTrader platform.
                    </p>
                </div>

                <div className="space-y-4" id='eligibility'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Eligibility</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">You
                        must meet the following criteria to use MegaTrader:</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Be
                        at least 18 years old or of legal majority age in your country of residence.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Possess
                        the legal capacity to enter into contracts and be legally bound.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Not
                        be a resident or national of a country or region listed in our Restricted Countries section.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Not
                        be previously banned or terminated from our platform for breach of policies.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader reserves the right to carry out identity verification (KYC), eligibility checks, and
                        deny
                        access to any user who fails to meet the required standards. Additional documentation may be
                        requested to confirm identity, address, or source of funds as necessary to comply with
                        compliance
                        obligations.
                    </p>
                </div>

                <div className="space-y-4" id='nature-of-services'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Nature
                        of Services</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader provides access to a simulated trading platform for educational and evaluation
                        purposes
                        only. We do not provide brokerage services or access to live capital markets. All trading is
                        conducted using demo accounts with virtual capital, replicating real-time market data and
                        conditions.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Our
                        primary services include:</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Evaluation
                        Challenges: Paid programs to assess a user’s trading skill, discipline, and strategy.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Simulated
                        Funded Accounts: Provided to users who meet the evaluation criteria, allowing continued
                        trading with payout potential.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Performance-Based
                        Payouts: Reward structures for qualified users based on their performance and
                        adherence to platform rules.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        The platform is designed to simulate professional trading without risking real money. However,
                        successful simulated performance does not guarantee real-world trading success.
                    </p>
                </div>

                <div className="space-y-4" id='account-registration-and-security'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Account
                        Registration and Security</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        To access MegaTrader’s services, users must create a registered account by submitting accurate,
                        complete information including a valid email address and secure password. You agree to:
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Maintain
                        the confidentiality of your login credentials.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Ensure
                        that all submitted information remains current and truthful.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Take
                        full responsibility for all activities occurring under your account.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Sharing your account with others, registering with false information, or using another
                        individual’s
                        credentials is strictly prohibited and may lead to permanent account suspension.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader may suspend, investigate, or terminate accounts where unauthorized access, fraudulent
                        registration, or suspicious activity is detected. We encourage users to use strong passwords and
                        enable two-factor authentication for enhanced security.
                    </p>
                </div>

                <div className="space-y-4" id='evaluation-and-funded-programs'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Evaluation
                        and Funded Programs</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader’s trading evaluations are structured challenges meant to assess trading performance
                        under
                        defined rules. Each program comes with its own set of metrics and conditions:
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Profit
                        Targets</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Maximum
                        Daily and Overall Drawdowns</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Minimum
                        Trading Days</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Risk
                        Management Expectations</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Participants who pass the evaluation gain access to a Simulated Funded Account, which allows
                        them to
                        continue trading under similar parameters and request performance-based payouts.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        All evaluations are conducted in a simulated environment. Any breach of the stated rules,
                        manipulation of the demo system, or use of unauthorized strategies may result in
                        disqualification.
                        MegaTrader reserves the right to revoke access to funded accounts or suspend payouts where abuse
                        or
                        policy violation is suspected.
                    </p>
                </div>

                <div className="space-y-4" id='fees-payments-and-refunds'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Fees,
                        Payments, and Refunds</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Users
                        are required to pay applicable fees to access certain features or services, including:</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Evaluation
                        plan entry fees</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Account
                        reset and reactivation fees</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Add-on
                        services (e.g., analytics tools or data feeds)</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        All payment terms are displayed at the time of purchase and must be accepted before access is
                        granted. Fees are non-refundable unless otherwise stated or where required by law.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader accepts payments via various channels, including debit/credit card, cryptocurrency,
                        and
                        RiseWorks. Users are solely responsible for ensuring successful transaction completion.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">For
                        qualified users, performance-based payouts may be requested. These are:</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Subject
                        to internal review for compliance with trading rules.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Issued
                        through RiseWorks, Bitcoin, or Ethereum.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Dependent
                        on verification of user identity and payment preferences.</p>
                </div>

                <div className="space-y-4" id='compliance-and-prohibited-conduct'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Compliance
                        and Prohibited Conduct</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Users
                        agree to use the MegaTrader platform ethically and in compliance with all applicable laws. The
                        following activities are strictly prohibited:</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Use
                        of automated bots, scripts, or AI unless explicitly permitted.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Exploitation
                        of demo environment inconsistencies (e.g., latency arbitrage).</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Sharing
                        or reselling of user accounts.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Attempting
                        to bypass rules or falsely simulate legitimate trading.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Providing
                        false information or misrepresenting geographic location.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader complies with applicable anti-money laundering (AML) and know-your-customer (KYC)
                        laws.
                        We reserve the right to request documentation or suspend accounts under review for suspicious
                        activity.
                    </p>
                </div>

                <div className="space-y-4" id='intellectual-property'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Intellectual
                        Property</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        All components of the MegaTrader platform are the exclusive property of MegaTrader Holdings LLC
                        or
                        its licensors, including but not limited to:
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Branding
                        and logos</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Website
                        content and platform design</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Source
                        code, databases, and proprietary tools</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">You
                        may not:</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Reproduce,
                        republish, or modify platform materials.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Use
                        MegaTrader’s brand or assets without written authorization.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Develop
                        competing services based on MegaTrader’s intellectual assets.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Violation
                        of these rights may result in legal action and termination of access.</p>
                </div>

                <div className="space-y-4" id='disclaimers'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Disclaimers</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader provides its services &#34;as is&#34; without warranties of any kind. We do not
                        guarantee:
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Continuous,
                        uninterrupted platform availability</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Accuracy
                        or completeness of market data</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Profits
                        or payouts to any user</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">That
                        simulation reflects live-market behavior</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We expressly disclaim any implied warranties including merchantability or fitness for a
                        particular
                        purpose. Users engage with the platform at their own risk and are advised to approach simulated
                        trading with the same caution and discipline as real trading.
                    </p>
                </div>

                <div className="space-y-4" id='limitation-of-liability'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Limitation
                        of Liability</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        To the maximum extent permitted by law, MegaTrader is not liable for:
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Losses
                        arising from simulated trades</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Delayed
                        or missing payouts</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Account
                        access issues due to user negligence</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Suspension
                        or termination resulting from rule violations</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Our total liability is limited to the amount you paid us within the 90-day period prior to any
                        incident. This limitation applies regardless of the legal theory or remedy sought.
                    </p>
                </div>

                <div className="space-y-4" id='account-suspension-or-termination'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Account
                        Suspension or Termination</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">MegaTrader
                        reserves the right to suspend or permanently close accounts that violate these Terms,
                        platform rules, or applicable law.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Grounds
                        for suspension include:</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Use
                        of unauthorized tools or strategies</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Fraudulent
                        behavior</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Multiple
                        or shared accounts</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Regulatory
                        concerns or compliance flags</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Termination
                        results in:</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Immediate
                        loss of access to the platform</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Cancellation
                        of payouts</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Forfeiture
                        of any active simulated accounts</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Appeals may be submitted to <a href='mailto:support@megatrader.io'
                                                       className='text-blue-600 underline'>support@megatrader.io</a> but
                        reinstatement is not guaranteed.
                    </p>
                </div>

                <div className="space-y-4" id='changes-to-terms'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Changes
                        to Terms</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We may update these Terms periodically to reflect operational changes, legal requirements, or
                        feature updates. When changes are made:
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">The
                        revised Terms will be posted on our website with the new effective date.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">We
                        may notify users via email or in-platform alerts.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        By continuing to use the platform after changes take effect, you agree to the updated Terms. If
                        you
                        disagree with the changes, you must cease use of the Services immediately.
                    </p>
                </div>

                <div className="space-y-4" id='governing-law-and-dispute-resolution'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Governing
                        Law and Dispute Resolution</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        These Terms shall be governed by and interpreted under the laws of the State of [Insert State],
                        excluding its conflict of law provisions.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Dispute
                        resolution procedures:</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Users
                        must first contact MegaTrader support to attempt informal resolution.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">If
                        unresolved, disputes will be submitted to confidential binding arbitration in [Insert
                        Location].</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Arbitration
                        will be conducted in English under [Insert Arbitration Rules] and judgment may be entered
                        in any court of competent jurisdiction.</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">You
                        waive any right to participate in class-action litigation or jury trials.</p>
                </div>

                <div className="space-y-4" id='restricted-countries-and-regions'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Restricted
                        Countries and Regions</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">Due
                        to compliance restrictions, MegaTrader does not offer services to users in the following
                        jurisdictions:</p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Restricted Countries:<br/>
                        Afghanistan | Belarus | Burundi | Central African Republic | Congo (DRC) | Cuba | Eritrea | Iran
                        |
                        Iraq | Lebanon | Libya | Mali | Myanmar (Burma) | Nicaragua | North Korea | Russia | Somalia |
                        South
                        Sudan | Sudan | Syria | Venezuela | Yemen | Zimbabwe
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Restricted Regions/Territories:<br/>
                        Crimea, Donetsk, and Luhansk regions of Ukraine<br/>
                        Any area subject to international sanctions
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Restricted U.S. States (if applicable):<br/>
                        New York | Connecticut | Hawaii | New Hampshire (subject to review)
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader reserves the right to update this list at any time for legal or operational reasons.
                    </p>
                </div>
            </div>
        </div>
    );
}

export default TermsOfService;
