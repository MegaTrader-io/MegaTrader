import React from "react";
import MegatraderScreen from "@/components/MegatraderScreen";

const Layout = ({children}: { children: React.ReactNode }) => {
    return (
        <div className="m-4 xl:max-h-[calc(100dvh-32px)] xl:h-dvh xl:flex xl:flex-row flex-col">
            <div
                className="flex lg:min-h-auto h-full md:h-auto w-full xl:w-2/5 flex-col justify-center px-4 py-12 sm:px-6 xl:flex-none xl:px-24">
                <div className="mx-auto w-full md:max-w-[638px] lg:max-w-[409px]">
                    <div className="w-full mx-auto space-y-8">{children}</div>
                </div>
            </div>
            <div
                className="relative hidden rounded-2xl pt-[60px] xl:pb-[32px] h-full lg:min-h-auto xl:h-auto md:px-[52px] lg:flex-1 md:block bg-[#1e1e1e] lg:overflow-hidden xl:flex flex-col">
                <div className="flex items-center min-h-[calc(50dvh-60px-16px-32px)] justify-center flex-grow">
                    <div className="h-full w-full content-center">
                        <MegatraderScreen/>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Layout;


