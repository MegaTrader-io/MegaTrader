'use client'

import React, {useEffect, useState} from "react";
import Image from "next/image";
import {Button} from "@/components/Button";
import Card from "@/components/Card";
import PayoutRequestTable from "@/app/(backoffice)/payouts/_components/PayoutRequestTable";
import {IPayoutRequest, RequestStatusType} from "@/commons/interfaces";
import RequestPayoutsModal, {IRequestPayoutForm} from "@/app/(backoffice)/payouts/_components/RequestPayoutsModal";
import {METRICS, payoutRequests} from "@/commons/data";
import Alert from "@/components/Alert";
import {useLoading} from "@/context/LoadingContext";

export default function AccountOverView() {
    const {setLoading} = useLoading();
    const [submitRequestState, setSubmitRequestState] = useState<{
        success: boolean | undefined
    }>({success: undefined});
    const [openRequestModal, setOpenRequestModal] = useState<boolean>(false);
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

    function toggleRequestModal() {
        setOpenRequestModal(prev => !prev);
    }

    function submitRequest(form: IRequestPayoutForm) {
        setOpenRequestModal(false)
        setLoading(true);

        console.info('form', form);

        setTimeout(function () {
            setSubmitRequestState({success: Number(form.amount) >= 100});
            setLoading(false);
        }, 1200);
    }

    return <>
        {openRequestModal &&
            <RequestPayoutsModal open={openRequestModal} onClose={toggleRequestModal} submitRequest={submitRequest}/>}
            <div className="w-full space-y-8">
                {submitRequestState.success === true && (
                    <Alert type={'success'}
                           message={'Your request has been submitted successfully. You\'ll be notified once it\'s approved.'}/>
                )}

                {submitRequestState.success === false && (
                    <Alert type={'error'}
                           message={'Something went wrong. Check your internet connection and try again later.'}/>
                )}

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
                                className="text-primary text-[32px] font-light uppercase leading-10">
                                {metric.value}
                            </div>
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

                    <Button onClick={toggleRequestModal} className="w-full md:w-auto">
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