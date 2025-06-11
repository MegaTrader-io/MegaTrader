'use client';

import React, {useEffect, useState} from 'react';
import Image from "next/image";
import {chartConfig, periods, tooltipData} from "@/commons/data";
import Card from "@/components/Card";
import {Account, Period, TooltipData} from "@/commons/interfaces";
import dynamic from 'next/dynamic';
import Tooltip from "@/components/Tooltip";
import {ApexOptions} from "apexcharts";

const ReactApexChart = dynamic(() => import("react-apexcharts"), {ssr: false});

interface ChartData {
    id: number,
    series: { name: string, data: number[] }[],
    options: ApexOptions
}

interface QuestionIconProps {
    data: TooltipData;
}

const QuestionIcon: React.FC<QuestionIconProps> = ({data}) => {
    const template = (data: TooltipData) => {
        return (
            <>
                <div className="text-white text-xs font-bold leading-tight">Parameters</div>
                <div className="self-stretch flex-col justify-start items-start flex">
                    <div className="self-stretch py-2 justify-start items-center gap-2 inline-flex">
                        <div
                            className="grow shrink basis-0 text-stone-400 text-xs font-normal leading-tight">
                            Starting balance
                        </div>
                        <div className="grow shrink basis-0 text-stone-400 text-xs font-bold leading-tight">
                            {data.parameters.startingBalance}
                        </div>
                    </div>
                    <div className="self-stretch py-2 justify-start items-center gap-2 inline-flex">
                        <div
                            className="grow shrink basis-0 text-stone-400 text-xs font-normal leading-tight">
                            Max Position Size
                        </div>
                        <div className="grow shrink basis-0 text-stone-400 text-xs font-bold leading-tight">
                            {data.parameters.maxPositionSize}
                        </div>
                    </div>
                    <div className="self-stretch py-2 justify-start items-center gap-2 inline-flex">
                        <div
                            className="grow shrink basis-0 text-stone-400 text-xs font-normal leading-tight">
                            Max Drawdown
                        </div>
                        <div className="grow shrink basis-0 text-stone-400 text-xs font-bold leading-tight">
                            {data.parameters.maxDrawdown}
                        </div>
                    </div>
                </div>
                <div className="self-stretch h-[0px] border border-neutral-700"></div>
                <div className="self-stretch flex-col justify-start items-start flex">
                    <div className="self-stretch py-2 justify-start items-center gap-2 inline-flex">
                        <div
                            className="grow shrink basis-0 text-stone-400 text-xs font-normal leading-tight">
                            Account number:
                        </div>
                        <div className="grow shrink basis-0 text-stone-400 text-xs font-bold leading-tight">
                            {data.accountDetails.accountNumber}
                        </div>
                    </div>
                    <div className="self-stretch py-2 justify-start items-center gap-2 inline-flex">
                        <div
                            className="grow shrink basis-0 text-stone-400 text-xs font-normal leading-tight">
                            Platform:
                        </div>
                        <div className="grow shrink basis-0 text-stone-400 text-xs font-bold leading-tight">
                            {data.accountDetails.platform}
                        </div>
                    </div>
                    <div className="self-stretch py-2 justify-start items-center gap-2 inline-flex">
                        <div
                            className="grow shrink basis-0 text-stone-400 text-xs font-normal leading-tight">
                            Username:
                        </div>
                        <div className="grow shrink basis-0 text-stone-400 text-xs font-bold leading-tight">
                            {data.accountDetails.username}
                        </div>
                    </div>
                    <div className="self-stretch py-2 justify-start items-center gap-2 inline-flex">
                        <div
                            className="grow shrink basis-0 text-stone-400 text-xs font-normal leading-tight">
                            Password:
                        </div>
                        <div className="grow shrink basis-0 text-stone-400 text-xs font-bold leading-tight">
                            {data.accountDetails.password}
                        </div>
                    </div>
                </div>
            </>
        );
    }

    return (
        <Tooltip
            className="flex items-center"
            content={
                template(data)
            }>
            <Image
                className="inline"
                src={"/assets/images/exclamation-icon.svg"}
                alt={"question icon"}
                width={24}
                height={24}
            />
        </Tooltip>
    );
};

function ProPlanChart({account}: { account: Account }) {
    const [dataChart, setDataChart] = useState<ChartData | undefined>(undefined);
    const [selectPeriod, setSelectPeriod] = useState<Period>(periods[0]);

    function changeValue(e: React.ChangeEvent<HTMLSelectElement>) {
        const id = e.target.value
        const period = periods.find(period => period.id === id)!
        setSelectPeriod(period);
    }

    useEffect(() => {
        const days = {
            'last_30_days': 30,
            'last_14_days': 14,
            'last_7_days': 7,
        }[selectPeriod.id];

        setDataChart({
            id: (new Date()).getTime(),
            series: [
                {
                    name: "Pro Plan Revenue",
                    data: Array(days).fill('').map(() => Math.floor(Math.random() * 35000 + 1)),
                },
                {
                    name: "Upper Bound",
                    data: Array(days).fill('').map(() => 20000),
                },
                {
                    name: "Lower Bound",
                    data: Array(days).fill('').map(() => 10000),
                },
            ],
            options: {
                ...chartConfig.options,
                xaxis: {
                    ...chartConfig.options.xaxis,
                    categories: Array(days).fill('').map((_, index) => (index + 1)),
                },
            }
        });
    }, [selectPeriod.id]);

    return (
        <Card id="balance-graph" className="w-full space-y-4">
            <div className="space-y-4 lg:space-y-0 md:flex justify-between">
                <div className="text-white text-xl font-light uppercase leading-normal flex items-center gap-1">
                    {account.planDetail.level} {account.planDetail.planType} PLAN <QuestionIcon data={tooltipData}/>
                </div>

                <div>
                    <div className="relative w-full">
                        <select
                            className="w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none"
                            defaultValue={selectPeriod.id}
                            onChange={changeValue}>
                            {periods.map(option => (
                                <option key={option.id} value={option.id}>
                                    {option.text}
                                </option>
                            ))}
                        </select>
                        <div className="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <mask id="mask0_5269_2288" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse"
                                      x="0"
                                      y="0"
                                      width="24" height="24">
                                    <rect width="24" height="24" fill="#D9D9D9"/>
                                </mask>
                                <g mask="url(#mask0_5269_2288)">
                                    <path d="M12 15L7 10H17L12 15Z" fill="white"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div className="w-full h-[389px] rounded">
                {dataChart && (
                    <ReactApexChart
                        key={dataChart.id}
                        type='line'
                        height='100%'
                        width={'100%'}
                        series={dataChart.series}
                        options={dataChart.options}
                    />
                )}
            </div>
        </Card>
    );
}

export default ProPlanChart;
