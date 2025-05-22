import {PayoutsEntry} from "@/commons/interfaces";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/Table";
import React, {useCallback, useEffect, useState} from "react";
import {formatCurrency, sleep} from "@/commons/utils";
import BadgePendingOrPaid from "@/components/BadgePendingOrPaid";
import {Pagination, PaginationList, PaginationPage} from "@/components/Pagination";
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/16/solid";
import clsx from "clsx";
import {directionType} from "@/components/ArrowDown";
import PaymentMethodImage from "@/app/(backoffice)/payouts/_components/PaymentMethodImage";

const PayoutsTable = () => {
    const [sortBy] = useState<string>('id');
    const [direction] = useState<directionType>('asc');
    const [loading, setLoading] = useState(false)
    const [data, setData] = useState<PayoutsEntry[]>([]);
    const [currentPage, setCurrentPage] = useState(1);
    const limitPerPage = 10;

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
            const response = await fetch(`/api/refferals/payouts?page=${currentPage}&per_page=${limitPerPage}&sortBy=${sortBy}&direction=${direction}`);
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

    return (
        <div className="overflow-x-auto">
            <Table>
                <TableHead>
                    <TableRow className="text-white">
                        <TableHeader className='!text-sm'>Request ID</TableHeader>
                        <TableHeader className='!text-sm'>Date</TableHeader>
                        <TableHeader className="!text-sm">Status</TableHeader>
                        <TableHeader className="!text-sm">Company/Beneficiary</TableHeader>
                        <TableHeader className='!text-sm !w-[95px]'>Payment method</TableHeader>
                        <TableHeader className="text-right !text-sm">Amount</TableHeader>
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
                        <TableRow key={entry.id} className="text-stone-400 text-sm font-normal">
                            <TableCell className="py-4">#{entry.id}</TableCell>
                            <TableCell className="py-4">{entry.date}</TableCell>
                            <TableCell className="py-4">
                                <BadgePendingOrPaid status={entry.status}/>
                            </TableCell>
                            <TableCell className="py-4">{entry.company}</TableCell>
                            <TableCell className="py-4">
                                <PaymentMethodImage
                                    paymentMethod={entry.paymentMethod}/>
                            </TableCell>
                            <TableCell className="py-4 text-right">
                                {formatCurrency(entry.amount)}
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>

            {data.length > 0 && (<Pagination
                className="mt-6 items-center flex justify-end text-stone-400 text-xs font-normal leading-tight">
                {pagination.total >= limitPerPage ? `Showing ${pagination.per_page} of ${pagination.total}` : null}
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
