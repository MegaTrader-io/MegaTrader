import React, {useState} from 'react';
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/Table";
import ArrowDown, {directionType} from "@/components/ArrowDown";
import Badge from "@/components/Badge";
import {formatCurrency, formatDateTime} from "@/commons/utils";
import NumericStyle from "@/components/NumericStyle";

const records = [
    {
        id: 1,
        createdAt: '2022-07-31',
        status: 'pending',
        charges: 910.00,
        refunds: 0.00,
        fees: -910.00,
        total: 546.00,
    },
    {
        id: 2,
        createdAt: '2022-07-30',
        status: 'paid',
        charges: 910.00,
        refunds: -910.00,
        fees: 0.00,
        total: 546.00,
    },
]

function QuickActionsTable() {
    const [direction, setDirection] = useState<directionType>('desc')

    return (
        <div className="overflow-x-auto">
            <Table>
                <TableHead className="text-xs">
                    <TableRow className="text-white">
                        <TableHeader>
                            <div className="min-h-6 flex gap-2 items-center cursor-pointer select-none"
                                 onClick={() => {
                                     setDirection(direction === 'desc' ? 'asc' : 'desc');
                                 }}>
                                <div>
                                    Date
                                </div>
                                <div>
                                    <ArrowDown direction={direction}/>
                                </div>
                            </div>
                        </TableHeader>
                        <TableHeader>Status</TableHeader>
                        <TableHeader>Charges</TableHeader>
                        <TableHeader>Refunds</TableHeader>
                        <TableHeader>Fees</TableHeader>
                        <TableHeader className="text-right">Total</TableHeader>
                    </TableRow>
                </TableHead>
                <TableBody className="p-0">
                    {records.map(record => (
                        <TableRow key={record.id} className="text-stone-400 text-xs font-normal leading-tight">
                            <TableCell className="py-4">
                                {formatDateTime(record.createdAt, 'MMM DD, YYYY')}
                            </TableCell>
                            <TableCell className="py-4">
                                <Badge shape={'pill'}
                                       variant={record.status === 'paid'
                                           ? 'secondary'
                                           : 'primary'}>
                                    {record.status}
                                </Badge>
                            </TableCell>
                            <TableCell className="py-4">
                                {formatCurrency(record.charges)}
                            </TableCell>
                            <TableCell className="py-4">
                                <NumericStyle value={record.refunds}
                                              negativeColor='text-rose-400'
                                              positiveColor=''/>
                            </TableCell>
                            <TableCell className="py-4">
                                <NumericStyle value={record.fees}
                                              negativeColor='text-rose-400'
                                              positiveColor=''/>
                            </TableCell>
                            <TableCell className="py-4 text-right">
                                {formatCurrency(record.total)}
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}

export default QuickActionsTable;