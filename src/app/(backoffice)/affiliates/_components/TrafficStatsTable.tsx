'use client'

import React, {useState} from 'react';
import {Button} from "@/components/Button";
import Card from "@/components/Card";
import {OptionInterface} from "@/commons/interfaces";
import URLVisitsTable from "@/app/(backoffice)/affiliates/_components/URLVisitsTable";
import PayoutsTable from "@/app/(backoffice)/affiliates/_components/PayoutsTable";
import Conversions from "@/app/(backoffice)/affiliates/_components/Conversions";

const Options: OptionInterface[] = [
    {id: 'payouts', label: 'Payouts'},
    {id: 'url_visits', label: 'URL Visits'},
    {id: 'conversions', label: 'Conversions'},
]

function SelectionTab({onClick, selection}: { onClick: (option: OptionInterface) => void, selection: string }) {
    return <>
        {Options.map(option => (
            <Button key={option.id}
                    className={"!normal-case"}
                    variant={option.id === selection ? "primary" : 'dark'}
                    onClick={() => {
                        onClick(option)
                    }}>
                {option.label}
            </Button>
        ))}
    </>
}

function TrafficStatsTable() {
    const [selection, setSelection] = useState(Options[0].id);

    function clickOption(option: OptionInterface) {
        setSelection(option.id)
    }

    function changeOption(ev: React.ChangeEvent<HTMLSelectElement>) {
        setSelection(ev.target.value)
    }

    return (
        <div className="w-full space-y-2 lg:space-y-4">
            <Card id="traffic-conversion-table" className="space-y-4">
                <div className="hidden md:flex gap-2">
                    <SelectionTab onClick={clickOption} selection={selection}/>
                </div>

                <div className="md:hidden relative w-full">
                    <select
                        value={selection}
                        className="w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none"
                        onChange={changeOption}>
                        {Options.map(option => (
                            <option key={option.id} value={option.id}>
                                {option.label}
                            </option>
                        ))}
                    </select>
                    <div className="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_5269_2288" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="24" height="24">
                                <rect width="24" height="24" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_5269_2288)">
                                <path d="M12 15L7 10H17L12 15Z" fill="white"/>
                            </g>
                        </svg>
                    </div>
                </div>

                {selection === 'payouts' && <PayoutsTable/>}
                {selection === 'url_visits' && <URLVisitsTable/>}
                {selection === 'conversions' && <Conversions/>}
            </Card>
        </div>
    )
}

export default TrafficStatsTable;