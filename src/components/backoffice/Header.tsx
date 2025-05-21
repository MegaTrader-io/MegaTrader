'use client'

import Image from 'next/image';
import Link from "next/link";
import React from "react";
import {Bars3Icon, UserCircleIcon} from "@heroicons/react/24/solid";
import {usePathname} from "next/navigation";
import PopoverMenu from "@/components/backoffice/PopoverMenu";
import NotificationLink from "@/components/backoffice/NotificationLink";
import clsx from "clsx";

const navigationItems = [
    {href: '/account-overview', visibleOnDesktop: true, label: 'ACCOUNT OVERVIEW', sectionId: '/account-overview'},
    {href: '/affiliates', visibleOnDesktop: true, label: 'AFFILIATES', sectionId: '/affiliates'},
    {href: '/payouts', visibleOnDesktop: true, label: 'PAYOUTS', sectionId: '/payouts'},
    {href: 'https://help.megatrader.io/en/', visibleOnDesktop: true, label: 'HELP CENTER', sectionId: '/help-center'},
    {
        href: '/profile/identity-verification',
        visibleOnDesktop: false,
        label: 'MY PROFILE',
        sectionId: '/profile/identity-verification'
    },
];

function ProfileIcon({currentPath}: { currentPath: string }) {
    const isProfileCurrentPath = currentPath.startsWith('/profile/');
    return (
        <Link
            href="/profile/identity-verification"
            className={clsx('btn-dark-link rounded-xl w-12 h-12', {'!bg-stone-900': isProfileCurrentPath})}
        >
            <UserCircleIcon className="w-6 h-6 text-white"/>
        </Link>
    )
}

export default function Header() {
    const currentPath = usePathname()

    return <div
        className={`sticky top-0 w-full z-50 transition-all duration-300 bg-[#111]/80 header-shadow`}>
        <div
            className="w-full max-w-7xl mx-auto px-4 py-6 lg:py-0 grid grid-cols-[auto_1fr_auto] items-center justify-between h-[100px]">
            {/* Logo */}
            <div className="w-auto">
                <Link
                    href="/account-overview"
                    className="flex gap-4 items-center"
                >
                    <Image src={'/assets/images/logo-mt.svg'}
                           className="w-[60px]"
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

            <div className="w-full justify-center flex">
                {/* desktop */}
                <nav
                    className="hidden lg:flex justify-start items-center flex-row xl:gap-2"
                    aria-label="Main navigation"
                >
                    {navigationItems.filter(menu => menu.visibleOnDesktop).map((item) => (
                        <Link
                            key={item.label}
                            href={item.href}
                            target={item.sectionId === '/help-center' ? '_blank' : '_self'}
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
            </div>

            <div className="flex gap-2">
                <NotificationLink/>
                <PopoverMenu collisionPadding={16}
                             className="block lg:hidden"
                             icon={<Bars3Icon className="w-6 h-6 text-white"/>}>
                    <div className="gap1 flex flex-col">
                        {navigationItems.map((item) => (
                            <Link
                                key={item.label}
                                href={item.href}
                                target={item.sectionId === '/help-center' ? '_blank' : '_self'}
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
                        <Link
                            href="/auth/login"
                            data-dismiss="true"
                            className={`text-stone-800 text-center text-xs font-bold uppercase leading-6 px-4 py-1 transition-all duration-200 hover:bg-neutral-300`}
                        >
                            LOG OUT
                        </Link>
                    </div>
                </PopoverMenu>
                <div className="hidden lg:flex">
                    <div className="flex items-center gap-2">
                        <ProfileIcon currentPath={currentPath}/>
                        <Link
                            href="/auth/login"
                            className="btn-dark-link rounded-xl w-12 h-12"
                        >
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
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
    </div>
}