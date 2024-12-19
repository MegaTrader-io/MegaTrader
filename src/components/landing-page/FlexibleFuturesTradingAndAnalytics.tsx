import React, {useState} from "react";
import {CheckCircleIcon} from "@heroicons/react/16/solid";
import {Disclosure, DisclosureButton, DisclosurePanel, Tab, TabGroup, TabList} from "@headlessui/react";

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
            <h2 className="text-5xl text-white text-center mb-8 font-light leading-[60px]">
                FLEXIBLE FUTURES<br/>
                <span className="text-[#ffb34a] leading-[60px]">TRADING AND ANALYTICS</span>
            </h2>
            <div>
                <TabGroup onChange={setSelectedIndex}>
                    <TabList className="flex gap-2 group">
                        {Options.map((option, index) => (
                            <Tab
                                key={option.id}
                                className="text-left py-3 px-4 text-white w-full rounded-xl border border-stone-400  focus:outline-none data-[selected]:bg-[#f1a035] data-[selected]:border-transparent data-[selected]:text-black "
                            >
                                <div className="flex gap-2 items-center font-normal text-nowrap leading-6 text-base">
                                    {selectedIndex === index &&
                                        <CheckCircleIcon aria-hidden="true" className="w-6 h-6 text-black"/>} {option.title}
                                </div>
                            </Tab>
                        ))}
                    </TabList>
                </TabGroup>
            </div>
        </section>
    )
}

export default FlexibleFuturesTradingAndAnalytics;