'use client'
import React, {useState} from 'react';
import {Button} from "@/components/Button";
import Card from "@/components/Card";
import {OptionInterface} from "@/commons/interfaces";
import URLVisitsTable from "@/app/(backoffice)/affiliates/_components/URLVisitsTable";
import PayoutsTable from "@/app/(backoffice)/affiliates/_components/PayoutsTable";
import IncomeTable from "@/app/(backoffice)/affiliates/_components/IncomeTable";

const Options: OptionInterface[] = [
    {id: 'url_visits', label: 'URL Visits'},
    {id: 'payouts', label: 'Payouts'},
    {id: 'income', label: 'Income'},
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
                {selection === 'url_visits' && <URLVisitsTable/>}
                {selection === 'payouts' && <PayoutsTable/>}
                {selection === 'income' && <IncomeTable/>}
            </Card>
        </div>
    )
}

export default TrafficStatsTable;