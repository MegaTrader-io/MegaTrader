import {CheckIcon, XMarkIcon} from "@heroicons/react/16/solid";
import {PayoutsEntry} from "@/commons/interfaces";
import Badge from "@/components/Badge";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/Table";
import React, {useEffect, useState} from "react";
import {formatCurrency, sleep} from "@/commons/utils";
import BadgePendingOrPaid from "@/components/BadgePendingOrPaid";

function ConvertedIcon({converted}: { converted: boolean }) {
    return <>
        {
            converted ? (
                <Badge size={'md'} shape={'pill'}>
                    <CheckIcon className="h-4 w-4 text-black"/>
                </Badge>
            ) : (
                <Badge variant={'error'} shape={'pill'}>
                    <XMarkIcon className="h-4 w-4 text-black"/>
                </Badge>
            )
        }
    </>
}

const PayoutsTable = () => {
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

    console.info('pagination', pagination);

    const fetchPayoutsData = async (page = 1 as number) => {
        setCurrentPage(page)
        setLoading(true)
        await sleep(200);
        const response = await fetch(`/api/payouts?page=${page}&per_page=${limitPerPage}`);
        const result = await response.json();
        setData(result.data);
        setPagination(result.meta);
        setLoading(false)
    };

    useEffect(() => {
        sleep(500)
            .then(() => {
                void fetchPayoutsData();
            })
    }, []);

    // const handlePageChange = (page: number) => {
    //     fetchPayoutsData(page)
    //         .finally(() => {
    //
    //         })
    // };

    return (
        <div className="overflow-x-auto">
            <Table>
                <TableHead className="text-xs">
                    <TableRow className="text-white">
                        <TableHeader>Month</TableHeader>
                        <TableHeader>Sold</TableHeader>
                        <TableHeader className="text-right">Total Profit</TableHeader>
                        <TableHeader className="text-right">Status</TableHeader>
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
                        <TableRow key={entry.id} className="text-stone-400 text-xs font-normal">
                            <TableCell className="py-4">{entry.month}</TableCell>
                            <TableCell className="py-4">{entry.sold}</TableCell>
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
        </div>
    );
};

export default PayoutsTable;
