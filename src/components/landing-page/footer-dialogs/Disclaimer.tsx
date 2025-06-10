'use client';

import React, {useState} from 'react';
import clsx from "clsx";
import Select from "@/components/Select";

interface TabOption {
    id: string,
    title: string
}

const ITEMS: TabOption[] = [
    {id: 'general-disclosure', title: 'General Disclosure'},
    {id: 'risk-disclosure', title: 'Risk Disclosure'},
    {id: 'hypothetical-performance-disclosure', title: 'Hypothetical Performance Disclosure'},
    {id: 'customer-compensation-disclosure', title: 'Customer Compensation Disclosure'},
    {id: 'cftc-rule-4-41-compliance-notice', title: 'CFTC Rule 4.41 Compliance Notice'}
]

function Disclaimer() {
    const [tab, setTab] = useState<TabOption>(ITEMS[0]);

    function changeTab(tab: TabOption) {
        setTab(tab);
    }

    function onChange(e: React.ChangeEvent<HTMLSelectElement>) {
        const value = e.target.value;
        const tabSelected = ITEMS.find(tab => tab.id === value);
        if (!tabSelected) {
            return;
        }

        changeTab(tabSelected);
    }

    return (
        <div className='md:grid md:grid-cols-[300px_32px_1fr] my-1'>
            <div className="h-full">
                <div className="space-y-4 hidden md:block">
                    {ITEMS.map((item, index) => (
                        <button
                            key={index}
                            onClick={() => changeTab(item)}
                            className={clsx('w-full text-left tracking-tight leading-normal relative', [
                                tab.id === item.id ?
                                    'text-[#ffd78a] font-medium scroll-bar' : 'text-stone-400'
                            ])}>
                            {item.title}
                        </button>
                    ))}
                </div>

                <div className="block md:hidden">
                    <Select value={tab.id} onChange={onChange}>
                        {ITEMS.map((item, index) => (
                            <option key={index} value={item.id}>{item.title}</option>
                        ))}
                    </Select>
                </div>
            </div>
            <div className="flex justify-center">
                <div
                    className="sm:border-r-[1px] sm:border-r-[#404040] mb-8 w-full h-full md:w-0 md:my-0 "></div>
            </div>
            <div className="text-white space-y-12 lg:mx-4">
                <div className="space-y-4">
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7"
                        id='introduction'>Introduction</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        The materials and content provided by MegaTrader Holdings LLC (“MegaTrader”)—whether on our
                        website, in documents, or through any communications—are for general informational purposes
                        only. Nothing presented constitutes investment advice, an offer to buy or sell securities, or a
                        recommendation of any financial instrument or product. This information is not tailored to any
                        specific jurisdiction and should not be interpreted as legal, financial, or trading advice. Use
                        of this content in a manner that violates local laws or regulations is strictly prohibited.
                        MegaTrader Holdings LLC is a registered U.S. entity and complies with all applicable local,
                        state, and federal laws. © 2025 MegaTrader. All rights reserved.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7"
                        id='risk-disclosure'>Risk Disclosure</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Trading in simulated financial markets carries inherent risk. The MegaTrader platform provides a
                        demo trading environment with virtual funds and does not expose users to actual financial risk;
                        however, it does aim to mirror the behavior of live markets for evaluation purposes. Any actual
                        trading in real markets involves significant risk, including the potential loss of all capital.
                        Only those with sufficient financial understanding and risk capacity should engage in trading.
                        Past simulated performance does not guarantee future results.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7"
                        id='hypothetical-performance-disclosure'>Hypothetical Performance Disclosure</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Any performance results shown or referenced on this platform are based on hypothetical or
                        simulated data and should not be interpreted as actual trading results. Simulated trading does
                        not involve real money, and thus, unlike real trading, it does not reflect real market
                        conditions such as liquidity or slippage. Testimonials and promotional content do not reflect
                        all users{'\\'} experiences and do not predict future success.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7"
                        id='customer-compensation-disclosure'>Customer Compensation Disclosure</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        All examples and figures presented as part of the MegaTrader program—including funded account
                        structures and profit share examples—are for illustrative purposes only. They are hypothetical
                        in nature and are not guarantees of results or compensation. No representation is made that any
                        account will or is likely to achieve profits or losses similar to those shown.
                    </p>
                </div>
                <div className="space-y-4">
                    <h3 className="title-dialog self-stretch justify-start text-white text-2xl font-medium uppercase leading-7"
                        id='cftc-rule-4-41-compliance-notice'>CFTC Rule 4.41 Compliance Notice</h3>
                    <p className="self-stretch justify-start text-stone-400 text-base font-medium leading-normal">
                        Pursuant to CFTC Rule 4.41, any simulated or hypothetical performance results presented on this
                        website or in marketing communications have inherent limitations. Unlike actual performance
                        records, simulated results do not represent actual trading. Because trades have not been
                        executed, results may have under- or over-compensated for the impact of certain market factors.
                        No representation is being made that any account will or is likely to achieve profits or losses
                        similar to those being shown.
                    </p>
                </div>
            </div>
        </div>
    );
}

export default Disclaimer;