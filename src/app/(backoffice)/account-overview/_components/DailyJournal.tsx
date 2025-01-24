import React, {useEffect, useState} from 'react';
import Card from "@/components/Card";
import {Table, TableBody, TableHead, TableHeader, TableRow, TableCell} from "@/components/Table";
import {Pagination, PaginationList, PaginationPage} from "@/components/Pagination";
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/16/solid";
import clsx from "clsx";
import {JournalEntry} from "@/commons/interfaces";
import {sleep} from "@/commons/utils";
import {PopoverTrigger, Popover, PopoverPortal} from "@radix-ui/react-popover";
import PopoverSurvey from "@/app/(backoffice)/account-overview/_components/PopoverSurvey";
import IconSurvey from "@/app/(backoffice)/account-overview/_components/IconSurvey";

function DailyJournal() {
    const [loading, setLoading] = useState(false)
    const [data, setData] = useState<JournalEntry[]>([]);
    const [currentPage, setCurrentPage] = useState(1);
    const limitPerPage = 7;

    const [pagination, setPagination] = useState({
        current_page: currentPage,
        per_page: limitPerPage,
        total: 0,
        last_page: 0,
    });

    const fetchJournalData = async (page = 1 as number) => {
        setCurrentPage(page)
        setLoading(true)
        await sleep(200);
        const response = await fetch(`/api/journal?page=${page}&per_page=${limitPerPage}`);
        const result = await response.json();
        setData(result.data);
        setPagination(result.meta);
        setLoading(false)
    };

    useEffect(() => {
        sleep(500).then(() => {
            void fetchJournalData();
        })
    }, []);

    const handlePageChange = (page: number) => {
        fetchJournalData(page)
            .finally(() => {

            })
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
                        {loading && Array(limitPerPage).fill('1').map((_, index) => (
                            <TableRow key={index}>
                                <TableCell
                                    colSpan={13}
                                    className="h-[65px] animate-pulse bg-[#1e1e1e]/70 text-center font-bold w-full text-zinc-400">
                                    <div className="bg-slate-800/70 w-full h-full"></div>
                                </TableCell>
                            </TableRow>
                        ))}
                        {!loading && data.map((entry) => (
                            <TableRow key={entry.id} className="text-right text-stone-400 text-xs font-normal">
                                <TableCell className="text-left">


                                    <Popover>
                                        <PopoverTrigger asChild>
                                            <button onClick={() => {
                                                console.info(entry)
                                            }}>
                                                <IconSurvey canEdit={!!entry.survey?.id} />
                                            </button>
                                        </PopoverTrigger>
                                        <PopoverPortal>
                                            <PopoverSurvey surveyData={entry.survey}/>
                                        </PopoverPortal>
                                    </Popover>
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
                        {!loading && data.length === 0 && (
                            <TableRow>
                                <TableCell
                                    colSpan={13}
                                    className="text-center font-bold w-full text-zinc-400">
                                    NO DATA TO DISPLAY
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>

                {data.length > 0 && (<Pagination
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
                                    'bg-stone-950': currentPage === page
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
                </Pagination>)}
            </>
        </Card>
    );
}

export default DailyJournal;
