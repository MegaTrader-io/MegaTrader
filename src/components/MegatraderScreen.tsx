'use client'

import React, {useEffect, useRef} from "react";
import Image from "next/image";

const MegatraderScreen = () => {
    const windowExampleRef = useRef<HTMLInputElement | null>(null);

    useEffect(() => {
        const handlerResize = () => {
            console.info(handlerResize);
        }

        const observer = new ResizeObserver(handlerResize);

        return () => {
            observer.disconnect();
        }
    }, []);

    return <div ref={windowExampleRef}
                className="mx-auto relative mt-[48px] xl:w-full h-full overflow-hidden rounded-2xl">
        <div
            className="mx-auto w-full h-[calc(100vh-326px)] rounded-2xl shadow-lg overflow-hidden border-4 border-black"
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

export default MegatraderScreen;