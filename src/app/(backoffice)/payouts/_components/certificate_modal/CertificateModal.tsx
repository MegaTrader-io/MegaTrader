import React from 'react';
import Dialog from "@/components/Dialog";
import LogoExpanded from "@/components/LogoExpanded";
import {Button} from "@/components/Button";
import SocialMedia from "@/components/landing-page/SocialMedia";
import {CopyButton} from "@/components/CopyButton";

function CertificateModal({open, onClose}: {
    open: boolean,
    onClose: () => void,
}) {

    const handlerDownload = () => {
        console.info('download')
    }

    return (
        <Dialog showModal={open}
                childrenClassName={'max-h-dvh'}
                className="w-[calc(100vw-32px)] sm:w-[580px]"
                title={'CERTIFICATE'}
                onClose={onClose}>
            <div className="space-y-8">
                <div
                    className="p-8  relative rounded-2xl w-full sm:w-[548px] h-auto flex bg-[url('/assets/images/bg-certificate.svg')] bg-no-repeat bg-center bg-cover">
                    <div className="text-center flex flex-col items-center space-y-4">
                        <LogoExpanded logoType={'black'}/>
                        <div>
                            <div
                                className="text-center justify-start text-[#131210] text-[32px] font-medium  uppercase leading-10">FUNDING
                            </div>
                            <div
                                className="text-center justify-start text-[#131210] text-xl font-medium  uppercase leading-normal">Certificate
                            </div>
                        </div>
                        <div className="gap-1 flex flex-col">
                            <div
                                className="text-center justify-start text-[#131210] text-base font-medium leading-normal">PROUDLY
                                PRESENTED TO
                            </div>
                            <div className="text-center justify-start text-black text-xl font-bold leading-loose">John
                                Doe
                            </div>
                        </div>
                        <div
                            className="px-8 py-2 bg-black/10 rounded-[64px] outline outline-2 outline-offset-[-2px] outline-[#ffd78a] inline-flex justify-center items-center gap-2.5">
                            <div
                                className="text-center justify-start text-black text-xl font-bold leading-loose">$50,000
                            </div>
                        </div>
                        <div className="text-center justify-start text-[#131210] text-sm font-medium leading-tight">Who
                            has
                            demonstrated the capability to effectively achieve the MegaTrader profit target, you have
                            exhibited commendable risk management skills and exceptional trading discipline.
                        </div>
                    </div>
                </div>

                <div className="space-y-2">
                    <div className="text-stone-400 text-base font-bold leading-normal">
                        Certificate URL:
                    </div>
                    <div
                        className="w-full py-3 px-4 bg-[#1e1e1e]/70 rounded-xl outline outline-1 outline-offset-[-1px] outline-neutral-700 inline-flex justify-start items-center">
                        <div
                            className="flex-1 justify-start truncate text-stone-400 text-base font-medium leading-normal">
                            https://megatrader.io/share/certificate/akl54as
                        </div>
                        <CopyButton className="text-[#ffd78a] w-6 h-6"
                                    value="https://megatrader.io/share/certificate/akl54as"/>
                        <div
                            className="ml-2 text-right justify-start text-[#ffd78a] text-sm font-medium uppercase leading-tight">Copy
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-[auto_1fr] gap-4 items-center">
                    <div className="text-stone-400 text-base font-bold leading-normal">
                        Share on social media
                    </div>
                    <SocialMedia className="justify-between !gap-0 sm:gap-4 w-full" size={'lg'}/>
                </div>

                <Button onClick={handlerDownload} className="w-full">
                    <div className="flex items-center gap-2">
                        <span className="leading-[25px]">DOWNLOAD</span>
                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_9877_2714" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="25" height="25">
                                <rect width="24" height="24" transform="matrix(1 0 0 -1 0.5 24.3335)" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_9877_2714)">
                                <path
                                    d="M12.5 8.3335L16.5 12.3335L15.1 13.7335L13.5 12.1335V16.3335H11.5V12.1335L9.9 13.7335L8.5 12.3335L12.5 8.3335ZM12.5 2.3335C11.1167 2.3335 9.81667 2.596 8.6 3.121C7.38333 3.646 6.325 4.3585 5.425 5.2585C4.525 6.15849 3.8125 7.21683 3.2875 8.4335C2.7625 9.65016 2.5 10.9502 2.5 12.3335C2.5 13.7168 2.7625 15.0168 3.2875 16.2335C3.8125 17.4502 4.525 18.5085 5.425 19.4085C6.325 20.3085 7.38333 21.021 8.6 21.546C9.81667 22.071 11.1167 22.3335 12.5 22.3335C13.8833 22.3335 15.1833 22.071 16.4 21.546C17.6167 21.021 18.675 20.3085 19.575 19.4085C20.475 18.5085 21.1875 17.4502 21.7125 16.2335C22.2375 15.0168 22.5 13.7168 22.5 12.3335C22.5 10.9502 22.2375 9.65016 21.7125 8.4335C21.1875 7.21683 20.475 6.15849 19.575 5.2585C18.675 4.3585 17.6167 3.646 16.4 3.121C15.1833 2.596 13.8833 2.3335 12.5 2.3335Z"
                                    fill="black"/>
                            </g>
                        </svg>
                    </div>

                </Button>
            </div>
        </Dialog>
    );
}

export default CertificateModal;