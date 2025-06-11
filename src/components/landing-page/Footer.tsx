'use client';

import Image from "next/image";
import React, {useState} from "react";
import SocialMedia from "@/components/landing-page/SocialMedia";
import SubscribeForm from "@/components/SubscribeForm";
import {IShowAlert} from "@/app/(backoffice)/refferals/page";
import Alert from "@/components/Alert";
import Link from "next/link";

export default function Footer() {
    const [showAlert, setShowAlert] = useState<IShowAlert | null>(null);

    function cbShowAlert(payload: IShowAlert | null) {
        setShowAlert(payload)
    }

    return (
        <>
            <footer
                className="w-full max-w-7xl flex-1 h-dvh mx-auto px-4 pb-8  flex items-center justify-between flex-col space-y-8">
                <div
                    className="w-full p-8 bg-[#131210] rounded-[20px] outline outline-1 outline-neutral-700">
                    <div className="w-full grid grid-cols-2 space-y-8 lg:space-y-0 lg:space-x-8">
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
                                className="justify-start text-stone-400 text-sm font-medium leading-tight">From
                                evaluation to funding, we{'\''}re redefining the trader journey with performance-driven
                                solutions and transparency.
                            </div>
                            <SocialMedia/>
                        </div>
                        <div className="col-span-2 space-y-4 lg:col-span-1">
                            {showAlert && (
                                <div className="w-full">
                                    <Alert type={showAlert.type}
                                           message={showAlert.message}/>
                                </div>
                            )}

                            <SubscribeForm focusForced={false} compact={true} cbShowAlert={cbShowAlert}/>
                        </div>
                    </div>
                    <div className="my-8 col-span-2">
                        <div className="h-0 border-t-[0.5px] border-t-neutral-700"></div>
                    </div>
                    <div
                        className="grid grid-cols-4 gap-4 lg:inline-flex lg:justify-start lg:items-start lg:gap-8 lg:w-full">
                        <div
                            className="col-span-full text-center lg:text-left lg:flex-1 text-stone-400 text-sm font-medium leading-tight">
                            © 2024 Megatrader
                        </div>
                        <nav
                            className="col-span-full flex-col space-y-4 sm:space-y-0 sm:text-center sm:flex-none sm:justify-center sm:gap-4 lg:contents">
                            <div className="flex justify-center space-x-4 lg:space-x-0 sm:contents">
                                <Link
                                    href='https://help.megatrader.io/en/articles/11553854-megatrader-disclosure-policy'
                                    target={'_blank'}
                                    className="text-stone-400 text-sm font-medium underline leading-tight"
                                >
                                    Disclaimer
                                </Link>
                                <Link
                                    href='https://help.megatrader.io/en/articles/11553837-megatrader-privacy-policy'
                                    target={'_blank'}
                                    className="text-stone-400 text-sm font-medium underline leading-tight">
                                    Privacy Policy
                                </Link>
                            </div>
                            <div className="flex justify-center space-x-4 lg:space-x-0 sm:contents">
                                <Link
                                    href='https://help.megatrader.io/en/articles/11553770-megatrader-terms-of-service'
                                    target={'_blank'}
                                    className="text-stone-400 text-sm font-medium underline leading-tight"
                                >
                                    Terms of Service
                                </Link>
                                <Link
                                    href='https://help.megatrader.io/en/articles/11553882-megatrader-cookies-policy'
                                    target={'_blank'}
                                    className="text-stone-400 text-sm font-medium underline leading-tight"
                                >
                                    Cookies Settings
                                </Link>
                            </div>
                        </nav>
                    </div>
                </div>
            </footer>
        </>
    )
}

