import Link from "next/link";
import Image from "next/image";
import React from "react";
import FooterLinks from "@/components/landing-page/FooterLinks";

const links: { href: string, title: string }[] = [
    {
        href: '#',
        title: 'Disclaimer'
    },
    {
        href: '#',
        title: 'Privacy Policy'
    },
    {
        href: '#',
        title: 'Terms of Service'
    },
    {
        href: '#',
        title: 'Cookies Settings'
    },
]

export default function Footer() {
    return (
        <footer
            className="w-full max-w-7xl flex-1 h-dvh mx-auto px-4 pb-8  flex items-center justify-between flex-col space-y-8">
            <div
                className="w-full p-8 bg-[#131210] rounded-[20px] outline outline-1 outline-neutral-700">
                <div className="w-full grid grid-cols-2 space-y-8 lg:space-x-8">
                    <div className="space-y-4 col-span-2 lg:col-span-1">
                        <div className="flex gap-4 items-center">
                            <Image src={'/assets/images/logo-mt.svg'}
                                   width={60} height={60} alt={'Logo Megatrader'}/>
                            <Image
                                src="../assets/images/megatrader-original.svg"
                                alt="Logo"
                                width={200}
                                height={45}
                            />
                        </div>
                        <div
                            className="self-stretch justify-start text-stone-400 text-sm font-medium leading-tight">From
                            evaluation to funding, we{'\''}re redefining the trader journey with performance-driven
                            solutions and transparency.
                        </div>
                        <FooterLinks/>
                    </div>
                    <div className="bg-gray-600 h-[144px] col-span-2 lg:col-span-1">
                    </div>
                    <div className="self-stretch my-8 col-span-2">
                        <div className="h-0 border-t-[0.5px] border-t-neutral-700"></div>
                    </div>
                    <div className="self-stretch inline-flex justify-start items-start gap-8 col-span-2">
                        <div className="flex-1 justify-start text-stone-400 text-sm font-medium leading-tight">© 2024
                            Megatrader
                        </div>
                        {links.map((link, index) => (
                            <Link
                                key={index}
                                className="justify-start text-stone-400 text-sm font-medium underline leading-tight"
                                href={link.href}>
                                {link.title}
                            </Link>
                        ))}
                    </div>
                </div>
            </div>
        </footer>
    )
}

