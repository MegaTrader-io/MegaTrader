import Image from 'next/image';
import Link from "next/link";
import React, {useEffect, useState} from "react";
import {Bars3Icon} from "@heroicons/react/24/solid";
import PopoverMenu from "@/components/backoffice/PopoverMenu";
import {usePathname} from "next/navigation";

const navigationItems = [
    {href: '#hero-section', label: 'HOME', sectionId: 'hero-section', visible: true},
    {href: '#hero-section', label: '', sectionId: 'market-data', visible: false},
    {href: '#hero-section', label: '', sectionId: 'sponsor', visible: false},
    {href: '#hero-section', label: '', sectionId: 'megatrader-numbers', visible: false},
    {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works', visible: true},
    {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works-01', visible: false},
    {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works-02', visible: false},
    {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works-03', visible: false},
    {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works-04', visible: false},
    {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works-05', visible: false},
    {href: '#pricing', label: 'PRICING', sectionId: 'pricing', visible: true},
    {href: '#features', label: 'FEATURES', sectionId: 'features', visible: true},
    {href: '#features', label: '', sectionId: 'feature-smarter-tools', visible: false},
    {href: '#features', label: '', sectionId: 'feature-your-path', visible: false},
    {href: '#features', label: '', sectionId: 'feature-earn-more-throuch', visible: false},
    {href: '#features', label: '', sectionId: 'feature-discover-the-platforms', visible: false},
    {href: '#features', label: '', sectionId: 'feature-our-with-drawal-methods', visible: false},
    {href: '#features', label: '', sectionId: 'feature-trusted-by-leadres', visible: false},
    {href: '#faq', label: 'FAQ', sectionId: 'faq', visible: true},
];

export default function Header() {
    const [activeSection, setActiveSection] = useState('hero-section');
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    const currentPath = usePathname()
    const section = navigationItems.find(nav => nav.sectionId === activeSection)!
    const activeSectionGroup = section.href.replace('#', '');
    const visibleNavigationItems = navigationItems.filter(n => n.visible);

    useEffect(() => {
        const options = {
            root: null,
            rootMargin: '100px 0px 100px 0px',
            threshold: 0.8,
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    setActiveSection(entry.target.id);
                }
            });
        }, options);

        const observedElements: HTMLElement[] = [];

        navigationItems.forEach(({sectionId}) => {
            const element = document.getElementById(sectionId);
            if (element) {
                observer.observe(element);
                observedElements.push(element);
            }
        });

        return () => {
            observedElements.forEach((el) => observer.unobserve(el));
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

    return (
        <div
            className={`fixed top-0 left-0 w-full z-50 transition-all duration-300 h-[100px] flex ${isMenuOpen ? 'bg-[#131210]' : 'bg-[#111]/80'}  backdrop-blur-3xl shadow-lg`}
        >
            <div className="w-full max-w-7xl mx-auto px-4 flex items-center justify-between lg:h-[100px]">
                {/* Logo */}
                <div className="w-auto">
                    <Link
                        href="/landing-page"
                        className="flex gap-4 items-center"
                    >
                        <Image src={'/assets/images/logo-mt.svg'}
                               width={60} height={60} alt={'Logo Megatrader'}/>

                        <Image
                            src="../assets/images/megatrader-original.svg"
                            alt="Logo"
                            width={250}
                            height={45}
                            className="w-[170px] h-[47px] lg:w-[250px] lg:h-[45px] hidden lg:block"
                        />
                    </Link>
                </div>

                <div
                    className={`${
                        isMenuOpen ? 'block' : 'hidden'
                    } absolute top-[96px] left-0 w-full h-screen ${isMenuOpen ? 'bg-[#1e1e1e]' : 'bg-[#111]'}  flex flex-col items-center lg:hidden`}
                >
                    {visibleNavigationItems.map((item) => (
                        <Link
                            key={item.label}
                            href={item.href}
                            onClick={(e) => handleClick(e, item.href)}
                            className={`text-xl text-neutral-50 font-light uppercase leading-6 px-4 py-3 transition-all duration-200 ${
                                activeSectionGroup === item.sectionId
                                    ? 'text-white bg-[#1e1e1e] rounded-lg'
                                    : 'text-gray-400 hover:text-white'
                            }`}
                        >
                            {item.label}
                        </Link>
                    ))}
                </div>

                <nav
                    className="hidden lg:flex justify-start items-center flex-row xl:gap-2"
                    aria-label="Main navigation"
                >
                    {visibleNavigationItems.map((item) => (
                        <Link
                            key={item.label}
                            href={item.href}
                            onClick={(e) => handleClick(e, item.href)}
                            className={`text-base text-neutral-50 text-nowrap font-light uppercase leading-6 px-4 py-3 transition-all duration-200 ${
                                activeSectionGroup === item.sectionId
                                    ? 'text-white bg-[#1e1e1e] rounded-lg'
                                    : 'text-gray-400 hover:text-white'
                            }`}
                        >
                            {item.label}
                        </Link>
                    ))}
                </nav>

                <div className="">
                    <div className="flex items-center gap-3.5">
                        <Link
                            href="/auth/login"
                            className="btn-dark-link rounded-xl h-12 px-4 py-3"
                        >
                            LOGIN
                        </Link>

                        <PopoverMenu collisionPadding={16}
                                     className="block lg:hidden"
                                     icon={<Bars3Icon className="w-6 h-6 text-white"/>}>
                            <div className="gap1 flex flex-col">
                                {visibleNavigationItems.map((item) => (
                                    <Link
                                        key={item.label}
                                        href={item.href}
                                        data-dismiss="true"
                                        className={`text-stone-800 text-center text-xs font-bold uppercase leading-6 px-4 py-1 transition-all duration-200 ${
                                            currentPath === item.sectionId
                                                ? 'px-3 py-1 bg-neutral-300 rounded border border-neutral-300 justify-center items-center gap-2 inline-flex'
                                                : ' hover:bg-neutral-300'
                                        }`}
                                    >
                                        {item.label}
                                    </Link>
                                ))}
                            </div>
                        </PopoverMenu>
                    </div>
                </div>
            </div>
        </div>
    );
}
