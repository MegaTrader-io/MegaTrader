import React from "react";
import MegatraderScreen from "@/components/MegatraderScreen";
import Badge from "@/components/Badge";

const Layout = ({children}: { children: React.ReactNode }) => {
    return (
        <div className="lg:h-dvh m-4 lg:flex lg:flex-1">
            <div
                className="flex h-full md:h-auto w-full lg:w-2/5 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none xl:px-24">
                <div className="mx-auto w-full md:max-w-[638px] lg:max-w-[409px]">
                    <div className="w-full mx-auto space-y-8">{children}</div>
                </div>
            </div>
            <div
                className="relative hidden rounded-2xl md:px-[52px] lg:w-0 lg:flex-1 md:block bg-[#1e1e1e] lg:overflow-hidden">
                <Badge className="mt-8 lg:mt-[51px] mb-[17px] !font-bold">Start earning up to 90% profit</Badge>
                <p className="text-stone-400 text-base font-normal leading-normal max-w-[434px] mb-[36px]">
                    Trade smarter with MegaTrader—achieve your goals within our structured evaluation period. Unlock opportunities and maximize your potential in the dynamic world of trading.
                </p>
                <MegatraderScreen/>
            </div>
        </div>
    );
};

export default Layout;
