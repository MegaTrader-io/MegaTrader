import {PayoutsEntry} from "@/commons/interfaces";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/Table";
import React, {useCallback, useEffect, useState} from "react";
import {formatCurrency, sleep} from "@/commons/utils";
import BadgePendingOrPaid from "@/components/BadgePendingOrPaid";
import {Pagination, PaginationList, PaginationPage} from "@/components/Pagination";
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/16/solid";
import clsx from "clsx";
import ArrowDown, {directionType} from "@/components/ArrowDown";
import PaymentMethodImage from "@/app/(backoffice)/payouts/_components/PaymentMethodImage";

const PayoutsTable = () => {
    const [sortBy, setSortBy] = useState<string>('month');
    const [direction, setDirection] = useState<directionType>('desc');
    const [loading, setLoading] = useState(false)
    const [data, setData] = useState<PayoutsEntry[]>([]);
    const [currentPage, setCurrentPage] = useState(1);
    const limitPerPage = 7;

    const [pagination, setPagination] = useState({
        current_page: currentPage,
        per_page: limitPerPage,
        total: 0,
        last_page: 0,
    });

    const fetchPayoutsData = useCallback(async () => {
        try {
            setLoading(true);
            await sleep(200);
            const response = await fetch(`/api/affiliates/payouts?page=${currentPage}&per_page=${limitPerPage}&sortBy=${sortBy}&direction=${direction}`);
            if (!response.ok) {
                throw new Error(`unable to fetch the end point: ${response.statusText}`);
            }
            const result = await response.json();
            setData(result.data);
            setPagination(result.meta);
        } catch (error) {
            console.error("error:", error);
        } finally {
            setLoading(false);
        }
    }, [currentPage, sortBy, direction]);

    useEffect(() => {
        void fetchPayoutsData();
    }, [fetchPayoutsData]);

    const handlePageChange = (page: number) => {
        setCurrentPage(page);
    };

    const handlerSortBy = (sortBy: string) => {
        setDirection(direction === 'desc' ? 'asc' : 'desc');
        setSortBy(sortBy)
    }

    return (
        <div className="overflow-x-auto">
            <Table>
                <TableHead className="!text-base">
                    <TableRow className="text-white">
                        <TableHeader className="!text-base">
                            <div className="min-h-6 flex gap-2 items-center cursor-pointer select-none"
                                 onClick={() => {
                                     handlerSortBy('month')
                                 }}>
                                <div>
                                    Month
                                </div>
                                <div>
                                    {sortBy === 'month' && <ArrowDown direction={direction}/>}
                                </div>
                            </div>
                        </TableHeader>
                        <TableHeader className='!text-base'>Sold</TableHeader>
                        <TableHeader className='!text-base !w-[95px]'>Payment method</TableHeader>
                        <TableHeader className="!text-base">
                            <div className="min-h-6 flex gap-2 justify-end items-center cursor-pointer select-none"
                                 onClick={() => {
                                     handlerSortBy('total_profit')
                                 }}>
                                <div>
                                    Total Profit
                                </div>
                                <div>
                                    {sortBy === 'total_profit' && <ArrowDown direction={direction}/>}
                                </div>
                            </div>
                        </TableHeader>
                        <TableHeader className="text-right !text-base">Status</TableHeader>
                    </TableRow>
                </TableHead>
                <TableBody className="p-0">
                    {loading && Array(limitPerPage).fill('1').map((_, index) => (
                        <TableRow key={index}>
                            <TableCell
                                colSpan={4}
                                className="h-[65px] animate-pulse bg-[#1e1e1e]/70 text-center font-bold w-full text-zinc-400">
                                <div className="bg-slate-800/70 w-full h-full"></div>
                            </TableCell>
                        </TableRow>
                    ))}
                    {!loading && data.map((entry) => (
                        <TableRow key={entry.id} className="text-stone-400 text-base font-normal">
                            <TableCell className="py-4">{entry.month}</TableCell>
                            <TableCell className="py-4">{entry.sold}</TableCell>
                            <TableCell className="py-4"><PaymentMethodImage paymentMethod={entry.paymentMethod} /></TableCell>
                            <TableCell className="py-4 text-right">
                                {formatCurrency(entry.total_profit)}
                            </TableCell>
                            <TableCell className="py-4 text-right">
                                <BadgePendingOrPaid status={entry.status}/>
                            </TableCell>
                        </TableRow>
                    ))}
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
        </div>
    );
};

export default PayoutsTable;
