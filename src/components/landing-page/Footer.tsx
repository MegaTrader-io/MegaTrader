'use client';

import Image from "next/image";
import React, {useState} from "react";
import SocialMedia from "@/components/landing-page/SocialMedia";
import SubscribeForm from "@/components/SubscribeForm";
import {IShowAlert} from "@/app/(backoffice)/refferals/page";
import Alert from "@/components/Alert";
import Dialog from "@/components/Dialog";
import TermsOfService from "@/components/landing-page/footer-dialogs/TermsOfService";
import Disclaimer from "@/components/landing-page/footer-dialogs/Disclaimer";
import PrivacyPolicy from "@/components/landing-page/footer-dialogs/PrivacyPolicy";
import Cookies from "@/components/landing-page/footer-dialogs/Cookies";


export type DIALOG_FOOTER_TYPE = 'DISCLAIMER' | 'PRIVACY_POLICY' | 'TERMS_OF_SERVICE' | 'COOKIES_SETTINGS';

export default function Footer() {
    const [showModal, setShowModal] = useState<boolean>(false);
    const [showAlert, setShowAlert] = useState<IShowAlert | null>(null);
    const [modalForm, setModalForm] = useState<{ title: string, component: React.ReactNode } | null>(null)

    function cbShowAlert(payload: IShowAlert | null) {
        setShowAlert(payload)
    }

    function openDialog(option: DIALOG_FOOTER_TYPE): void {
        console.info('option', option);

        let title = '';
        let component: React.ReactNode = <><span className="text-white">{option}</span></>
        if (option === 'DISCLAIMER') {
            title = 'Disclaimer';
            component = <Disclaimer/>
        } else if (option === 'PRIVACY_POLICY') {
            title = 'Privacy Policy';
            component = <PrivacyPolicy/>
        } else if (option === 'TERMS_OF_SERVICE') {
            title = 'Terms of Service';
            component = <TermsOfService/>
        } else if (option === 'COOKIES_SETTINGS') {
            title = 'Cookies Settings';
            component = <Cookies/>
        }

        setModalForm({
            title,
            component
        })


        setShowModal(true);
    }

    return (
        <>
            {showModal && (
                <Dialog
                    className="w-[calc(100vw-32px)] sm:max-w-[800px] px-4 py-8"
                    childrenClassName="px-0 !pb-0 lg:!max-h-[847px] scrollbar scrollbar-track-mgt-dark scrollbar-thumb-rounded-full scrollbar-track-rounded-full scrollbar-w-2 scrollbar-thumb-neutral-700 scrollbar-thumb-custom"
                    showModal={showModal}
                    onClose={() => setShowModal(false)}
                    title={modalForm?.title}>
                    {modalForm?.component}
                </Dialog>
            )}

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
                                <button
                                    onClick={(e) => {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        openDialog("DISCLAIMER")
                                    }}
                                    className="text-stone-400 text-sm font-medium underline leading-tight"
                                >
                                    Disclaimer
                                </button>
                                <button
                                    className="text-stone-400 text-sm font-medium underline leading-tight"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        openDialog("PRIVACY_POLICY")
                                    }}>
                                    Privacy Policy
                                </button>
                            </div>
                            <div className="flex justify-center space-x-4 lg:space-x-0 sm:contents">
                                <button
                                    onClick={(e) => {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        openDialog("TERMS_OF_SERVICE")
                                    }}
                                    className="text-stone-400 text-sm font-medium underline leading-tight"
                                >
                                    Terms of Service
                                </button>
                                <button
                                    onClick={(e) => {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        openDialog("COOKIES_SETTINGS")
                                    }}
                                    className="text-stone-400 text-sm font-medium underline leading-tight"
                                >
                                    Cookies Settings
                                </button>
                            </div>
                        </nav>
                    </div>
                </div>
            </footer>
        </>
    )
}

