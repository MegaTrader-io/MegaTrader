import Image from 'next/image';
import Link from "next/link";
import { useEffect, useState } from "react";

const navigationItems = [
    { href: '#home', label: 'HOME', sectionId: 'home' },
    { href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works' },
    { href: '#pricing', label: 'PRICING', sectionId: 'pricing' },
    { href: '#features', label: 'FEATURES', sectionId: 'features' },
    { href: '#faq', label: 'FAQ', sectionId: 'faq' },
];

export default function Header() {
    const [activeSection, setActiveSection] = useState('home');
    const [hasScrolled, setHasScrolled] = useState(false);

    useEffect(() => {
        // Detectar scroll para cambiar el fondo del navbar
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

        navigationItems.forEach(({ sectionId }) => {
            const element = document.getElementById(sectionId);
            if (element) observer.observe(element);
        });

        return () => {
            navigationItems.forEach(({ sectionId }) => {
                const element = document.getElementById(sectionId);
                if (element) observer.unobserve(element);
            });
        };
    }, []);

    const handleClick = (e: React.MouseEvent<HTMLAnchorElement>, href: string) => {
        e.preventDefault();
        const element = document.querySelector(href);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    return (
        <div
            className={`fixed top-0 left-0 w-full z-50 transition-all duration-300 ${
                hasScrolled ? 'bg-[#111]/80  backdrop-blur-3xl  shadow-lg' : 'bg-transparent'
            }`}
        >
            <div className="w-full max-w-7xl  mx-auto px-4 py-6 flex items-center justify-between">
                <div className="w-auto">
                    <Link
                        href="/"
                        onClick={(e) => handleClick(e, '#home')}
                    >
                        <Image
                            src="../assets/images/megatrader2.svg"
                            alt="Logo"
                            height={83}
                            width={338}
                        />
                    </Link>
                </div>
                <nav
                    className="hidden md:flex justify-start items-center flex-row gap-4"
                    aria-label="Main navigation"
                >
                    {navigationItems.map((item) => (
                        <Link
                            key={item.label}
                            href={item.href}
                            onClick={(e) => handleClick(e, item.href)}
                            className={`text-xl text-neutral-50 text-nowrap font-light uppercase leading-6 px-4 py-3 transition-all duration-200 ${
                                activeSection === item.sectionId
                                    ? 'text-white bg-[#1e1e1e] rounded-lg'
                                    : 'text-gray-400 hover:text-white'
                            }`}
                        >
                            {item.label}
                        </Link>
                    ))}
                </nav>
                <div className="w-auto hidden md:block">
                    <div className="flex items-center gap-3.5">
                        <Link href="#"
                              className="bg-[#292524] rounded-xl border border-neutral-700 h-12 px-4 py-3 text-white uppercase flex items-center">
                            Sign In to Trade
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
