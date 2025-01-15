import Image from "next/image";
import React from "react";

export default function Tooltip({children}: { children: React.ReactNode }) {
    return <div className="relative mt-[22px]">
        <div>
            {children}
        </div>
        <Image src='/assets/images/arrow-up.svg' alt='arrow up'
               className="absolute w-[90px] h-3.5 -top-[13px] z-1" width={90} height={14}></Image>
    </div>
}