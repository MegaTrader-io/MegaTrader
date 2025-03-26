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

const PayoutsTable = ({payoutStatus}: { payoutStatus: 'all_payouts' | 'approved' | 'pending' | 'rejected' }) => {
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

    useEffect(() => {
        setCurrentPage(1)
    }, [payoutStatus]);

    const fetchPayoutsData = useCallback(async () => {
        try {
            setLoading(true);
            await sleep(200);
            const response = await fetch(`/api/payouts?page=${currentPage}&per_page=${limitPerPage}&sortBy=${sortBy}&direction=${direction}&payoutStatus=${payoutStatus}`);
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
    }, [currentPage, sortBy, direction, payoutStatus]);

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
                    {!loading && data.length === 0 && (
                        <TableRow>
                            <TableCell
                                colSpan={6}
                                className="h-[65px] bg-[#1e1e1e]/70 text-center font-bold w-full text-zinc-400">
                                <EmptyPanel status={payoutStatus}/>
                            </TableCell>
                        </TableRow>
                    )}
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

function EmptyPanel({status}: { status: string }) {
    return (
        <div className="flex-col  w-full justify-center items-center gap-2 inline-flex">
            <div className="w-[30px] h-[30px] relative">
                <div className="w-[30px] h-[30px] left-0 top-0 absolute"></div>
                <div className="left-[2.50px] top-[2.50px]">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.75 19.25H14.25V11.75H11.75V19.25ZM13 9.25C13.3542 9.25 13.651 9.13021 13.8906 8.89062C14.1302 8.65104 14.25 8.35417 14.25 8C14.25 7.64583 14.1302 7.34896 13.8906 7.10938C13.651 6.86979 13.3542 6.75 13 6.75C12.6458 6.75 12.349 6.86979 12.1094 7.10938C11.8698 7.34896 11.75 7.64583 11.75 8C11.75 8.35417 11.8698 8.65104 12.1094 8.89062C12.349 9.13021 12.6458 9.25 13 9.25ZM13 25.5C11.2708 25.5 9.64583 25.1719 8.125 24.5156C6.60417 23.8594 5.28125 22.9688 4.15625 21.8438C3.03125 20.7188 2.14063 19.3958 1.48438 17.875C0.828125 16.3542 0.5 14.7292 0.5 13C0.5 11.2708 0.828125 9.64583 1.48438 8.125C2.14063 6.60417 3.03125 5.28125 4.15625 4.15625C5.28125 3.03125 6.60417 2.14063 8.125 1.48438C9.64583 0.828125 11.2708 0.5 13 0.5C14.7292 0.5 16.3542 0.828125 17.875 1.48438C19.3958 2.14063 20.7188 3.03125 21.8438 4.15625C22.9688 5.28125 23.8594 6.60417 24.5156 8.125C25.1719 9.64583 25.5 11.2708 25.5 13C25.5 14.7292 25.1719 16.3542 24.5156 17.875C23.8594 19.3958 22.9688 20.7188 21.8438 21.8438C20.7188 22.9688 19.3958 23.8594 17.875 24.5156C16.3542 25.1719 14.7292 25.5 13 25.5Z"
                            fill="white"/>
                    </svg>
                </div>
            </div>
            <div className="text-center text-white text-base font-medium leading-normal">
                No {status.toLowerCase()} payouts
            </div>
        </div>
    )
}

export default PayoutsTable;
