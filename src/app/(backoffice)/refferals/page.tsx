'use client';

import React, {useEffect, useState} from "react";
import {useRouter} from "next/navigation";
import TrafficStatsTable from "@/app/(backoffice)/refferals/_components/TrafficStatsTable";
import ReferralAndEarningsSection from "@/app/(backoffice)/refferals/_components/ReferralAndEarningsSection";
import Alert from "@/components/Alert";
import MetricsPanel from "@/components/MetricsPanel";
import {AffiliatesMetrics} from "@/commons/data";
import RequestWithdrawal from "@/components/RequestWithdrawal";
import PerformanceAnalysis from "@/app/(backoffice)/refferals/_components/PerformanceAnalysis";
import DriverGuide from "@/components/on-boarding/DriverGuide";
import {ActivateAffiliateModal} from "@/app/(backoffice)/refferals/_components/activate_modal/ActivateAffiliateModal";
import SkeletonAffiliate from "@/app/(backoffice)/refferals/_components/SkeletonAffiliate";

export interface IShowAlert {
    type: 'success' | 'error',
    message: string
}

export default function Affiliates() {
    const router = useRouter();
    const [showAlert, setShowAlert] = useState<IShowAlert | null>(null);
    const [isActivated, setIsActivated] = useState<boolean>(false);
    const [openActivateModal, setOpenActivateModal] = useState<boolean>(true);

    useEffect(() => {
        if (!isActivated) {
            setOpenActivateModal(true);
        }
    }, [isActivated]);

    function handleDisplayAlert(payload: IShowAlert) {
        setShowAlert(payload)
        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    }

    function handleActivation() {
        setIsActivated(true);
        setOpenActivateModal(false);
    }

    if (!isActivated) {
        return <>
            <ActivateAffiliateModal open={openActivateModal}
                                    onSubmit={handleActivation}
                                    onClose={() => {
                                        const oldIsActivatedState = isActivated;
                                        setOpenActivateModal(false);
                                        setIsActivated(false)

                                        if (!oldIsActivatedState) {
                                            router.replace(
                                                `/account-overview`
                                            );

                                            return;
                                        }
                                    }}/>

            <SkeletonAffiliate/>
        </>
    }

    return <>
        <DriverGuide currentPath="/refferals"/>

        {showAlert && (
            <div className="w-full">
                <Alert type={showAlert.type}
                       message={showAlert.message}/>
            </div>
        )}

        <MetricsPanel id="affiliate-summary" metrics={AffiliatesMetrics}/>
        <RequestWithdrawal handleDisplayAlert={handleDisplayAlert}/>
        <ReferralAndEarningsSection handleDisplayAlert={handleDisplayAlert}/>
        <PerformanceAnalysis/>
        <TrafficStatsTable/>
    </>
}