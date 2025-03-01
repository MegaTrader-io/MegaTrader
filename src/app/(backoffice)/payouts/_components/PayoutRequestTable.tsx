import React, {useEffect, useState} from 'react';
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/Table";
import Badge from "@/components/Badge";
import {formatCurrency, formatDateTime} from "@/commons/utils";
import {IPayoutRequest, RequestStatusType} from "@/commons/interfaces";
import clsx from "clsx";
import ArrowSortBy from "@/components/ArrowSortBy";
import {directionType} from "@/components/ArrowDown";
import PaymentMethodImage from "@/app/(backoffice)/payouts/_components/PaymentMethodImage";


function BadgeColorByStatus({status}: { status: RequestStatusType }) {
    let variant: 'primary' | 'secondary' | 'error' | 'info';

    if (status === 'APPROVED') {
        variant = 'secondary'
    } else if (status === 'PENDING') {
        variant = 'primary'
    } else {
        variant = 'error'
    }

    return <Badge shape={'pill'}
                  size={'sm'}
                  variant={variant || 'secondary'}>
        {status}
    </Badge>
}

function ToggleArrow({open, onChange}: { open: boolean, onChange: () => void }) {
    const [rotate, setRotate] = useState('');

    useEffect(() => {
        setRotate(open ? '' : 'rotate-180')
    }, [open])

    return <button onClick={onChange} className="w-[30px] h-[30px]">
        <svg className={clsx(rotate)} width="26" height="26" viewBox="0 0 26 26" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <path
                d="M13 12.1562L16.8438 16L18.625 14.25L13 8.625L7.375 14.25L9.15625 16L13 12.1562ZM13 25.5C11.2708 25.5 9.64583 25.1719 8.125 24.5156C6.60417 23.8594 5.28125 22.9688 4.15625 21.8438C3.03125 20.7188 2.14063 19.3958 1.48438 17.875C0.828125 16.3542 0.5 14.7292 0.5 13C0.5 11.2708 0.828125 9.64583 1.48438 8.125C2.14063 6.60417 3.03125 5.28125 4.15625 4.15625C5.28125 3.03125 6.60417 2.14063 8.125 1.48438C9.64583 0.828125 11.2708 0.5 13 0.5C14.7292 0.5 16.3542 0.828125 17.875 1.48438C19.3958 2.14063 20.7188 3.03125 21.8438 4.15625C22.9688 5.28125 23.8594 6.60417 24.5156 8.125C25.1719 9.64583 25.5 11.2708 25.5 13C25.5 14.7292 25.1719 16.3542 24.5156 17.875C23.8594 19.3958 22.9688 20.7188 21.8438 21.8438C20.7188 22.9688 19.3958 23.8594 17.875 24.5156C16.3542 25.1719 14.7292 25.5 13 25.5Z"
                fill="white"/>
        </svg>
    </button>
}

function PayoutRequestTable({status, payoutRequests}: { status: RequestStatusType, payoutRequests: IPayoutRequest[] }) {
    const [direction, setDirection] = useState<directionType>('desc')
    const [togglePanel, setTogglePanel] = useState(true);
    const hasData = payoutRequests.length > 0;
    return (
        <div>
            <div className="flex w-full justify-between items-center mb-2">
                <div
                    className="flex items-center gap-2">
                    <div
                        className={clsx('h-6 px-2  rounded-xl justify-center items-center gap-2.5 inline-flex', {
                            'bg-teal-500': status === 'APPROVED',
                            'bg-primary': status === 'PENDING',
                            'bg-rose-500': status === 'REJECTED'
                        })}>
                        <div
                            className="text-[#131210] text-xs font-bold uppercase leading-normal">{payoutRequests.length}
                        </div>
                    </div>
                    <div
                        className="text-white text-2xl font-medium uppercase leading-7">{status}</div>
                </div>

                <ToggleArrow onChange={() => {
                    setTogglePanel(prev => !prev);
                }} open={togglePanel}/>
            </div>

            {togglePanel && !hasData && <EmptyPanel status={status}/>}
            {togglePanel && hasData && (
                <div className="overflow-x-auto">
                    <Table>
                        <TableHead>
                            <TableRow className="text-white">
                                <TableHeader className="!text-base">
                                    <div
                                        className="min-h-6 flex justify-between gap-2 items-center cursor-pointer select-none"
                                        onClick={() => {
                                            setDirection(direction === 'desc' ? 'asc' : 'desc');
                                        }}>
                                        <div>
                                            Date of request
                                        </div>
                                        <div>
                                            <ArrowSortBy direction={direction}/>
                                        </div>
                                    </div>
                                </TableHeader>
                                <TableHeader className="!text-base">MT Amount</TableHeader>
                                <TableHeader className="!text-base">Profit share</TableHeader>
                                <TableHeader className="!text-base">Trader share</TableHeader>
                                <TableHeader className="!text-base">Payment method</TableHeader>
                                <TableHeader className={"!text-base w-[230px]"}>Status</TableHeader>
                            </TableRow>
                        </TableHead>
                        <TableBody className="p-0">
                            {payoutRequests.map(payoutRequest => (
                                <TableRow key={payoutRequest.id}
                                          className="text-stone-400 text-base font-normal leading-tight">
                                    <TableCell className="py-4">
                                        {formatDateTime(payoutRequest.dateOfRequest, 'DD-MM-YYYY')}
                                    </TableCell>
                                    <TableCell className="py-4">
                                        {formatCurrency(payoutRequest.mtAmount)}
                                    </TableCell>
                                    <TableCell className="py-4">
                                        {payoutRequest.profitShare}%
                                    </TableCell>
                                    <TableCell className="py-4">
                                        {formatCurrency(payoutRequest.traderShare)}
                                    </TableCell>
                                    <TableCell className="py-4">
                                        <PaymentMethodImage paymentMethod={payoutRequest.paymentMethod}/>
                                    </TableCell>
                                    <TableCell className="py-4 text-base">
                                        <BadgeColorByStatus status={payoutRequest.status}/>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>
            )}
        </div>
    );
}

function EmptyPanel({status}: { status: string }) {
    return (
        <div className="p-4 flex-col  w-full justify-center items-center gap-2 inline-flex">
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


export default PayoutRequestTable;