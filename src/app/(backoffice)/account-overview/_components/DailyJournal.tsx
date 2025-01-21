import React, {useEffect, useState} from 'react';
import Card from "@/components/Card";
import {Table, TableBody, TableHead, TableHeader, TableRow, TableCell} from "@/components/Table";
import Image from "next/image";
import {journalData} from "@/commons/data";
import {Pagination, PaginationList, PaginationPage} from "@/components/Pagination";
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/16/solid";
import {SymbolMarketData} from "@/commons/interfaces";

function DailyJournal() {
    const [data, setData] = useState<SymbolMarketData[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch("/api/journal");
                if (!response.ok) {
                    throw new Error("error getting market data");
                }
                const result = await response.json() as SymbolMarketData[];
                setData(result.reverse());
            } catch (err: unknown) {
                const error = err as { message: string };
                setError(error.message);
            } finally {
                setLoading(false);
            }
        };

        void fetchData();

        console.info(data);
        console.info(loading);
        console.info(error);
    }, [])

    return (
        <Card className="w-full space-y-8">
            <>
                <Table>
                    <TableHead className="text-xs">
                        <TableRow className="text-white text-right align-top">
                            <TableHeader className="text-left">Daily<br/>Journal</TableHeader>
                            <TableHeader>Date</TableHeader>
                            <TableHeader>Net P&L</TableHeader>
                            <TableHeader>P&L High</TableHeader>
                            <TableHeader>P&L Low</TableHeader>
                            <TableHeader>Total<br/>Contracts</TableHeader>
                            <TableHeader>Total Fees<br/>+Comm</TableHeader>
                            <TableHeader>Total Trades</TableHeader>
                            <TableHeader>Avg. Winning<br/>Trades</TableHeader>
                            <TableHeader>Avg. Losing<br/>Trades</TableHeader>
                            <TableHeader>Winning<br/>Trade %</TableHeader>
                            <TableHeader>Max. Consec.<br/>W/L Trades</TableHeader>
                            <TableHeader>Avg. W/L<br/>Duration</TableHeader>
                        </TableRow>
                    </TableHead>
                    <TableBody className="text-xs">
                        {journalData.map((entry) => (
                            <TableRow key={entry.id} className="text-right text-stone-400 text-xs font-normal">
                                <TableCell className="text-left">
                                    {entry.canEdit && (
                                        <button onClick={() => {
                                            console.info(entry)
                                        }}>
                                            <Image src='/assets/images/pencil.svg'
                                                   alt={'pencil'}
                                                   width={50}
                                                   height={28}/>
                                        </button>
                                    ) || '-'}
                                </TableCell>
                                <TableCell>{entry.date}</TableCell>
                                <TableCell>{entry.netPnl}</TableCell>
                                <TableCell>{entry.pnlHigh}</TableCell>
                                <TableCell>{entry.pnlLow}</TableCell>
                                <TableCell>{entry.totalContracts}</TableCell>
                                <TableCell>{entry.totalFeesComm}</TableCell>
                                <TableCell>{entry.totalTrades}</TableCell>
                                <TableCell>{entry.avgWinningTrades}</TableCell>
                                <TableCell>{entry.avgLosingTrades}</TableCell>
                                <TableCell>{entry.winningTradePercentage}%</TableCell>
                                <TableCell>{entry.maxConsecutiveWLTrades}</TableCell>
                                <TableCell>{entry.avgWLDuration}</TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>

                <Pagination
                    className="mt-6 items-center flex justify-end text-stone-400 text-xs font-normal leading-tight">
                    Showing 6/10
                    <PaginationList className="text-white flex items-center">
                        <PaginationPage
                            as={'button'}
                            className="h-7 p-1 bg-stone-800 rounded border border-neutral-700">
                            <ChevronLeftIcon className="text-white w-5 h-5 "/>
                        </PaginationPage>
                        {[1, 2]
                            .map((link, key) => (
                                <PaginationPage
                                    as={'button'}
                                    className={'w-7 h-7 px-3 py-1 bg-stone-800 rounded border border-neutral-700 justify-center items-center gap-2 inline-flex'}
                                    key={key}>
                                    {link}
                                </PaginationPage>
                            ))}
                        <PaginationPage
                            as={'button'}
                            className="h-7 p-1 bg-stone-800 rounded border border-neutral-700 justify-center items-center gap-2 inline-flex">
                            <ChevronRightIcon className="text-white w-5 h-5"/>
                        </PaginationPage>
                    </PaginationList>
                </Pagination>
            </>
        </Card>
    );
}

export default DailyJournal;
