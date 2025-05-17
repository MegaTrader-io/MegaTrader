'use client';

import React, {useState} from "react";
import TrafficStatsTable from "@/app/(backoffice)/affiliates/_components/TrafficStatsTable";
import ReferralAndEarningsSection from "@/app/(backoffice)/affiliates/_components/ReferralAndEarningsSection";
import Alert from "@/components/Alert";
import MetricsPanel from "@/components/MetricsPanel";
import {AffiliatesMetrics} from "@/commons/data";
import RequestWithdrawal from "@/components/RequestWithdrawal";
import PerformanceAnalysis from "@/app/(backoffice)/affiliates/_components/PerformanceAnalysis";
import DriverGuide from "@/components/on-boarding/DriverGuide";
import {ActivateAffiliateModal} from "@/app/(backoffice)/affiliates/_components/activate_modal/ActivateAffiliateModal";

export interface IShowAlert {
    type: 'success' | 'error',
    message: string
}

export default function Affiliates() {
    const [showAlert, setShowAlert] = useState<IShowAlert | null>(null);
    const [openActivateModal, setOpenActivateModal] = useState<boolean>(true);

    function handleDisplayAlert(payload: IShowAlert) {
        setShowAlert(payload)
    }

    return <>
        {!openActivateModal && (
            <DriverGuide currentPath="/affiliates"/>
        )}

        {showAlert && (
            <div className="w-full">
                <Alert type={showAlert.type}
                       message={showAlert.message}/>
            </div>
        )}

        <ActivateAffiliateModal open={openActivateModal}
                                onClose={() => {
                                    setOpenActivateModal(false)
                                }}/>
        <MetricsPanel id="affiliate-summary" metrics={AffiliatesMetrics}/>
        <RequestWithdrawal handleDisplayAlert={handleDisplayAlert}/>
        <ReferralAndEarningsSection handleDisplayAlert={handleDisplayAlert}/>
        <PerformanceAnalysis/>
        <TrafficStatsTable/>
    </>
}