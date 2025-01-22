import React, {useEffect, useState} from 'react';
import Card from "@/components/Card";
import {Table, TableBody, TableHead, TableHeader, TableRow, TableCell} from "@/components/Table";
import Image from "next/image";
import {Pagination, PaginationList, PaginationPage} from "@/components/Pagination";
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/16/solid";
import clsx from "clsx";
import {JournalEntry} from "@/commons/interfaces";

function DailyJournal() {
    const [data, setData] = useState<JournalEntry[]>([]);

    const [pagination, setPagination] = useState({
        current_page: 1,
        per_page: 10,
        total: 0,
        last_page: 0,
    });

    const fetchJournalData = async (page = 1 as number) => {
        const response = await fetch(`/api/journal?page=${page}&per_page=7`);
        const result = await response.json();
        setData(result.data);
        setPagination(result.meta);
    };

    useEffect(() => {
        void fetchJournalData();
    }, []);

    const handlePageChange = (page: number) => {
        void fetchJournalData(page);
    };

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
                        {data.map((entry) => (
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
                                <TableCell className="whitespace-pre-wrap w-0">{entry.avgWLDuration}</TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>

                <Pagination
                    className="mt-6 items-center flex justify-end text-stone-400 text-xs font-normal leading-tight">
                    Showing {pagination.per_page} of {pagination.total}
                    <PaginationList className="text-white flex items-center">
                        <PaginationPage
                            as={'button'}
                            className="h-7 p-1 bg-stone-800 rounded border border-neutral-700"
                            onClick={() => handlePageChange(pagination.current_page - 1)}
                            disabled={pagination.current_page === 1}>
                            <ChevronLeftIcon className="text-white w-5 h-5 "/>
                        </PaginationPage>
                        {Array.from({length: pagination.last_page}, (_, i) => i + 1).map((page) => (
                            <PaginationPage
                                as={'button'}
                                className={clsx('w-7 h-7 px-3 py-1 bg-stone-800 rounded border border-neutral-700 justify-center items-center gap-2 inline-flex', {
                                    'bg-stone-950': pagination.current_page === page
                                })}
                                key={page}
                                onClick={() => handlePageChange(page)}>
                                {page}
                            </PaginationPage>
                        ))}
                        <PaginationPage
                            as={'button'}
                            className="h-7 p-1 bg-stone-800 rounded border border-neutral-700 justify-center items-center gap-2 inline-flex"
                            onClick={() => handlePageChange(pagination.current_page + 1)}
                            disabled={pagination.current_page === pagination.last_page}>
                            <ChevronRightIcon className="text-white w-5 h-5"/>
                        </PaginationPage>
                    </PaginationList>
                </Pagination>
            </>
        </Card>
    );
}

export default DailyJournal;
