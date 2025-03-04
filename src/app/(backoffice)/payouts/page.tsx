'use client'

import React, {useState} from "react";
import Card from "@/components/Card";
import PayoutRequestTable from "@/app/(backoffice)/payouts/_components/PayoutRequestTable";
import {RequestStatusType} from "@/commons/interfaces";
import {PayoutMetrics} from "@/commons/data";
import Alert from "@/components/Alert";
import MetricsPanel from "@/components/MetricsPanel";
import RequestWithdrawal from "@/components/RequestWithdrawal";
import {IShowAlert} from "@/app/(backoffice)/affiliates/page";

export default function AccountOverView() {
    const payoutRequestLegend: Record<'approved' | 'pending' | 'rejected', RequestStatusType> = {
        approved: 'APPROVED',
        pending: 'PENDING',
        rejected: 'REJECTED'
    };

    const [showAlert, setShowAlert] = useState<IShowAlert | null>(null);

    function handleDisplayAlert(payload: IShowAlert) {
        setShowAlert(payload)
    }

    return <>
        <div className="w-full space-y-8">
            {showAlert && (
                <div className="w-full">
                    <Alert type={showAlert.type}
                           message={showAlert.message}/>
                </div>
            )}

            <MetricsPanel metrics={PayoutMetrics}/>

            <RequestWithdrawal handleDisplayAlert={handleDisplayAlert}/>

            <Card className="space-y-8">
                <PayoutRequestTable
                    status={payoutRequestLegend.approved}
                />

                <PayoutRequestTable
                    status={payoutRequestLegend.pending}
                />

                <PayoutRequestTable
                    status={payoutRequestLegend.rejected}
                />
            </Card>
        </div>

    </>
}