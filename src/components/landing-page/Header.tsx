import Image from 'next/image';
import Link from "next/link";
import {useEffect, useState} from "react";
import {Bars3Icon} from "@heroicons/react/24/solid";

const navigationItems = [
    {href: '#home', label: 'HOME', sectionId: 'home'},
    {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works'},
    {href: '#pricing', label: 'PRICING', sectionId: 'pricing'},
    {href: '#features', label: 'FEATURES', sectionId: 'features'},
    {href: '#faq', label: 'FAQ', sectionId: 'faq'},
];

export default function Header() {
    const [activeSection, setActiveSection] = useState('home');
    const [hasScrolled, setHasScrolled] = useState(false);
    const [isMenuOpen, setIsMenuOpen] = useState(false);

    console.info(hasScrolled);
    useEffect(() => {
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

    return (
        <div
            className={`fixed top-0 left-0 w-full z-50 transition-all duration-300 ${isMenuOpen ? 'bg-[#131210]' : 'bg-[#111]/80'}  backdrop-blur-3xl shadow-lg`}
        >
            <div className="w-full max-w-7xl mx-auto px-4 flex items-center justify-between lg:h-[100px]">
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
                            className={`text-xl text-neutral-50 font-light uppercase leading-6 px-4 py-3 transition-all duration-200 ${
                                activeSection === item.sectionId
                                    ? 'text-white bg-[#1e1e1e] rounded-lg'
                                    : 'text-gray-400 hover:text-white'
                            }`}
                        >
                            {item.label}
                        </Link>
                    ))}

                    <Link
                        href="/auth/login"
                        className={`${isMenuOpen ? 'block' : 'hidden'} text-xl text-neutral-50 font-light uppercase leading-6 px-4 py-3 transition-all duration-200`}>
                        Sign In
                    </Link>
                </div>

                {/* Navegación escritorio */}
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

                {/* Botón Sign In */}
                <div className="hidden lg:flex">
                    <div className="flex items-center gap-3.5">
                        <Link
                            href="/auth/login"
                            className="btn-dark-link  rounded-xl h-12 px-4 py-3"
                        >
                            Sign In
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
