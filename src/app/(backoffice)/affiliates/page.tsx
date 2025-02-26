'use client';

import React from "react";
import EarningsOverTime from "@/app/(backoffice)/affiliates/_components/EarningsOverTime";
import TrafficStatsTable from "@/app/(backoffice)/affiliates/_components/TrafficStatsTable";
import FinanceSummary from "@/app/(backoffice)/affiliates/_components/FinanceSummary";
import ReferralAndEarningsSection from "@/app/(backoffice)/affiliates/_components/ReferralAndEarningsSection";

export default function AccountOverView() {
    return <>
        <ReferralAndEarningsSection/>
        <FinanceSummary/>
        <EarningsOverTime/>
        <TrafficStatsTable/>
    </>
}