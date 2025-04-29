'use client'
import React, {useEffect, useRef} from 'react';
import Image from "next/image";

function ScreenshotMg() {
    const containerRef = useRef<HTMLDivElement | null>(null);

    useEffect(() => {
        if (!containerRef.current) return;

        let resizeTimeout: NodeJS.Timeout | null = null;

        const handleResize = () => {
            if (resizeTimeout) clearTimeout(resizeTimeout);

            resizeTimeout = setTimeout(() => {
                if (!containerRef.current) return;

                const slickList = document.querySelector('.slick-list');
                const containerHeight = containerRef.current.offsetHeight || 0;

                if (!slickList) return;

                const slickListHeight = slickList.getBoundingClientRect().height;
                const maxHeight = Math.floor(Math.max(slickListHeight, containerHeight));

                containerRef.current.style.height = `${maxHeight}px`;
            }, 250);
        };

        window.addEventListener('resize', handleResize);

        handleResize();

        return () => {
            window.removeEventListener('resize', handleResize);
            if (resizeTimeout) clearTimeout(resizeTimeout);
        };
    }, []);

    return (<div className="mx-auto relative w-[840px] xl:w-[700px] h-auto overflow-hidden rounded-2xl">
        <div
            className="w-full window-custom-shape inset-0 bg-white/[0.040] absolute">
        </div>
        <div
            ref={containerRef}
            className="container-screenshot mx-auto w-full h-[calc(100dvh-300px)] bg-[#151211] rounded-2xl shadow-[0px_30px_35px_32px_rgba(0,0,0,0.20)] overflow-hidden border-4 border-black"
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
                    src="/assets/images/screenshots/account-overview.svg"
                    alt="Account overview"
                    width={800}
                    height={766}
                    style={{width: '100%', height: 'auto'}}
                    quality={100}
                />
            </div>
        </div>
    </div>)
}

export default ScreenshotMg;