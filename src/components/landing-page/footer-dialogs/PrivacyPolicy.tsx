'use client';

import React, {useState} from 'react';
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
    {id: 'sharing-and-disclosure', title: 'Sharing and Disclosure of Information'},
    {id: 'data-retention', title: 'Data Retention'},
    {id: 'your-rights-and-choices', title: 'Your Rights and Choices'},
    {id: 'data-security', title: 'Data Security'},
    {id: 'international-data-transfers', title: 'International Data Transfers'},
    {id: 'cookie-settings', title: 'Cookie Settings'},
    {id: 'updates-to-privacy-policy', title: 'Updates to This Privacy Policy'}
];

function PrivacyPolicy() {
    const [tab, setTab] = useState<TabOption>(ITEMS[0]);

    function changeTab(option: TabOption) {
        setTab(option);
    }

    function onChange(e: React.ChangeEvent<HTMLSelectElement>) {
        const selected = ITEMS.find(item => item.id === e.target.value);
        if (selected) changeTab(selected);
    }

    return (
        <div className='md:grid md:grid-cols-[300px_32px_1fr] my-1'>
            <div className="h-full">
                <div className="space-y-2 hidden md:block">
                    {ITEMS.map((item, idx) => (
                        <button
                            key={idx}
                            onClick={() => changeTab(item)}
                            className={clsx('w-full text-left tracking-tight leading-normal', [
                                tab.id === item.id ?
                                    'text-[#ffd78a] font-medium' : 'text-stone-400'
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
                    className="sm:border-r-[1px] sm:border-r-[#404040] mb-8 w-full h-full md:w-0 md:my-0 "></div>
            </div>
            <div className="text-white space-y-12 lg:mx-4">
                {/* Introduction */}
                <div className='space-y-4'>
                    <h3 id='introduction' className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Introduction
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        This Privacy Policy describes how MegaTrader Holdings LLC
                        (&quot;MegaTrader,&quot; &quot;we,&quot; &quot;us,&quot; or &quot;our&quot;)
                        collects, uses, discloses, and protects your personal information when you visit our website,
                        register an account, or use any
                        of our services (collectively, the &quot;Services&quot;). This policy is designed to help you
                        understand what data we collect, why we collect it,
                        how we use it, and the choices you have regarding your information.
                    </p>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        Our goal is to be transparent about our data practices and ensure that your personal information
                        is treated with respect and in accordance
                        with applicable privacy laws. By using our Services, you consent to the practices described in
                        this Privacy Policy.
                    </p>
                </div>

                {/* Information We Collect */}
                <div className='space-y-4'>
                    <h3 id='information-we-collect'
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Information We Collect
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We collect personal information that you provide directly to us, such as when you create an
                        account, submit documents for
                        verification, make a purchase, or contact support. This includes your full name, email address,
                        phone number, country and state
                        of residence, payment information, and any identification documents submitted for compliance
                        purposes.
                    </p>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We also collect data automatically when you access or use our platform. This may include your IP
                        address, browser type, device type,
                        session data, location data, and usage patterns (such as pages visited, time spent, and
                        interaction history). This information is used
                        for analytics, security, and to improve platform performance.
                    </p>
                </div>

                {/* How We Use Your Information */}
                <div className='space-y-4'>
                    <h3 id='how-we-use-your-information'
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        How We Use Your Information
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We use the information we collect to operate and improve the MegaTrader platform and to provide
                        services to you, including:
                    </p>
                    <ul className='list-disc list-inside text-stone-400 space-y-1 ml-4'>
                        <li>Managing your account and preferences</li>
                        <li>Facilitating payments, withdrawals, and issuing payouts</li>
                        <li>Performing identity checks and fulfilling compliance obligations (KYC and AML)</li>
                        <li>Detecting and preventing fraud, unauthorized activity, or system abuse</li>
                        <li>Sending platform notifications, alerts, and administrative messages</li>
                        <li>Delivering customer support and responding to inquiries</li>
                        <li>Analyzing usage to optimize features and functionality</li>
                        <li>Meeting legal, regulatory, and contractual obligations</li>
                    </ul>
                </div>

                {/* Sharing and Disclosure */}
                <div className='space-y-4'>
                    <h3 id='sharing-and-disclosure'
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Sharing and Disclosure of Information
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We share your personal information only when necessary to provide our Services or when legally
                        required. This may include sharing data with payment
                        providers, identity verification services, hosting providers, or authorities responding to
                        lawful requests. We do not sell your personal information
                        or allow third parties to use it for their own marketing.
                    </p>
                </div>

                {/* Data Retention */}
                <div className='space-y-4'>
                    <h3 id='data-retention'
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Data Retention
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We retain your personal information as long as your account is active or as necessary to comply
                        with legal and operational obligations. Once data is no
                        longer needed, it is securely deleted or anonymized.
                    </p>
                </div>

                {/* Your Rights and Choices */}
                <div className='space-y-4'>
                    <h3 id='your-rights-and-choices'
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Your Rights and Choices
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        Depending on your jurisdiction, you may have rights to access, update, correct, or delete your
                        data, object to processing, withdraw consent, or
                        request a machine-readable copy. To exercise these rights, contact us at privacy@megatrader.io.
                        We may require identity verification for such requests.
                    </p>
                </div>

                {/* Data Security */}
                <div className='space-y-4'>
                    <h3 id='data-security' className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Data Security
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We implement technical and organizational measures to protect your data, including encryption,
                        access controls, secure storage, and audits. However, no system is
                        entirely secure. You are responsible for safeguarding your login credentials and notifying us of
                        any unauthorized use.
                    </p>
                </div>

                {/* International Data Transfers */}
                <div className='space-y-4'>
                    <h3 id='international-data-transfers'
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        International Data Transfers
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        MegaTrader is based in the U.S. and may process data in other countries. By using our Services,
                        you consent to transfers to jurisdictions with different
                        data protections. We ensure legal compliance and safeguards for such transfers.
                    </p>
                </div>

                {/* Cookie Settings */}
                <div className='space-y-4'>
                    <h3 id='cookie-settings'
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Cookie Settings
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We use cookies for essential, performance, functional, and marketing purposes. You can manage
                        settings on our site or via your browser. Disabling essential cookies may limit platform
                        features.
                    </p>
                </div>

                {/* Updates to Privacy Policy */}
                <div className='space-y-4'>
                    <h3 id='updates-to-privacy-policy'
                        className='title-dialog text-white text-2xl font-medium uppercase leading-7'>
                        Updates to This Privacy Policy
                    </h3>
                    <p className='text-stone-400 text-base font-medium leading-normal'>
                        We may update this policy to reflect legal or operational changes. Updates will include a new
                        effective date. Continued use after changes constitutes acceptance.
                    </p>
                </div>
            </div>
        </div>
    );
}

export default PrivacyPolicy;