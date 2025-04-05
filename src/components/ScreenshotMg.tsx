import React from 'react';
import Image from "next/image";

function ScreenshotMg() {
    return (<div className="mx-auto relative w-[840px] xl:w-full h-auto overflow-hidden rounded-2xl">
        <div
            className="w-full h-[1200px] opacity-5 rotate-[20deg] translate-x-[520px] xl:translate-x-[700px] bg-white -top-[10px] absolute">
        </div>
        <div
            className="mx-auto w-full h-[758px] bg-[#151211] rounded-2xl shadow-[0px_30px_35px_32px_rgba(0,0,0,0.20)] overflow-hidden border-4 border-black"
        >
            <div className="flex justify-between px-2 items-center bg-black h-[48px]">
                <div className="flex gap-2 items-center justify-end">
                    <div className="bg-neutral-700 shadow rounded-full w-4 h-4"></div>
                    <div className="bg-neutral-700 rounded-full w-4 h-4"></div>
                    <div className="bg-neutral-700 rounded-full w-4 h-4"></div>
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
                    src="/assets/images/img_7.png"
                    alt="Trading Platform Interface"
                    width={1000}
                    height={700}
                    style={{width: '100%', height: 'auto'}}
                    quality={100}
                />
            </div>
        </div>
    </div>)
}

export default ScreenshotMg;