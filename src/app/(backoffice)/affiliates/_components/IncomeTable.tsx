import {IncomeEntry} from "@/commons/interfaces";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/Table";
import React, {useCallback, useEffect, useState} from "react";
import {formatCurrency, sleep} from "@/commons/utils";
import {Pagination, PaginationList, PaginationPage} from "@/components/Pagination";
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/16/solid";
import clsx from "clsx";
import ArrowDown, {directionType} from "@/components/ArrowDown";

const IncomeTable = () => {
    const [sortBy, setSortBy] = useState<string>('orderNumber');
    const [direction, setDirection] = useState<directionType>('desc');
    const [loading, setLoading] = useState(false)
    const [data, setData] = useState<IncomeEntry[]>([]);
    const [currentPage, setCurrentPage] = useState(1);
    const limitPerPage = 7;

    const [pagination, setPagination] = useState({
        current_page: currentPage,
        per_page: limitPerPage,
        total: 0,
        last_page: 0,
    });

    const fetchData = useCallback(async () => {
        try {
            setLoading(true);
            await sleep(200);
            const response = await fetch(`/api/affiliates/income?page=${currentPage}&per_page=${limitPerPage}&sortBy=${sortBy}&direction=${direction}`);
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
        void fetchData();
    }, [fetchData]);

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
                <TableHead>
                    <TableRow className="text-white">
                        <TableHeader className="!text-base">
                            <div className="min-h-6 flex gap-2 items-center cursor-pointer select-none"
                                 onClick={() => {
                                     handlerSortBy('orderNumber')
                                 }}>
                                <div>
                                    Order number
                                </div>
                                <div>
                                    {sortBy === 'orderNumber' && <ArrowDown direction={direction}/>}
                                </div>
                            </div>
                        </TableHeader>
                        <TableHeader className="!text-base">Product</TableHeader>
                        <TableHeader>
                            <div className="min-h-6 flex gap-2 items-center cursor-pointer select-none"
                                 onClick={() => {
                                     handlerSortBy('created')
                                 }}>
                                <div>
                                    Created
                                </div>
                                <div>
                                    {sortBy === 'created' && <ArrowDown direction={direction}/>}
                                </div>
                            </div>
                        </TableHeader>
                        <TableHeader className="!text-base">
                            <div className="min-h-6 flex gap-2 items-center cursor-pointer select-none"
                                 onClick={() => {
                                     handlerSortBy('paymentDate')
                                 }}>
                                <div>
                                    Payment Date
                                </div>
                                <div>
                                    {sortBy === 'paymentDate' && <ArrowDown direction={direction}/>}
                                </div>
                            </div>
                        </TableHeader>
                        <TableHeader className="!text-base">
                            <div className="min-h-6 flex gap-2 justify-end items-center cursor-pointer select-none"
                                 onClick={() => {
                                     handlerSortBy('originalPrice')
                                 }}>
                                <div>
                                    Ordinal price
                                </div>
                                <div>
                                    {sortBy === 'originalPrice' && <ArrowDown direction={direction}/>}
                                </div>
                            </div>
                        </TableHeader>
                        <TableHeader className="!text-base">
                            <div className="min-h-6 flex gap-2 justify-end items-center cursor-pointer select-none"
                                 onClick={() => {
                                     handlerSortBy('profit')
                                 }}>
                                <div>
                                    Profit
                                </div>
                                <div>
                                    {sortBy === 'profit' && <ArrowDown direction={direction}/>}
                                </div>
                            </div>
                        </TableHeader>
                    </TableRow>
                </TableHead>
                <TableBody className="p-0">
                    {loading && Array(limitPerPage).fill('1').map((_, index) => (
                        <TableRow key={index}>
                            <TableCell
                                colSpan={6}
                                className="h-[65px] animate-pulse bg-[#1e1e1e]/70 text-center font-bold w-full text-zinc-400">
                                <div className="bg-slate-800/70 w-full h-full"></div>
                            </TableCell>
                        </TableRow>
                    ))}
                    {!loading && data.map((entry) => (
                        <TableRow key={entry.id} className="text-stone-400 !text-base font-normal">
                            <TableCell className="py-4">{entry.orderNumber}</TableCell>
                            <TableCell className="py-4">{entry.product}</TableCell>
                            <TableCell className="py-4">{entry.created}</TableCell>
                            <TableCell className="py-4">{entry.paymentDate}</TableCell>
                            <TableCell className="py-4 text-right">{formatCurrency(entry.originalPrice)}</TableCell>
                            <TableCell className="py-4 text-right">{formatCurrency(entry.profit)}</TableCell>
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

export default IncomeTable;
