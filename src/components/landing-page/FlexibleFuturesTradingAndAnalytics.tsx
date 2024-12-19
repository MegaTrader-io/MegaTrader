import React, {useState} from "react";
import {CheckCircleIcon} from "@heroicons/react/16/solid";
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from "@headlessui/react";
import Link from "@/components/link";
import Image from "next/image";

interface Option {
    id: number
    title: string
    component: React.ReactElement | null;
}

function FinanceAndTrading() {
    return (
        <div>
            <h2 className="text-white">
                Automate & scale trading operations with ease.
            </h2>
        </div>
    )
}

const Options: Option[] = [
    {
        id: 1,
        title: 'Finance & Trading',
        component: <FinanceAndTrading/>
    },
    {
        id: 2,
        title: 'Risk Management',
        component: <FinanceAndTrading/>
    },
    {
        id: 3,
        title: 'Compliance & Reporting',
        component: <FinanceAndTrading/>
    },
    {
        id: 4,
        title: 'Founders Dashboard',
        component: <FinanceAndTrading/>
    },
    {
        id: 5,
        title: 'Accounts & Contracts',
        component: <FinanceAndTrading/>
    }
]


const FlexibleFuturesTradingAndAnalytics = () => {
    const [selectedIndex, setSelectedIndex] = useState(0)

    return (
        <section className="py-8 w-full px-16 bg-gradient-to-b from-[#1e1e1e] to-[#131210] rounded-2xl">
            <h2 className="text-5xl text-white text-center font-light leading-[60px]">
                FLEXIBLE FUTURES<br/>
                <span className="text-[#ffb34a] leading-[60px]">TRADING AND ANALYTICS</span>
            </h2>
            <div>
                <TabGroup onChange={setSelectedIndex}>
                    <TabList className="flex gap-2 py-8">
                        {Options.map((option, index) => (
                            <Tab
                                key={option.id}
                                className="text-left py-3 px-4 text-stone-400 w-full rounded-xl border border-stone-400  focus:outline-none data-[selected]:bg-[#f1a035] data-[selected]:border-transparent data-[selected]:text-black "
                            >
                                <div className="flex gap-2 items-center font-normal text-nowrap leading-6 text-base">
                                    {selectedIndex === index &&
                                        <CheckCircleIcon aria-hidden="true"
                                                         className="w-6 h-6 text-black"/>} {option.title}
                                </div>
                            </Tab>
                        ))}
                    </TabList>
                    <TabPanels
                        className="h-[396px] w-full bg-[#1e1e1e] rounded-2xl shadow-[0px_20px_20px_20px_rgba(0,0,0,0.10)] justify-start items-center inline-flex overflow-hidden">
                        <TabPanel className="grid grid-cols-[511px_1fr] items-center">
                            <div className="p-8 space-y-8">
                                <h2 className="text-white text-2xl font-light uppercase leading-7">AUTOMATE & SCALE
                                    TRADING OPERATIONS WITH EASE.</h2>
                                <p className="text-stone-400 text-xl font-normal leading-8">
                                    Track trades, payouts, and performance in real-time. Integrate fiat and crypto
                                    transactions for efficient futures trading.
                                </p>

                                <Link href="#" className="bg-[#ffb34a] !text-black">
                                    GET STARTED
                                </Link>
                            </div>
                            <div>
                                <Image
                                    src="/assets/images/frame_monitor.svg"
                                    alt="AUTOMATE & SCALE TRADING OPERATIONS WITH EASE."
                                    width={641}
                                    height={396}
                                />
                            </div>
                        </TabPanel>
                        <TabPanel className="text-white">Content 2</TabPanel>
                        <TabPanel className="text-white">Content 3</TabPanel>
                        <TabPanel className="text-white">Content 4</TabPanel>
                        <TabPanel className="text-white">Content 5</TabPanel>
                    </TabPanels>
                </TabGroup>


            </div>
        </section>
    )
}

export default FlexibleFuturesTradingAndAnalytics;