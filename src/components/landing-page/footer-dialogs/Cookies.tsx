'use client';

import React, {useEffect, useRef, useState} from 'react';
import clsx from "clsx";
import Select from "@/components/Select";

interface TabOption {
    id: string,
    title: string
}

const ITEMS: TabOption[] = [
    {
        "title": "Overview",
        "id": "overview"
    },
    {
        "title": "What Are Cookies?",
        "id": "what-are-cookies"
    },
    {
        "title": "Types of Cookies We Use",
        "id": "types-of-cookies-we-use"
    },
    {
        "title": "Essential Cookies",
        "id": "essential-cookies"
    },
    {
        "title": "Performance Cookies",
        "id": "performance-cookies"
    },
    {
        "title": "Functionality Cookies",
        "id": "functionality-cookies"
    },
    {
        "title": "Targeting and Advertising Cookies",
        "id": "targeting-and-advertising-cookies"
    },
    {
        "title": "Managing Cookie Preferences",
        "id": "managing-cookie-preferences"
    },
    {
        "title": "Third-Party Cookies",
        "id": "third-party-cookies"
    },
    {
        "title": "Updates to This Cookie Policy",
        "id": "updates-to-this-cookie-policy"
    },
    {
        "title": "Contact Us",
        "id": "contact-us"
    }
]

function Cookies() {
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
                <div className="space-y-4" id='overview'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Overview
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        MegaTrader uses cookies and similar technologies to improve your experience, deliver
                        personalized content, and analyze platform usage. This Cookie Settings Policy explains what
                        cookies are, how we use them, and your options for managing them. By using our site, you consent
                        to the use of cookies in accordance with this policy, unless you have disabled them via your
                        browser or settings panel. Our cookie practices comply with applicable data privacy laws,
                        including GDPR, CCPA, and other global standards. We value transparency, so we encourage users
                        to understand how cookies work and how they affect your privacy while browsing and trading on
                        MegaTrader.
                    </p>
                </div>
                <div className="space-y-4" id='what-are-cookies'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        What Are Cookies?
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Cookies are small text files placed on your computer, smartphone, or tablet when you visit a
                        website. These files store information such as your preferences, login sessions, or items in
                        your cart. Cookies are widely used to make websites function more efficiently and provide
                        valuable data to site operators. They do not run programs or deliver viruses to your device.
                        Some cookies are temporary and deleted after your session ends (session cookies), while others
                        remain on your device until you delete them or they expire (persistent cookies). MegaTrader
                        utilizes both types for various performance, security, and user experience purposes.
                    </p>
                </div>
                <div className="space-y-4" id='types-of-cookies-we-use'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Types of Cookies We Use
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Cookies are small text files placed on your computer, smartphone, or tablet when you visit a
                        website. These files store information such as your preferences, login sessions, or items in
                        your cart. Cookies are widely used to make websites function more efficiently and provide
                        valuable data to site operators. They do not run programs or deliver viruses to your device.
                        Some cookies are temporary and deleted after your session ends (session cookies), while others
                        remain on your device until you delete them or they expire (persistent cookies). MegaTrader
                        utilizes both types for various performance, security, and user experience purposes.
                    </p>
                </div>
                <div className="space-y-4" id='essential-cookies'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Essential Cookies
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Essential cookies enable core functionality such as logging in, verifying your identity, and
                        accessing secure areas of the platform. Without these cookies, many MegaTrader services would
                        not function properly. These cookies are always active and cannot be disabled via the cookie
                        consent panel, as they are critical for security, fraud detection, and navigation. Examples
                        include session tokens, CSRF protection tokens, and load balancer routing. Although users can
                        block these cookies using their browser, doing so may render key features of the platform
                        inoperable and could prevent access to your account or trading dashboard.
                    </p>
                </div>
                <div className="space-y-4" id='performance-cookies'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Performance Cookies
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Performance cookies help us monitor how visitors interact with the MegaTrader platform. They
                        collect aggregated data on page load times, navigation paths, error messages, and other behavior
                        metrics. These cookies allow us to identify technical issues, optimize page layouts, and ensure
                        smooth operation across devices and browsers. We use analytics tools, including Google Analytics
                        and similar providers, under strict data privacy contracts. The information collected is
                        anonymized and not used to personally identify users. Disabling these cookies may limit our
                        ability to detect bugs or enhance the performance of our platform for all users.
                    </p>
                </div>

                <div className="space-y-4" id='functionality-cookies'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Functionality Cookies
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Functionality cookies remember your user settings and preferences to provide a customized
                        experience. This includes remembering your selected trading interface, preferred language, time
                        zone, account view mode, or recent activity. These cookies allow MegaTrader to deliver a more
                        personalized experience and reduce the need to re-enter settings on each visit. While not
                        essential, disabling functionality cookies may degrade user convenience or cause repetitive
                        prompts. These cookies are set by MegaTrader directly and are never used for advertising or
                        third-party tracking purposes. They expire after a predefined period unless manually deleted by
                        the user.
                    </p>
                </div>

                <div className="space-y-4" id='targeting-and-advertising-cookies'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Targeting and Advertising Cookies
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        These cookies are used to deliver relevant ads, promotions, and content to users based on their
                        browsing behavior or account interactions. They may be set by MegaTrader or by third-party
                        advertising networks such as Google Ads or Meta Pixel. We use these cookies to measure the
                        effectiveness of our campaigns and avoid showing the same ads repeatedly. If you opt out of
                        these cookies, you may still see ads, but they will be less tailored to your interests. We never
                        sell your data, and all advertising cookies are subject to opt-in consent and strict data
                        handling protocols.
                    </p>
                </div>

                <div className="space-y-4" id='managing-cookie-preferences'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Managing Cookie Preferences
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        You can manage your cookie preferences at any time by clicking “Cookie Settings” at the bottom
                        of our website. From there, you can opt in or out of non-essential cookie categories such as
                        performance or advertising. You can also modify your preferences through your browser settings
                        to block, delete, or restrict cookies globally. Most browsers provide tools to notify you when
                        cookies are set or allow you to accept them selectively. Please note that disabling some cookies
                        may impact your experience or prevent you from using certain features of the MegaTrader platform
                        effectively.
                    </p>
                </div>

                <div className="space-y-4" id='third-party-cookies'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Third-Party Cookies
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Some cookies on MegaTrader are placed by trusted third-party providers that assist us with
                        analytics, security, marketing, and integrations. These third parties include payment
                        processors, identity verification vendors, and behavioral analytics tools. Each third party is
                        subject to its own privacy and cookie policies, and we require them to process data in
                        compliance with applicable laws. We do not permit unauthorized third-party cookies or hidden
                        tracking scripts. You can find a list of active third-party providers and their respective
                        cookie use in our Cookie Settings Panel or by requesting a detailed report via our support team.
                    </p>
                </div>

                <div className="space-y-4" id='updates-to-this-cookie-policy'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Updates to This Cookie Policy
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        We may revise this Cookie Settings Policy from time to time to reflect updates in technology,
                        legal requirements, or our business operations. When significant changes are made, we will
                        notify users via a banner notification, email, or an update log on our site. Continued use of
                        the MegaTrader platform after such updates constitutes acceptance of the revised terms. We
                        encourage you to review this policy regularly to stay informed about how we use cookies. If you
                        have questions about any changes or your rights regarding cookies, please contact us using the
                        information provided below.
                    </p>
                </div>

                <div className="space-y-4" id='contact-us'>
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7">
                        Contact Us
                    </h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        If you have any questions or concerns about our cookie practices, please contact our Privacy
                        Team at cookies@megatrader.com. You may also write to us at: MegaTrader Holdings Inc., Attn:
                        Privacy Department, 350 Lincoln Road, Miami Beach, FL 33139, USA. We aim to respond to
                        cookie-related inquiries within five business days. EU or UK residents may also raise cookie
                        concerns with their respective data protection authorities. For immediate assistance or to
                        request a full cookie audit of your current session, please use the “Support” or “Live Chat”
                        function within the MegaTrader platform.
                    </p>
                </div>
            </div>
        </div>
    );
}

export default Cookies;