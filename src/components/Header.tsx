import Image from 'next/image';
import Link from "next/link";
import {useEffect, useState} from "react";
import {Bars3Icon, BellIcon, UserCircleIcon} from "@heroicons/react/24/solid";

const navigationItems = [
    {href: '/dashboard', label: 'ACCOUNT OVERVIEW', sectionId: 'dashboard'},
    {href: '/affiliates', label: 'AFFILIATES', sectionId: 'affiliates'},
    {href: '/payouts', label: 'PAYOUTS', sectionId: 'payouts'},
    {href: '/help-center', label: 'HELP CENTER', sectionId: 'help-center'},
];

export default function Header() {
    const [activeSection, setActiveSection] = useState('home');
    const [hasScrolled, setHasScrolled] = useState(false);
    const [isMenuOpen, setIsMenuOpen] = useState(false);

    useEffect(() => {
        console.info('hasScrolled', hasScrolled);
        const handleScroll = () => {
            if (window.scrollY > 10) {
                setHasScrolled(true);
            } else {
                setHasScrolled(false);
            }
        };

        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    useEffect(() => {
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.6,
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    setActiveSection(entry.target.id);
                }
            });
        }, options);

        navigationItems.forEach(({sectionId}) => {
            const element = document.getElementById(sectionId);
            if (element) observer.observe(element);
        });

        return () => {
            navigationItems.forEach(({sectionId}) => {
                const element = document.getElementById(sectionId);
                if (element) observer.unobserve(element);
            });
        };
    }, []);

    const handleClick = (e: React.MouseEvent<HTMLAnchorElement>, href: string) => {
        e.preventDefault();
        const element = document.querySelector(href);
        if (element) {
            const headerOffset = isMenuOpen ? 96 : 131;
            const elementPosition = element.getBoundingClientRect().top + window.scrollY;
            const offsetPosition = elementPosition - headerOffset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth',
            });
            setIsMenuOpen(false);
        }
    };

    // return <header className={clsx('mx-auto w-full max-w-screen-xl bg-red-500 h-[100px] flex items-center', className)}>
    //
    // </header>

    return <div
        className={`w-full z-50 transition-all duration-300 ${isMenuOpen ? 'bg-[#131210]' : 'bg-[#111]/80'}  backdrop-blur-3xl shadow-lg`}
    >
        <div className="w-full max-w-7xl mx-auto px-4 py-6 flex items-center justify-between lg:h-[100px]">
            {/* Logo */}
            <div className="w-auto">
                <Link
                    href="/"
                    onClick={(e) => handleClick(e, '#home')}
                >
                    <Image
                        src="../assets/images/megatrader-original.svg"
                        alt="Logo"
                        width={298}
                        height={96}
                        className="w-[197px] h-[47px] lg:w-[298px] lg:h-[96px]"
                    />
                </Link>
            </div>

            <button
                className="btn-primary block lg:hidden"
                onClick={() => setIsMenuOpen(!isMenuOpen)}
            >
                <Bars3Icon className="w-6 h-6 text-white"/>
            </button>

            <div
                className={`${
                    isMenuOpen ? 'block' : 'hidden'
                } absolute top-[96px] left-0 w-full h-screen ${isMenuOpen ? 'bg-[#1e1e1e]' : 'bg-[#111]'}  flex flex-col items-center lg:hidden`}
            >
                {navigationItems.map((item) => (
                    <Link
                        key={item.label}
                        href={item.href}
                        onClick={(e) => handleClick(e, item.href)}
                        className={`text-base text-neutral-50 font-light uppercase leading-6 px-4 py-3 transition-all duration-200 ${
                            activeSection === item.sectionId
                                ? 'text-white bg-[#1e1e1e] rounded-lg'
                                : 'text-gray-400 hover:text-white'
                        }`}
                    >
                        {item.label}
                    </Link>
                ))}
            </div>

            {/* desktop */}
            <nav
                className="hidden lg:flex justify-start items-center flex-row xl:gap-2"
                aria-label="Main navigation"
            >
                {navigationItems.map((item) => (
                    <Link
                        key={item.label}
                        href={item.href}
                        onClick={(e) => handleClick(e, item.href)}
                        className={`text-base text-neutral-50 text-nowrap font-light uppercase leading-6 px-4 py-3 transition-all duration-200 ${
                            activeSection === item.sectionId
                                ? 'text-white bg-[#1e1e1e] rounded-lg'
                                : 'text-gray-400 hover:text-white'
                        }`}
                    >
                        {item.label}
                    </Link>
                ))}
            </nav>

            <div className="hidden lg:flex">
                <div className="flex items-center gap-2">
                    <Link
                        href="#"
                        className="bg-[#292524] rounded-xl border border-neutral-700 w-12 h-12 text-white uppercase text-nowrap flex items-center justify-center"
                    >
                        <BellIcon className="w-6 h-6 text-white"/>
                    </Link>
                    <Link
                        href="#"
                        className="bg-[#292524] rounded-xl border border-neutral-700 w-12 h-12 text-white uppercase text-nowrap flex items-center justify-center"
                    >
                        <UserCircleIcon className="w-6 h-6 text-white"/>
                    </Link>
                    <Link
                        href="/auth/login"
                        className="bg-[#292524] rounded-xl border border-neutral-700 w-12 h-12 text-white uppercase text-nowrap flex items-center justify-center"
                    >
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="path-1-inside-1_4521_998" fill="white">
                                <path
                                    d="M0 12C0 5.37258 5.37258 0 12 0H36C42.6274 0 48 5.37258 48 12V36C48 42.6274 42.6274 48 36 48H12C5.37258 48 0 42.6274 0 36V12Z"/>
                            </mask>
                            <path
                                d="M0 12C0 5.37258 5.37258 0 12 0H36C42.6274 0 48 5.37258 48 12V36C48 42.6274 42.6274 48 36 48H12C5.37258 48 0 42.6274 0 36V12Z"
                                fill="#292524"/>
                            <path
                                d="M12 1H36V-1H12V1ZM47 12V36H49V12H47ZM36 47H12V49H36V47ZM1 36V12H-1V36H1ZM12 47C5.92487 47 1 42.0751 1 36H-1C-1 43.1797 4.8203 49 12 49V47ZM47 36C47 42.0751 42.0751 47 36 47V49C43.1797 49 49 43.1797 49 36H47ZM36 1C42.0751 1 47 5.92487 47 12H49C49 4.8203 43.1797 -1 36 -1V1ZM12 -1C4.8203 -1 -1 4.8203 -1 12H1C1 5.92487 5.92487 1 12 1V-1Z"
                                fill="#404040" mask="url(#path-1-inside-1_4521_998)"/>
                            <mask id="mask0_4521_998" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="12"
                                  y="12"
                                  width="24" height="24">
                                <rect x="12" y="12" width="24" height="24" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_4521_998)">
                                <path
                                    d="M17 33C16.45 33 15.9792 32.8042 15.5875 32.4125C15.1958 32.0208 15 31.55 15 31V17C15 16.45 15.1958 15.9792 15.5875 15.5875C15.9792 15.1958 16.45 15 17 15H24V17H17V31H24V33H17ZM28 29L26.625 27.55L29.175 25H21V23H29.175L26.625 20.45L28 19L33 24L28 29Z"
                                    fill="white"/>
                            </g>
                        </svg>

                    </Link>
                </div>
            </div>
        </div>
    </div>
}