import Image from 'next/image'
import Link from "next/link";
import {useEffect, useState} from "react";

const navigationItems = [
    {href: '#home', label: 'HOME', sectionId: 'home'},
    {href: '#how-it-works', label: 'HOW IT WORKS', sectionId: 'how-it-works'},
    {href: '#pricing', label: 'PRICING', sectionId: 'pricing'},
    {href: '#features', label: 'FEATURES', sectionId: 'features'},
    {href: '#faq', label: 'FAQ', sectionId: 'faq'},
];

export default function Header() {
    const [activeSection, setActiveSection] = useState('home');

    useEffect(() => {
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.5, // Cambia esto según el comportamiento deseado
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
            element.scrollIntoView({behavior: 'smooth'});
        }
    };

    return (
        <div
            className="w-full inline-flex items-center justify-between relative">
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
            <nav className="flex justify-start items-center flex-row gap-2" aria-label="Main navigation">
                {navigationItems.map((item) => (
                    <Link
                        key={item.label}
                        href={item.href}
                        onClick={(e) => handleClick(e, item.href)}
                        className={`text-xl text-neutral-50 text-nowrap font-light uppercase leading-6 px-4 py-3 ${
                            activeSection === item.sectionId
                                ? 'bg-[#1e1e1e] rounded-xl border border-neutral-700'
                                : ''
                        }`}
                    >
                        {item.label}
                    </Link>
                ))}
            </nav>
            <div className="w-auto">
                <div className="flex items-center gap-3.5">
                    <Link href="#"
                          className="bg-[#292524] rounded-xl border border-neutral-700 h-12 px-4 py-3 text-white uppercase flex items-center">
                        Login
                    </Link>
                </div>

            </div>
        </div>
    );
}
