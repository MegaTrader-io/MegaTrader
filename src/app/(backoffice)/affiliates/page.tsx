'use client';

import React, {useState} from "react";
import EarningsOverTime from "@/app/(backoffice)/affiliates/_components/EarningsOverTime";
import TrafficStatsTable from "@/app/(backoffice)/affiliates/_components/TrafficStatsTable";
import FinanceSummary from "@/app/(backoffice)/affiliates/_components/FinanceSummary";
import ReferralAndEarningsSection from "@/app/(backoffice)/affiliates/_components/ReferralAndEarningsSection";
import Alert from "@/components/Alert";

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

        <ReferralAndEarningsSection handleDisplayAlert={handleDisplayAlert}/>
        <FinanceSummary handleDisplayAlert={handleDisplayAlert}/>
        <EarningsOverTime/>
        <TrafficStatsTable/>
    </>
}