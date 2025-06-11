'use client';

import React, {useEffect, useRef, useState} from 'react';
import clsx from 'clsx';
import Select from '@/components/Select';

interface TabOption {
    id: string;
    title: string;
}

const ITEMS: TabOption[] = [
    {id: 'introduction', title: 'Introduction'},
    {id: 'information-we-collect', title: 'Information We Collect'},
    {id: 'how-we-use-your-information', title: 'How We Use Your Information'},
    {id: 'cookies-and-tracking-technologies', title: 'Cookies and Tracking Technologies'},
    {id: 'data-sharing-and-disclosure', title: 'Data Sharing and Disclosure'},
    {id: 'international-data-transfers', title: 'International Data Transfers'},
    {id: 'data-retention-and-security', title: 'Data Retention and Security'},
    {id: 'your-rights-and-choices', title: 'Your Rights and Choices'},
    {id: 'childrens-privacy', title: 'Children\'s Privacy'},
    {id: 'changes-to-this-privacy-policy', title: 'Changes to This Privacy Policy'},
    {id: 'contact-us', title: 'Contact Us'},
];

function PrivacyPolicy() {
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
            <div className="h-full w-[300px]">
                <div className="space-y-4 hidden sticky top-1 md:block">
                    {ITEMS.map((item, idx) => (
                        <button
                            key={idx}
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
                        {ITEMS.map((item, idx) => (
                            <option key={idx} value={item.id}>{item.title}</option>
                        ))}
                    </Select>
                </div>
            </div>
            <div className="flex justify-center">
                <div
                    className="sm:border-r-2 sm:border-r-[#404040] mb-8 w-full h-full md:w-0 md:my-0 "></div>
            </div>
            <div className="text-white space-y-12 lg:mx-4">
                <div className='space-y-4' id='introduction'>
                    <h3 className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Introduction
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        At MegaTrader Holdings Inc.
                        (&quot;MegaTrader,&quot; &quot;we,&quot; &quot;our,&quot; or &quot;us&quot;), we are committed
                        to protecting your privacy and handling your personal information responsibly. This Privacy
                        Policy describes how we collect, use, store, share, and protect the information you provide when
                        accessing our website, platform, services, and any related tools. By using MegaTrader, you agree
                        to the terms of this Privacy Policy. If you do not agree, you should not use our services. This
                        policy applies to all users, including traders, affiliates, and visitors, regardless of
                        geographic location. We encourage you to read this policy carefully and contact us if you have
                        any questions. MegaTrader complies with applicable data protection laws, including but not
                        limited to the California Consumer Privacy Act (CCPA), the General Data Protection Regulation
                        (GDPR), and other relevant international privacy frameworks.
                    </p>
                </div>

                <div className='space-y-4' id='information-we-collect'>
                    <h3 className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Information We Collect
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        MegaTrader collects both personally identifiable information (PII) and non-personal data when
                        you register, trade, browse, or interact with our platform. This includes your name, email
                        address, date of birth, billing address, IP address, device type, trading activity, payment
                        details, and identity verification documents (such as passport or driver’s license). We also
                        collect behavioral data via cookies and analytics tools to improve our platform performance and
                        tailor user experiences. You may voluntarily submit additional information through surveys,
                        support forms, or community channels. All data collected is processed in accordance with our
                        legitimate business interests and for compliance with legal obligations under applicable
                        financial regulations.
                    </p>
                </div>

                <div className='space-y-4' id='how-we-use-your-information'>
                    <h3 className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        How We Use Your Information
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We use the information we collect to provide, maintain, and improve MegaTrader’s services. This
                        includes verifying your identity, facilitating transactions, analyzing platform usage,
                        personalizing your experience, and enforcing platform rules and terms. We may also use your
                        information for customer support, account administration, fraud prevention, promotional
                        communication (only with consent), and compliance with regulatory obligations. Your data helps
                        us conduct internal research and platform optimization, ensuring a secure and reliable user
                        environment. We do not sell your personal information to third parties. Any use of your data is
                        aligned with our commitment to confidentiality and lawful processing.
                    </p>
                </div>

                <div className='space-y-4' id='cookies-and-tracking-technologies'>
                    <h3
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Cookies and Tracking Technologies
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        MegaTrader uses cookies, web beacons, and other tracking technologies to improve site
                        functionality, user experience, and advertising relevance. Cookies are small files stored on
                        your device that allow us to remember user preferences, track performance metrics, and deliver
                        tailored content. You may control or delete cookies through your browser settings; however,
                        disabling cookies may affect your ability to access certain features of our platform.
                        Third-party analytics providers (e.g., Google Analytics) may collect aggregated data about your
                        interactions with our services. We use this data to understand user behavior and to enhance the
                        platform’s speed, accuracy, and usability in a compliant and transparent manner.
                    </p>
                </div>

                <div className='space-y-4' id='data-sharing-and-disclosure'>
                    <h3
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Data Sharing and Disclosure
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We may share your personal information with trusted service providers and business partners who
                        assist in delivering our services, such as payment processors, KYC/AML providers, analytics
                        vendors, and hosting providers. These third parties are contractually obligated to protect your
                        data and use it only for authorized purposes. We may also disclose information to law
                        enforcement or regulators if required by law, court order, or subpoena, or if necessary to
                        protect the rights, safety, or property of MegaTrader, its users, or others. In the event of a
                        merger, acquisition, or asset transfer, your information may be transferred to a new entity
                        under the same privacy obligations.
                    </p>
                </div>

                <div className='space-y-4' id='international-data-transfers'>
                    <h3
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        International Data Transfers
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        MegaTrader operates globally, and your personal data may be transferred to, stored in, or
                        processed in countries outside your jurisdiction, including the United States. We take
                        appropriate safeguards to ensure your information is handled in accordance with applicable data
                        protection laws, including standard contractual clauses approved by regulatory authorities.
                        Where required, we obtain your explicit consent for international transfers. Our partners and
                        vendors are vetted to ensure adequate data protection, whether they operate in the EU, UK,
                        Canada, or other jurisdictions. By using our services, you acknowledge and agree to the
                        potential cross-border transfer of your personal information.
                    </p>
                </div>

                <div className='space-y-4' id='data-retention-and-security'>
                    <h3 className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Data Retention and Security
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We retain your personal information for as long as necessary to provide our services, fulfill
                        contractual obligations, comply with legal requirements, resolve disputes, and enforce our
                        agreements. When data is no longer needed, we securely delete or anonymize it. We implement
                        industry-standard security measures, including encryption, secure access controls, firewalls,
                        and regular audits, to protect your data against unauthorized access, misuse, or breach. Despite
                        our efforts, no method of transmission or storage is 100% secure. We encourage users to use
                        strong passwords and enable two-factor authentication when available to further protect their
                        accounts.
                    </p>
                </div>

                <div className='space-y-4' id='your-rights-and-choices'>
                    <h3
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Your Rights and Choices
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        Depending on your jurisdiction, you may have certain rights regarding your personal data,
                        including the right to access, correct, delete, restrict processing, object to processing, or
                        receive a portable copy of your data. You may also withdraw consent for certain uses at any
                        time. To exercise these rights, contact our support team through the designated privacy inquiry
                        channels listed below. We will respond to all requests within legally mandated timeframes.
                        Additionally, you may opt out of marketing emails by clicking “unsubscribe” in any message.
                        Please note that some data may be retained for compliance, dispute resolution, or platform
                        integrity purposes.
                    </p>
                </div>

                <div className='space-y-4' id='childrens-privacy'>
                    <h3 className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Children{'\''}s Privacy
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        MegaTrader{'\''}s services are not intended for or directed to individuals under the age of 18.
                        We do not knowingly collect personal information from minors. If we become aware that we have
                        inadvertently collected data from a person under 18, we will take steps to delete such
                        information promptly. Parents or legal guardians who believe their child may have submitted
                        personal data without their consent should contact us immediately. We encourage all users to be
                        mindful of internet safety practices and to ensure that accounts are only used by individuals
                        who meet our eligibility requirements as outlined in our Terms of Service.
                    </p>
                </div>

                <div className='space-y-4' id='changes-to-this-privacy-policy'>
                    <h3 className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Changes to This Privacy Policy
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We may update this Privacy Policy from time to time to reflect changes in our practices, legal
                        requirements, or platform features. When we make material changes, we will notify you through
                        email, account notifications, or by posting the revised policy on our website with an updated
                        effective date. We encourage you to review this page periodically to stay informed about how we
                        protect your information. Continued use of MegaTrader’s services after a policy update
                        constitutes your acceptance of the revised terms. If you do not agree to the new policy, you
                        must discontinue use of the platform and request account closure and data deletion, if
                        applicable.
                    </p>
                </div>

                <div className='space-y-4' id='contact-us'>
                    <h3 className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Contact Us
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        If you have any questions, concerns, or requests regarding this Privacy Policy or your personal
                        data, please contact MegaTrader’s Data Protection Officer at privacy@megatrader.com. You may
                        also reach us via postal mail at MegaTrader Holdings Inc., Attn: Privacy Department, 350 Lincoln
                        Road, Miami Beach, FL 33139, USA. We are committed to resolving privacy-related inquiries
                        promptly and transparently. Users in the EU or UK may also file complaints with their local data
                        protection authorities. For faster resolution of platform-related questions, please use our
                        in-app support or knowledge base before submitting privacy-specific requests.
                    </p>
                </div>
            </div>
        </div>
    );
}

export default PrivacyPolicy;