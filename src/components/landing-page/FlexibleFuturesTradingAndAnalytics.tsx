import Link from "@/components/link";
import React from "react";

interface Option {
    title: string
}

const Options: Option[] = [
    {
        title: 'Futures Trading',
    },
    {
        title: 'Risk Management',
    },
    {
        title: 'Compliance & Reporting',
    },
    {
        title: 'Founders Dashboard',
    },
    {
        title: 'Accounts & Contracts',
    }
]

const FlexibleFuturesTradingAndAnalytics = () => {
    return (
        <section className="py-8 w-full px-16 bg-gradient-to-b from-[#1e1e1e] to-[#131210] rounded-2xl">
            <h2 className="text-5xl text-white text-center mb-8 font-light leading-[60px]">
                FLEXIBLE FUTURES<br/>
                <span className="text-[#ffb34a] leading-[60px]">TRADING AND ANALYTICS</span>
            </h2>
            <div className="flex gap-2">
                {Options.map((option: Option) => {
                    return <Link key={option.title} as={"button"}
                                 className={`w-full !h-auto px-4 text-base`}>
                        {option.title}
                    </Link>
                })}
            </div>
        </section>
    )
}

export default FlexibleFuturesTradingAndAnalytics;