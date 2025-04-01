import React from "react";
import Badge from "@/components/Badge";
import Image from "next/image";

const Layout = ({children,}: {
    children: React.ReactNode
}) => {
    return <div className="h-dvh lg:flex lg:flex-1">
        <div
            className="flex h-full md:h-auto w-full lg:w-2/5 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none xl:px-24">
            <div className="mx-auto w-full md:max-w-[638px] lg:max-w-[409px]">
                <div className="w-full mx-auto space-y-8">
                    {children}
                </div>
            </div>
        </div>
        <div
            className="relative hidden md:px-[46px] md:pb-[46px] lg:w-0 lg:flex-1 md:block bg-[#1e1e1e] lg:overflow-hidden">
            <Badge className="mt-8 lg:mt-[91px] mb-[17px] !font-bold">Start earning up to 90% profit</Badge>
            <p className="text-stone-400 text-base font-normal leading-normal max-w-[434px]">
                Trade smarter with MegaTrader—achieve your goals within our structured evaluation period. Unlock
                opportunities and maximize your potential in the dynamic world of trading.
            </p>
            <MegatraderScreen/>
        </div>
    </div>
}

const MegatraderScreen = () => {
    return <div className="mx-auto relative mt-[48px] w-[840px] xl:w-full h-[758px] overflow-hidden rounded-2xl">
        <div
            className="w-full h-[1200px] opacity-5 rotate-[20deg] translate-x-[520px] xl:translate-x-[700px] bg-white -top-[10px] absolute">
        </div>
        <div
            className="mx-auto w-full h-[758px] bg-[#151211] rounded-2xl shadow-[0px_30px_35px_32px_rgba(0,0,0,0.20)] overflow-hidden border-4 border-black"
        >
            <div className="flex justify-between px-2 items-center bg-black h-[48px]">
                <div className="flex gap-2 items-center justify-end">
                    <div className="bg-[#ee6a5e] shadow rounded-full w-4 h-4"></div>
                    <div className="bg-[#f5be4e] rounded-full w-4 h-4"></div>
                    <div className="bg-[#63c755] rounded-full w-4 h-4"></div>
                </div>
                <div className="flex items-center justify-center">
                    <div
                        className="self-stretch text-center justify-center text-sm font-bold leading-tight text-[#A8A29E]">https://megatrader.io
                    </div>
                </div>
                <div className="flex items-center justify-start">
                    <Image src={'/assets/images/locked.svg'} alt={'locked'} width={24} height={24}/>
                </div>
            </div>
            <div className="h-0">
                <Image
                    src="/assets/images/img_6.png"
                    alt="Trading Platform Interface"
                    width={1000}
                    height={700}
                    style={{width: '100%', height: 'auto'}}
                    quality={100}
                />
            </div>
        </div>
    </div>
}

export default Layout;