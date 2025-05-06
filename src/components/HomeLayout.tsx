'use client'

import React, {useState, useEffect, useRef} from "react";
import MegatraderScreen from "@/components/MegatraderScreen";

const Layout = ({children}: { children: React.ReactNode }) => {
    const [height, setHeight] = useState<string>('auto');
    const divRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const updateHeight = () => {
            if (window.innerWidth >= 767 && window.innerWidth <= 1280) {
                if (!divRef.current) {
                    return;
                }

                const divHeight = divRef.current.offsetHeight;
                const calculatedHeight = `calc(100dvh - ${divHeight + 32}px)`;

                if (window.innerHeight - divRef.current.offsetHeight - 60 - 32 < 865) {
                    setHeight('unset');
                    return;
                }

                setHeight(calculatedHeight);
            } else {
                setHeight('unset');
            }
        };

        updateHeight();

        window.addEventListener("resize", updateHeight);

        return () => {
            window.removeEventListener("resize", updateHeight);
        };
    }, []);

    return (
        <div className="m-4 xl:max-h-[calc(100dvh-32px)] xl:h-dvh xl:flex xl:flex-row flex-col">
            <div
                ref={divRef}
                className="mg-divRef flex h-full w-full flex-col justify-center px-4 sm:py-12 sm:pr-6 md:h-auto lg:min-h-auto xl:w-2/5 xl:flex-none xl:pr-4 2xl:px-24">
                <div className="mx-auto w-full">
                    {children}
                </div>
            </div>
            <div
                style={{height}}
                className="mg-height relative hidden rounded-2xl pt-[60px] xl:pb-[32px] h-full lg:min-h-auto xl:h-auto md:px-[52px] lg:flex-1 md:block bg-[#1e1e1e] lg:overflow-hidden xl:flex flex-col">
                <div
                    className="content-center h-full flex items-center min-h-[calc(50dvh-60px-16px-32px)] justify-center flex-grow">
                    <div className="h-full w-full content-center">
                        <MegatraderScreen/>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Layout;


