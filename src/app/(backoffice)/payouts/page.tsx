'use client'

import React, {useState} from "react";
import {PayoutMetrics} from "@/commons/data";
import Alert from "@/components/Alert";
import MetricsPanel from "@/components/MetricsPanel";
import RequestWithdrawal from "@/components/RequestWithdrawal";
import {IShowAlert} from "@/app/(backoffice)/affiliates/page";
import IncomeTracker from "@/app/(backoffice)/affiliates/_components/IncomeTracker";
import PayoutsManager from "@/app/(backoffice)/payouts/_components/PayoutsManager";
import DriverGuide from "@/components/on-boarding/DriverGuide";

export default function AccountOverView() {
    const [showAlert, setShowAlert] = useState<IShowAlert | null>(null);

    function handleDisplayAlert(payload: IShowAlert) {
        setShowAlert(payload)
    }

    return <>
        <DriverGuide currentPath="/payouts"/>
        <div className="w-full space-y-8">
            {showAlert && (
                <div className="w-full">
                    <Alert type={showAlert.type}
                           message={showAlert.message}/>
                </div>
            )}

            <MetricsPanel id="payout-summary" metrics={PayoutMetrics}/>
            <RequestWithdrawal handleDisplayAlert={handleDisplayAlert}/>
            <IncomeTracker/>
            <PayoutsManager/>
        </div>

    </>
}