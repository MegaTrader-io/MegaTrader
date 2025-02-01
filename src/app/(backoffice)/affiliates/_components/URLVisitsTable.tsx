import {CheckIcon, XMarkIcon} from "@heroicons/react/16/solid";
import {VisitDataInterface} from "@/commons/interfaces";
import Badge from "@/components/Badge";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/Table";
import React from "react";
import {Popover, PopoverPortal, PopoverTrigger} from "@radix-ui/react-popover";
import IconSurvey from "@/app/(backoffice)/account-overview/_components/IconSurvey";
import PopoverSurvey from "@/app/(backoffice)/account-overview/_components/PopoverSurvey";

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
                <TableBody>
                    {
                        visits.map((entry, index) => (
                            <TableRow key={index} className="text-stone-400 text-xs font-normal leading-tight">
                                <TableCell>{entry.url}</TableCell>
                                <TableCell>{entry.referrer}</TableCell>
                                <TableCell>
                                    <div className="py-4 flex items-center">
                                        <ConvertedIcon converted={entry.converted}/>
                                    </div>
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
