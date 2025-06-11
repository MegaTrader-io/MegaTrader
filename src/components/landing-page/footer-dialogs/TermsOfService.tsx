'use client';

import React, {useState, useRef, useEffect} from 'react';
import Select from "@/components/Select";

interface TabOption {
    id: string,
    title: string
}

const ITEMS: TabOption[] = [
    {
        "title": "Acceptance of Terms",
        "id": "acceptance-of-terms"
    },
    {
        "title": "Eligibility and Account Registration",
        "id": "eligibility-and-account-registration"
    },
    {
        "title": "Nature of Services",
        "id": "nature-of-services"
    },
    {
        "title": "Account Types and Program Structure",
        "id": "account-types-and-program-structure"
    },
    {
        "title": "Fees, Payments, and Refunds",
        "id": "fees-payments-and-refunds"
    },
    {
        "title": "Taxes and Reporting Obligations",
        "id": "taxes-and-reporting-obligations"
    },
    {
        "title": "Identity Verification and Compliance",
        "id": "identity-verification-and-compliance"
    },
    {
        "title": "Platform Use and Conduct",
        "id": "platform-use-and-conduct"
    },
    {
        "title": "Intellectual Property",
        "id": "intellectual-property"
    },
    {
        "title": "Disclaimer of Warranties",
        "id": "disclaimer-of-warranties"
    },
    {
        "title": "Limitation of Liability",
        "id": "limitation-of-liability"
    },
    {
        "title": "Suspension and Termination",
        "id": "suspension-and-termination"
    },
    {
        "title": "Modifications to Services",
        "id": "modifications-to-services"
    },
    {
        "title": "Governing Law and Dispute Resolution",
        "id": "governing-law-and-dispute-resolution"
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
                <div className="space-y-4" id='acceptance-of-terms'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Acceptance
                        of Terms</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        By accessing or using the services offered by MegaTrader Holdings LLC (“MegaTrader”), you agree
                        to be bound by these Terms of Service and all applicable laws and regulations. These terms
                        govern your use of the MegaTrader platform, including all products, evaluation programs, and
                        related services. If you do not agree with any part of these terms, you should not use the
                        Services. We reserve the right to update or modify these Terms at any time, and continued use of
                        the Services constitutes acceptance of any changes. By accessing the platform or completing a
                        registration, you affirm that you understand and accept these conditions fully and voluntarily.
                        The most recent version of these Terms will always be available on our website for your
                        reference.
                    </p>
                </div>
                <div className="space-y-4" id='eligibility-and-account-registration'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Eligibility
                        and Account Registration</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        To access MegaTrader’s services, users must create a registered account by submitting accurate,
                        complete information including a valid email address and secure password. You agree to maintain
                        the confidentiality of your login credentials, ensure that your submitted information remains
                        current and truthful, and take full responsibility for all activities occurring under your
                        account. Sharing your account with others, registering with false information, or using another
                        individual’s credentials is strictly prohibited and may lead to permanent account suspension.
                        MegaTrader may suspend, investigate, or terminate accounts where unauthorized access, fraudulent
                        registration, or suspicious activity is detected. We encourage users to use strong passwords and
                        enable two-factor authentication for enhanced security. In addition, creating multiple accounts
                        to bypass plan limitations or program restrictions is strictly forbidden.
                    </p>
                </div>
                <div className="space-y-4" id='nature-of-services'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Nature
                        of Services</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader provides a simulated trading environment for educational and evaluation purposes.
                        Users do not engage in actual market trading, and no real capital is used or distributed during
                        the simulation phase. Access to funded reward accounts is conditional and based entirely on
                        performance within the simulation. Our platform is not a brokerage service and does not
                        facilitate the purchase or sale of actual securities, futures, or commodities. Any
                        representation of account funding, payout, or profit sharing is hypothetical and governed by
                        specific program requirements. Participation in any program does not guarantee monetary
                        compensation, and all rewards are subject to compliance with internal rules and eligibility
                        criteria.
                    </p>
                </div>
                <div className="space-y-4" id='account-types-and-program-structure'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Account
                        Types and Program Structure</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader offers multiple evaluation programs, each with its own rules, objectives, and payout
                        structures. Users are responsible for understanding the program terms before participation.
                        Program eligibility may vary by region, and participation may require payment, identity
                        verification, and compliance with daily or monthly trading objectives. Rules regarding minimum
                        trading days, consistency, drawdown limits, and maximum position sizes are strictly enforced and
                        subject to change. Each account type may impose specific restrictions on trading hours,
                        permitted contract sizes, and payout timelines, and failure to adhere to these can result in
                        disqualification or forfeiture of eligibility.
                    </p>
                </div>
                <div className="space-y-4" id='fees-payments-and-refunds'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Fees,
                        Payments, and Refunds</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Some MegaTrader services require payment of non-refundable fees. This includes evaluation
                        entries, resets, add-ons, and platform data access where applicable. Payment is due upon
                        registration, and no refund will be issued unless otherwise stated in writing. Fees may differ
                        based on account type or subscription level. Users are encouraged to review all charges before
                        submitting payment. Third-party platform or exchange fees are not included in MegaTrader
                        pricing. Refunds will not be issued due to performance-based disqualifications or failure to
                        meet evaluation criteria. By purchasing any program, you agree to the full terms, costs, and
                        non-refundable nature of your selected product.
                    </p>
                </div>
                <div className="space-y-4" id='taxes-and-reporting-obligations'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Taxes
                        and Reporting Obligations</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Users are solely responsible for reporting any compensation, payouts, or performance-based
                        earnings received through the MegaTrader platform. We do not provide tax advice and do not
                        withhold taxes unless legally required. Where applicable, U.S. users may receive IRS Form 1099.
                        International users are expected to comply with local tax laws and consult their own tax
                        advisors. MegaTrader may cooperate with lawful tax and regulatory inquiries when required. It is
                        your responsibility to maintain accurate records of your trading performance and payout history
                        for tax reporting purposes. Failure to report earnings or comply with applicable tax regulations
                        may result in penalties from your local authority, not from MegaTrader.
                    </p>
                </div>
                <div className="space-y-4" id='identity-verification-and-compliance'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Identity
                        Verification and Compliance</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        To protect the integrity of our programs and meet legal requirements, MegaTrader may require
                        users to complete identity verification (KYC) and anti-money laundering (AML) screening.
                        Verification may include submission of a government-issued ID, proof of address, and a selfie
                        for facial matching. Failure to complete verification or providing false documentation may
                        result in disqualification from payouts, account suspension, or termination. In some
                        jurisdictions, we may also be required to collect tax identification numbers or additional
                        documents before releasing any funds. Our compliance team reserves the right to request updated
                        documentation or re-verification at any time if suspicious activity is detected.
                    </p>
                </div>
                <div className="space-y-4" id='platform-use-and-conduct'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Platform
                        Use and Conduct</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Users agree to use the MegaTrader platform only for lawful purposes and in accordance with
                        program rules. You must not engage in manipulative trading, exploit latency, use unauthorized
                        software, or otherwise attempt to circumvent program criteria. We reserve the right to suspend,
                        investigate, or permanently ban accounts engaged in prohibited conduct. MegaTrader may monitor
                        activity for quality assurance, policy enforcement, and fraud detection. You must not attempt to
                        access another user’s account or interfere with system operations through the use of bots,
                        scripts, or malicious tools. All use of the Services must reflect good faith participation and
                        adherence to the spirit of a fair and competitive trading environment.
                    </p>
                </div>
                <div className="space-y-4" id='intellectual-property'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Intellectual
                        Property</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        All content, branding, technology, and systems on the MegaTrader platform are the exclusive
                        property of MegaTrader Holdings LLC. Users are granted a limited, non-transferable license to
                        use the platform solely for its intended purpose. Copying, reverse-engineering, redistributing,
                        or modifying any part of the service without prior written consent is strictly prohibited. Any
                        misuse of our intellectual property, including trademarks, graphics, or proprietary software,
                        may result in legal action. Use of MegaTrader’s logo or brand elements in social media or
                        promotional material must receive prior written authorization.
                    </p>
                </div>
                <div className="space-y-4" id='disclaimer-of-warranties'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Disclaimer
                        of Warranties</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader provides its Services “as is” and makes no warranties regarding availability,
                        accuracy, or performance. We do not guarantee that the platform will be free from interruptions,
                        errors, or vulnerabilities. Participation in simulated trading does not guarantee live trading
                        success or future profitability. Users assume all risks associated with the use of the platform.
                        No advice or information obtained from MegaTrader, whether oral or written, shall create any
                        warranty not expressly stated in these Terms. Users should not rely solely on the functionality
                        of the platform or simulation data for financial or trading decisions.
                    </p>
                </div>
                <div className="space-y-4" id='limitation-of-liability'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Limitation
                        of Liability</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Under no circumstances shall MegaTrader be liable for any direct, indirect, incidental,
                        consequential, or punitive damages resulting from your use of, or inability to use, the
                        Services. This includes but is not limited to loss of data, trading losses, opportunity costs,
                        or damage to your equipment. MegaTrader’s liability is limited to the maximum extent permitted
                        by law. In the event of a technical malfunction, data breach, or pricing error, we may take
                        corrective action including but not limited to canceling simulated trades, resetting accounts,
                        or adjusting performance metrics.
                    </p>
                </div>
                <div className="space-y-4" id='suspension-and-termination'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Suspension
                        and Termination</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader reserves the right to suspend or terminate your account at any time, with or without
                        notice, for violations of these Terms, suspicious activity, or for maintenance and operational
                        needs. In cases involving suspected fraud, abuse, or legal risk, we may withhold payouts or take
                        further investigative action. Terminated accounts may lose access to all platform features and
                        historical data. Users may request a review of their case in writing, but reinstatement is not
                        guaranteed. If your account is terminated for cause, you are not entitled to any refund or
                        compensation.
                    </p>
                </div>
                <div className="space-y-4" id='modifications-to-services'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Modifications
                        to Services</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We may update, modify, or discontinue any part of the Services at our sole discretion. This
                        includes features, evaluation rules, platform access, and pricing. While we strive to notify
                        users of significant changes, MegaTrader is not obligated to maintain any specific feature or
                        service indefinitely. Continued use of the platform constitutes acceptance of such changes. You
                        are responsible for reviewing these Terms regularly to remain informed of updates. Archived
                        versions may be made available for your reference upon request.
                    </p>
                </div>
                <div className="space-y-4" id='governing-law-and-dispute-resolution'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">Governing
                        Law and Dispute Resolution</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        These Terms are governed by the laws of the State of \[Insert State], without regard to its
                        conflict of law principles. Any dispute or claim arising from your use of the Services shall be
                        resolved through binding arbitration in accordance with the rules of the American Arbitration
                        Association. By agreeing to these Terms, you waive any right to participate in class action
                        lawsuits or jury trials to the extent permitted by law. Arbitration shall take place in a
                        mutually agreed location or remotely when applicable. If any part of these Terms is deemed
                        unenforceable, the remaining provisions will remain in full effect.
                    </p>
                </div>
            </div>
        </div>
    );
}

export default TermsOfService;
