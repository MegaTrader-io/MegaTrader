'use client';

import React, {useState, useRef, useEffect} from 'react';
import Select from "@/components/Select";

interface TabOption {
    id: string,
    title: string
}

const ITEMS: TabOption[] = [
    {
        "title": "Information We Collect",
        "id": "information-we-collect"
    },
    {
        "title": "Use of Collected Data",
        "id": "use-of-collected-data"
    },
    {
        "title": "Data Sharing With Third Parties",
        "id": "data-sharing-with-third-parties"
    },
    {
        "title": "Data Storage and Protection",
        "id": "data-storage-and-protection"
    },
    {
        "title": "User Control and Data Rights",
        "id": "user-control-and-data-rights"
    },
    {
        "title": "Cookies and Tracking Technologies",
        "id": "cookies-and-tracking-technologies"
    },
    {
        "title": "Handling of Children’s Data",
        "id": "handling-of-children-data"
    },
    {
        "title": "Data Retention Practices",
        "id": "data-retention-practices"
    },
    {
        "title": "Policy Updates and Revisions",
        "id": "policy-updates-and-revisions"
    },
    {
        "title": "Contacting MegaTrader Regarding Privacy",
        "id": "contacting-megatrader-regarding-privacy"
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
                        MegaTrader Privacy Policy
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        This Privacy Policy explains how MegaTrader collects, uses, stores, and protects your personal
                        data when you access our website, use our services, or engage with our platform in any way. We
                        are committed to protecting your privacy and handling your data in compliance with applicable
                        data protection laws.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="information-we-collect"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Information We Collect
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader collects various types of personal and technical information to operate the platform
                        effectively and ensure compliance with applicable regulations. This includes information
                        provided directly by users, data collected automatically through your interaction with the
                        platform, and limited data from third-party integrations.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Collection practices are designed to
                        balance operational needs with privacy and user transparency.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We may collect:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Identity Information</span>: Your full name,
                                email address, country of residence, and username or password credentials.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Technical Information</span>:
                                IP addresses, device identifiers, browser type, operating system, time zone settings,
                                language preferences, and login timestamps.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Usage Data</span>: Simulated trades, session
                                length, page views, clickstream data, account performance, behavioral trends, and
                                interaction history.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">Communications</span>: Chat transcripts, emails,
                                and support ticket details that help us understand and resolve platform issues.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        In addition, we may use automated tools such as cookies, pixels, and analytics trackers to
                        gather behavioral insights and enhance user experience. By using MegaTrader, you consent to the
                        collection of this information in accordance with this Privacy Policy and applicable laws.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3
                        id="use-of-collected-data"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Use of Collected Data
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader uses your data to provide platform functionality, enhance performance, ensure user
                        security, and support compliance efforts. All data is processed under a legitimate operational
                        or legal basis, and we make every effort to minimize data collection to only what is strictly
                        necessary for service delivery.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We may use your data to:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Authenticate and Manage Accounts</span>:
                                Ensuring secure login access, verifying identities, and enabling account recovery.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Deliver Platform Features</span>: Running
                                simulations, storing trading metrics, awarding rewards, and presenting personalized
                                content.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">Enable Communication</span>: Sending service
                                updates, responding to support requests, or delivering reminders, offers, and
                                notifications (where applicable).
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">Monitor and Improve Services</span>: Tracking
                                performance bottlenecks, crash data, platform usability, and user engagement analytics.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">Ensure Compliance and Detect Abuse</span>:
                                Investigating suspicious activity, enforcing terms of service, preventing fraud, and
                                responding to legal inquiries.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        All data usage is restricted to internal operations and approved third-party processors under
                        contract. You can modify preferences and withdraw consent for non-essential data usage at any
                        time via your dashboard settings.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="data-sharing-with-third-parties"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Data Sharing With Third Parties
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader does not sell, rent, or commercially trade your personal information. We only share
                        your data with trusted third-party vendors who help us operate the platform and meet legal or
                        technical obligations. All sharing is done with care, under confidentiality agreements, and
                        aligned with applicable data protection laws such as GDPR and CCPA.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We may share your data with:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Payment Processors</span>:
                                To handle transactions, prevent fraud, and verify purchases (e.g., Stripe or
                                cryptocurrency gateways).
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Analytics and Tracking Providers</span>:
                                Services like Google Analytics help us understand platform performance and user
                                behavior.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span
                                    className="font-bold text-stone-300">Cloud Infrastructure and Storage Services</span>:
                                Used for hosting, backups, and database operations (e.g., AWS, Firebase).
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">Security and Fraud Monitoring Tools</span>: To
                                detect abuse, account sharing, bot activity, or other violations.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">Legal and Regulatory Authorities</span>:
                                When required by law, subpoena, or government order to comply with regulatory
                                obligations.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We never authorize third-party partners to reuse or repurpose your data for unrelated commercial
                        use. All vendors must adhere to strict security and privacy standards as outlined in their Data
                        Processing Agreements with MegaTrader.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="data-storage-and-protection"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Data Storage and Protection
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader uses advanced digital security frameworks and operational protocols to protect your
                        data from unauthorized access, loss, or misuse. We treat your personal information with the
                        highest level of confidentiality and implement multiple layers of protection, including
                        physical, technical, and administrative safeguards.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Our security practices include:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Encryption Protocols</span>:
                                All user data is encrypted both in transit and at rest using industry-standard TLS and
                                AES-256.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Access Control</span>:
                                Role-based access systems ensure that only authorized personnel can view or interact
                                with sensitive data.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span
                                    className="font-bold text-stone-300">Secure Infrastructure</span>:
                                We host data on secured cloud services with redundancy, disaster recovery, and uptime
                                guarantees.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">Continuous Monitoring</span>: Our systems are
                                monitored for anomalies, unauthorized access attempts, and potential breaches in real
                                time.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">Penetration Testing</span>: We conduct regular
                                security audits and third-party assessments to identify and mitigate vulnerabilities.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        In addition to institutional safeguards, we encourage users to:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                Enable two-factor authentication (2FA) to further protect their accounts
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                Use strong, unique passwords and avoid credential reuse
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                Log out of their accounts when using public devices
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        If you detect suspicious activity, please notify us immediately at <a
                        href="mailto:support@megatrader.io"
                        className="text-[#ffb54d]">support@megatrader.io</a> so we
                        can take corrective action.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="user-control-and-data-rights"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        User Control and Data Rights
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader empowers users to control how their data is collected, stored, and used. You have
                        full rights to access, correct, limit, or delete your personal data at any time, in accordance
                        with international privacy regulations like the GDPR and CCPA. Our platform and support systems
                        are built with data transparency and accessibility in mind.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        You may:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">View Stored Data</span>:
                                Review your personal and activity-related information via your account dashboard.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Update or Correct Information</span>:
                                Change your profile details, update contact preferences, or fix inaccuracies.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Limit Processing</span>:
                                Restrict the use of your data for non-essential communications, cookies, or analytics.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Withdraw Consent</span>:
                                Opt out of promotional emails or disable tracking features through privacy settings.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Request Deletion</span>:
                                Submit an account deletion request to have your data permanently removed from our
                                systems.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        To exercise these rights, submit a request through:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300 mr-1">Live Chat</span>
                                on our platform, or
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Email</span>:
                                <a href="mailto:support@megatrader.io"
                                   className="text-[#ffb54d] ml-1">support@megatrader.io</a>
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We will respond within a reasonable time frame, typically 2–5 business days, and fulfill
                        validated deletion or access requests unless restricted by compliance requirements or legal
                        holds.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="cookies-and-tracking-technologies"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Cookies and Tracking Technologies
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader uses cookies and tracking technologies to enhance the functionality, security, and
                        personalization of the platform. These tools allow us to recognize returning users, streamline
                        login sessions, improve page load performance, and detect technical issues or malicious
                        behavior. We are committed to maintaining transparency and giving you control over cookie
                        settings.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Types of cookies we use:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Essential Cookies</span>:
                                Required for core site functions like authentication, fraud detection, and session
                                persistence.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Performance Cookies</span>:
                                Collect data on how users interact with the platform to optimize layout, speed, and
                                accessibility.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span
                                    className="font-bold text-stone-300">Functional Cookies</span>:
                                Store language preferences, user settings, and UI personalization details.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal"><span
                                className="font-bold text-stone-300">Marketing Cookies (opt-in only)</span>: Help us
                                deliver relevant ads and measure the effectiveness of promotional efforts across
                                channels.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Cookie management options:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                You can modify cookie preferences at any time through our in-app cookie banner.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                Most browsers allow you to disable cookies, though this may affect functionality.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                You can clear cookies stored on your device through browser settings or private browsing
                                modes.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        For more information about cookies used on MegaTrader and your opt-out choices, visit our
                        Cookies Policy page or contact <a
                        href="mailto:support@megatrader.io"
                        className="text-[#ffb54d]">support@megatrader.io</a>.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="handling-of-children-data"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Handling of Children’s Data
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader’s platform is not designed or intended for individuals under the age of 18. We do not
                        knowingly collect, process, or retain any personal data from minors, and we take strict measures
                        to prevent underage access to our services.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Our policy includes:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Age Verification</span>:
                                We may implement manual or automated tools to verify that users meet the minimum age
                                requirement.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Account Monitoring</span>:
                                If an account is suspected of belonging to a minor, we will take immediate steps to
                                investigate and restrict access.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span
                                    className="font-bold text-stone-300">Prompt Deletion</span>:
                                In cases where underage usage is discovered, we will delete all associated data and
                                suspend the account without delay.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Parents or guardians who believe that their child has used the MegaTrader platform without
                        permission should contact us immediately at <a href="mailto:support@megatrader.io"
                                                                       className="text-[#ffb54d]">support@megatrader.io</a>.
                        Upon verification of such
                        claims, we will take all necessary actions to secure and erase the child{'\''}s data.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We remain committed to compliance with child protection laws, including the Children’s Online
                        Privacy Protection Act (COPPA) in the United States and similar global regulations. By using the
                        platform, you affirm that you are 18 years of age or older and legally able to enter into
                        binding agreements.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="data-retention-practices"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Data Retention Practices
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader retains personal and technical data only as long as necessary to fulfill operational,
                        legal, and regulatory obligations. Our retention schedules are designed to balance performance
                        optimization, audit requirements, and your privacy rights.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Data retention timelines include:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Active User Accounts</span>:
                                Retained indefinitely while the account remains in good standing and actively used.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Inactive Accounts</span>:
                                May be marked for deletion or anonymization after 12 consecutive months of inactivity.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span
                                    className="font-bold text-stone-300">Support Interactions</span>:
                                Retained for up to 24 months to assist in dispute resolution, audits, and training.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span
                                    className="font-bold text-stone-300">Deleted Accounts</span>:
                                Once a deletion request is verified, all associated data is purged from live systems and
                                queued for erasure from backup servers within 30–60 days.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Certain data may be preserved beyond these periods to:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                Comply with financial, tax, or legal obligations
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                Fulfill contractual audit or investigation requirements
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                Prevent fraud, abuse, or platform manipulation
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        You can request early deletion by contacting <a href="mailto:support@megatrader.io"
                                                                        className="text-[#ffb54d]">support@megatrader.io</a> or
                        through our live chat.
                        Once processed, you will receive confirmation, and no residual data will be stored unless
                        required by law.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="policy-updates-and-revisions"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Policy Updates and Revisions
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader may update or revise this Privacy Policy from time to time to reflect changes in data
                        handling practices, legal requirements, or business operations. Any updates will be posted
                        prominently on our website, and the &quot;Last Updated&quot; date at the top of the page will
                        reflect the latest revision.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Update protocols include:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Material Changes</span>:
                                For significant changes affecting your rights, such as expanded data usage or
                                third-party integrations, we will provide additional notice via email or in-dashboard
                                alerts.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Minor or Technical Edits</span>:
                                Updates that clarify language, restructure content, or revise terminology will be posted
                                without separate notification.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span
                                    className="font-bold text-stone-300">Version Control</span>:
                                Older versions of the policy may be archived and made available upon request for
                                transparency.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We encourage all users to review this policy periodically. Your continued use of the MegaTrader
                        platform after changes take effect constitutes acceptance of the revised policy. If you do not
                        agree with the changes, you should discontinue use and contact us to manage your data or close
                        your account.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 id="contacting-megatrader-regarding-privacy"
                        className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Contacting MegaTrader Regarding Privacy
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        If you have any questions, concerns, or requests related to MegaTrader’s Privacy Policy or how
                        your data is handled, you are encouraged to reach out to our team. We value user feedback and
                        take all inquiries seriously.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        You may contact us through:
                    </p>
                    <div className="pl-8">
                        <ul className="list-disc text-stone-400 space-y-2">
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Live Chat Support</span>:
                                Available directly on the MegaTrader website or user dashboard. You can open a support
                                ticket for privacy-related matters.
                            </li>
                            <li className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                                <span className="font-bold text-stone-300">Email Contact</span>:
                                Send your inquiries to <a
                                href="mailto:support@megatrader.io"
                                className="text-[#ffb54d]">support@megatrader.io</a> with the subject line &quot;Privacy
                                Request&quot; for faster routing.
                            </li>
                        </ul>
                    </div>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Whether you’re seeking clarification, requesting access to your data, or submitting a complaint,
                        we are here to assist you. All submissions will be acknowledged promptly and handled within our
                        standard response window of 2–5 business days, depending on the complexity of the request.
                    </p>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We strive to respond clearly, respectfully, and with a commitment to protecting your rights and
                        clarifying your data options.
                    </p>
                </div>
            </div>
        </div>
    );
}

export default TermsOfService;