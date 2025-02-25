'use client'

import React, {useEffect, useState} from "react";
import Image from "next/image";
import {Button} from "@/components/Button";
import Card from "@/components/Card";
import PayoutRequestTable from "@/app/(backoffice)/payouts/_components/PayoutRequestTable";
import {formatCurrency} from "@/commons/utils";
import {IPayoutRequest, RequestStatusType} from "@/commons/interfaces";

const METRICS = [
    {
        title: 'Available Amount',
        subtitle: 'Withdrable profit available',
        value: formatCurrency(4895)
    },
    {
        title: 'Available Profit',
        subtitle: 'Your total account profit',
        value: formatCurrency(9000)
    },
    {
        title: 'Profit Share %',
        subtitle: 'The amount of the profit you keep',
        value: '80%'
    },
    {
        title: 'Next Withdraw Date',
        subtitle: 'Next date you can withdraw profits',
        value: '06/03/2025'
    },
]

const payoutRequests: IPayoutRequest[] = [
    {
        id: 1,
        dateOfRequest: "01-01-2023",
        mtAmount: 910.00,
        profitShare: 60,
        traderShare: 546.00,
        status: "APPROVED"
    },
    {
        id: 2,
        dateOfRequest: "01-01-2023",
        mtAmount: 1810.00,
        profitShare: 50,
        traderShare: 450.00,
        status: "APPROVED"
    },
    {
        id: 3,
        dateOfRequest: "01-01-2023",
        mtAmount: 900.00,
        profitShare: 18,
        traderShare: 80.00,
        status: "APPROVED"
    },
    {
        id: 4,
        dateOfRequest: "01-01-2023",
        mtAmount: 900.00,
        profitShare: 18,
        traderShare: 80.00,
        status: "APPROVED"
    },
    {
        id: 5,
        dateOfRequest: "01-01-2023",
        mtAmount: 900.00,
        profitShare: 18,
        traderShare: 80.00,
        status: "APPROVED"
    },
    {
        id: 6,
        dateOfRequest: "01-01-2023",
        mtAmount: 910.00,
        profitShare: 60,
        traderShare: 546.00,
        status: "PENDING"
    },
    {
        id: 7,
        dateOfRequest: "01-01-2023",
        mtAmount: 1810.00,
        profitShare: 50,
        traderShare: 450.00,
        status: "PENDING"
    },
    {
        id: 8,
        dateOfRequest: "01-01-2023",
        mtAmount: 900.00,
        profitShare: 18,
        traderShare: 80.00,
        status: "PENDING"
    },
    {
        id: 9,
        dateOfRequest: "01-01-2023",
        mtAmount: 910.00,
        profitShare: 60,
        traderShare: 546.00,
        status: "REJECTED"
    },
    {
        id: 10,
        dateOfRequest: "01-01-2023",
        mtAmount: 1810.00,
        profitShare: 50,
        traderShare: 450.00,
        status: "REJECTED"
    }
]

export default function AccountOverView() {
    const payoutRequestLegend: Record<'approved' | 'pending' | 'rejected', RequestStatusType> = {
        approved: 'APPROVED',
        pending: 'PENDING',
        rejected: 'REJECTED'
    };

    const [payoutApprovedList, setPayoutApprovedList] = useState<IPayoutRequest[]>([]);
    const [payoutPendingList, setPayoutPendingList] = useState<IPayoutRequest[]>([]);
    const [payoutRejectedList, setPayoutRejectedList] = useState<IPayoutRequest[]>([]);

    useEffect(() => {
        setPayoutApprovedList(payoutRequests.filter(p => p.status === payoutRequestLegend.approved));
        setPayoutPendingList(payoutRequests.filter(p => p.status === payoutRequestLegend.pending));
        setPayoutRejectedList(payoutRequests.filter(p => p.status === payoutRequestLegend.rejected));
    }, [payoutRequestLegend.approved, payoutRequestLegend.pending, payoutRequestLegend.rejected]);

    return <>
        <div className="w-full space-y-8">
            <div className="space-y-4 md:space-y-0 md:grid md:grid-cols-2 lg:flex lg:justify-around gap-4">
                {METRICS.map((metric, index) => (
                    <Card
                        key={index}
                        className="flex-col justify-center items-start gap-2 inline-flex w-full">
                        <div className="flex-col justify-start items-start flex">
                            <div
                                className="text-white text-base font-medium leading-normal">
                                {metric.title}
                            </div>
                            <div
                                className="text-stone-400 text-xs font-medium leading-tight">
                                {metric.subtitle}
                            </div>
                        </div>
                        <div
                            className="text-primary text-[32px] font-light uppercase leading-10">{metric.value}</div>
                    </Card>
                ))}
            </div>

            <Card
                className="space-y-4 md:space-y-0 md:justify-start md:items-center md:gap-4 md:inline-flex md:w-full">
                <div
                    className="md:grow md:shrink md:basis-0 md:h-6 md:justify-start md:items-center md:gap-4 md:flex md:w-full">
                    <div className="text-white text-base font-medium leading-normal">Available Payment
                        Methods
                    </div>
                    <Image src='/assets/images/crypto-icons.svg' alt='icons' width={218} height={24}/>
                </div>

                <Button className="w-full md:w-auto">
                    Request Withdrawal
                </Button>
            </Card>

            <Card className="space-y-8">
                <PayoutRequestTable
                    status={payoutRequestLegend.approved}
                    payoutRequests={payoutApprovedList}
                />

                <PayoutRequestTable
                    status={payoutRequestLegend.pending}
                    payoutRequests={payoutPendingList}
                />

                <PayoutRequestTable
                    status={payoutRequestLegend.rejected}
                    payoutRequests={payoutRejectedList}
                />
            </Card>
        </div>

    </>
}