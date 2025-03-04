'use client';

import React, {useState} from "react";
import EarningsOverTime from "@/app/(backoffice)/affiliates/_components/EarningsOverTime";
import TrafficStatsTable from "@/app/(backoffice)/affiliates/_components/TrafficStatsTable";
import ReferralAndEarningsSection from "@/app/(backoffice)/affiliates/_components/ReferralAndEarningsSection";
import Alert from "@/components/Alert";
import MetricsPanel from "@/components/MetricsPanel";
import {AffiliatesMetrics} from "@/commons/data";
import RequestWithdrawal from "@/components/RequestWithdrawal";

export interface IShowAlert {
    type: 'success' | 'error',
    message: string
}

export default function Affiliates() {
    const [showAlert, setShowAlert] = useState<IShowAlert | null>(null);

    function handleDisplayAlert(payload: IShowAlert) {
        setShowAlert(payload)
    }

    return <>
        {showAlert && (
            <div className="w-full">
                <Alert type={showAlert.type}
                       message={showAlert.message}/>
            </div>
        )}

        <MetricsPanel metrics={AffiliatesMetrics}/>
        <RequestWithdrawal handleDisplayAlert={handleDisplayAlert}/>
        <ReferralAndEarningsSection handleDisplayAlert={handleDisplayAlert}/>
        <EarningsOverTime/>
        <TrafficStatsTable/>
    </>
}