import {CheckIcon, XMarkIcon} from "@heroicons/react/16/solid";
import {VisitDataInterface} from "@/commons/interfaces";
import Badge from "@/components/Badge";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/Table";
import React from "react";

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


const URLVisitsTable = ({visits}: { visits: VisitDataInterface[] }) => {
    return (
        <div className="overflow-x-auto">
            <Table>
                <TableHead className="text-xs">
                    <TableRow className="text-white">
                        <TableHeader>URL</TableHeader>
                        <TableHeader>Referring URL</TableHeader>
                        <TableHeader>Converted</TableHeader>
                    </TableRow>
                </TableHead>
                <TableBody className="p-0">
                    {
                        visits.map((entry, index) => (
                            <TableRow key={index} className="text-stone-400 text-xs font-normal leading-tight">
                                <TableCell className="py-4">{entry.url}</TableCell>
                                <TableCell className="py-4">{entry.referrer}</TableCell>
                                <TableCell className="py-4">
                                    <ConvertedIcon converted={entry.converted}/>
                                </TableCell>
                            </TableRow>
                        ))
                    }
                </TableBody>
            </Table>
        </div>
    );
};

export default URLVisitsTable;
