'use client'
import React, {useState} from 'react';
import {Button} from "@/components/Button";
import Card from "@/components/Card";
import {OptionInterface, VisitDataInterface} from "@/commons/interfaces";
import URLVisitsTable from "@/app/(backoffice)/affiliates/_components/URLVisitsTable";

const Options: OptionInterface[] = [
    {id: 'url_visits', label: 'Overview'},
    {id: 'payouts', label: 'Payouts'},
    {id: 'income', label: 'Income'},
]

const visitsData: VisitDataInterface[] = [
    {url: "http://axc.deviuco.a/", referrer: "Direct traffic", converted: true},
    {url: "http://axc.deviuco.a/", referrer: "Direct traffic", converted: true},
    {url: "http://axc.deviuco.a/", referrer: "Direct traffic", converted: false},
    {url: "http://axc.deviuco.a/", referrer: "Direct traffic", converted: true},
];

function SelectionTab({onClick, selection}: { onClick: (option: OptionInterface) => void, selection: string }) {
    return <>
        {Options.map(option => (
            <Button key={option.id}
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

    function changeSelection(option: OptionInterface) {
        setSelection(option.id)
    }

    return (
        <div className="w-full space-y-2 lg:space-y-4">
            <div className="hidden lg:flex gap-2">
                <SelectionTab onClick={changeSelection} selection={selection}/>
            </div>

            <Card className="space-y-4">
                <div className="flex lg:hidden gap-2">
                    <SelectionTab onClick={changeSelection} selection={selection}/>
                </div>
                <URLVisitsTable visits={visitsData}/>
            </Card>
        </div>
    )
}

export default TrafficStatsTable;