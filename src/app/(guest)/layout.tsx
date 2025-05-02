import React from "react";
import MegatraderScreen from "@/components/MegatraderScreen";

const Layout = ({children}: { children: React.ReactNode }) => {
    return (
        <div className="xl:h-dvh m-4 xl:flex xl:flex-1">
            <div
                className="flex h-full md:h-auto w-full xl:w-2/5 flex-col justify-center px-4 py-12 sm:px-6 xl:flex-none xl:px-24">
                <div className="mx-auto w-full md:max-w-[638px] lg:max-w-[409px]">
                    <div className="w-full mx-auto space-y-8">{children}</div>
                </div>
            </div>
            <div
                className="relative hidden rounded-2xl h-dvh xl:h-auto md:px-[52px] xl:w-0 lg:flex-1 md:block bg-[#1e1e1e] lg:overflow-hidden">
                <div className="h-full pt-[60px]">
                    <MegatraderScreen/>
                </div>
            </div>
        </div>
    );
};

export default Layout;
