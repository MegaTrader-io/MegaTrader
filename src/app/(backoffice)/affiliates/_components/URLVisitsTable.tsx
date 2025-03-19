import {VisitDataEntry} from "@/commons/interfaces";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/Table";
import React, {useCallback, useEffect, useState} from "react";
import {sleep} from "@/commons/utils";
import {Pagination, PaginationList, PaginationPage} from "@/components/Pagination";
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/16/solid";
import clsx from "clsx";
import {CheckCircleIcon, XCircleIcon} from "@heroicons/react/20/solid";

function ConvertedIcon({converted}: { converted: boolean }) {
    return <>
        {
            converted ? (
                <CheckCircleIcon className={'fill-[#2DD4BF] w-5 h-5'}/>
            ) : (
                <XCircleIcon className={'fill-[#F43F5E] stroke-1 w-5 h-5'}/>
            )
        }

    </>
}

const URLVisitsTable = () => {
    const [loading, setLoading] = useState(false)
    const [data, setData] = useState<VisitDataEntry[]>([]);
    const [currentPage, setCurrentPage] = useState(1);
    const limitPerPage = 10;

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
            const response = await fetch(`/api/affiliates/url-visits?page=${currentPage}&per_page=${limitPerPage}&sortBy=id&direction=desc`);
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
    }, [currentPage]);

    useEffect(() => {
        void fetchData();
    }, [fetchData]);

    const handlePageChange = (page: number) => {
        setCurrentPage(page);
    };

    return (
        <div className="overflow-x-auto">
            <Table>
                <TableHead>
                    <TableRow className="text-white">
                        <TableHeader className="!text-sm">URL</TableHeader>
                        <TableHeader className="!text-sm">Referring URL</TableHeader>
                        <TableHeader className="!text-sm">Converted</TableHeader>
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
                        <TableRow key={entry.id} className="text-stone-400 text-sm font-medium leading-tight">
                            <TableCell className="py-4">{entry.url}</TableCell>
                            <TableCell className="py-4">{entry.referrer}</TableCell>
                            <TableCell className="py-4">
                                <ConvertedIcon converted={entry.converted}/>
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

export default URLVisitsTable;
