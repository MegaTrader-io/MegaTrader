import React from "react";
import Badge from "@/components/Badge";
import Image from "next/image";

const Layout = ({children,}: {
    children: React.ReactNode
}) => {
    return <div className="flex flex-1">
        <div className="flex w-full lg:w-2/5 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none xl:px-24">
            <div className="mx-auto w-full max-w-[409px]">
                <div className="w-full mx-auto space-y-8">
                    {children}
                </div>
            </div>
        </div>
        <div className="relative hidden w-0 flex-1 lg:block bg-[#1e1e1e] pl-[120px] overflow-hidden">
            <Badge className="mt-[91px] mb-[17px] !font-bold">Start earning up to 90% profit</Badge>
            <p className="text-stone-400 text-base font-normal leading-normal max-w-[434px]">
                Trade smarter with MegaTrader—achieve your goals within our structured evaluation period. Unlock opportunities and maximize your potential in the dynamic world of trading.
            </p>

            <div
                className="h-full w-full mt-[111px] overflow-hidden relative bg-[#151211] rounded-tl-[36px] shadow-[0px_30px_35px_32px_rgba(0,0,0,0.20)] border-l-8 border-t-8 border-[#474b54]"
            >
                <div className="mx-auto w-10/12 h-0">
                    <Image
                        src="/assets/images/img_3.png"
                        alt="Trading Platform Interface"
                        width={1000}
                        height={700}
                        style={{width: '100%', height: 'auto'}}
                        quality={100}
                    />
                </div>
            </div>
        </div>
    </div>
}

export default Layout;