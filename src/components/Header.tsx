'use client'

import Image from 'next/image';
import Link from "next/link";
import {useState} from "react";
import {Bars3Icon, BellIcon, UserCircleIcon} from "@heroicons/react/24/solid";
import {usePathname} from "next/navigation";

const navigationItems = [
    {href: '/account-overview', label: 'ACCOUNT OVERVIEW', sectionId: '/account-overview'},
    {href: '/affiliates', label: 'AFFILIATES', sectionId: '/affiliates'},
    {href: '/payouts', label: 'PAYOUTS', sectionId: '/payouts'},
    {href: '/help-center', label: 'HELP CENTER', sectionId: '/help-center'},
];

export default function Header() {
    const currentPath = usePathname()
    const [isMenuOpen, setIsMenuOpen] = useState(false);
    // bg-[#131210]/70 shadow-[0px_4px_4px_0px_rgba(0,0,0,0.25)] backdrop-blur-[25px]
    return <div
        className={`w-full z-50 transition-all duration-300 ${isMenuOpen ? 'bg-[#131210]' : 'bg-[#131210]/70'} shadow-[0px_4px_4px_0px_rgba(0,0,0,0.25)] backdrop-blur-[25px]`}>
        <div className="w-full max-w-7xl mx-auto px-4 py-6 flex items-center justify-between lg:h-[100px]">
            {/* Logo */}
            <div className="w-auto">
                <Link
                    href="/account-overview"
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
                        className={`text-base text-neutral-50 font-light uppercase leading-6 px-4 py-3 transition-all duration-200 ${
                            currentPath === item.sectionId
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
                        className={`text-base text-neutral-50 text-nowrap font-light uppercase leading-6 px-4 py-3 transition-all duration-200 ${
                            currentPath === item.sectionId
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
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_4562_2177" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="24" height="24">
                                <rect width="24" height="24" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_4562_2177)">
                                <path
                                    d="M5 21C4.45 21 3.97917 20.8042 3.5875 20.4125C3.19583 20.0208 3 19.55 3 19V5C3 4.45 3.19583 3.97917 3.5875 3.5875C3.97917 3.19583 4.45 3 5 3H12V5H5V19H12V21H5ZM16 17L14.625 15.55L17.175 13H9V11H17.175L14.625 8.45L16 7L21 12L16 17Z"
                                    fill="white"/>
                            </g>
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
}