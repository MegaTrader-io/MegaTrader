'use client';

import React, {useState} from 'react';
import Image from "next/image";
import Dropdown from "@/components/Dropdown";
import {periods, tooltipData} from "@/commons/data";
import Card from "@/components/Card";
import {Period, TooltipData} from "@/commons/interfaces";
import dynamic from 'next/dynamic';
import {ApexOptions} from "apexcharts";
import * as BaseTooltip from "@radix-ui/react-tooltip";
import clsx from "clsx";

const ReactApexChart = dynamic(() => import("react-apexcharts"), {ssr: false});

const chartConfig = {
    type: "line" as const,
    height: '100%',
    series: [
        {
            name: "Pro Plan Revenue",
            data: [24850, 24600, 24300, 23950, 23500, 23250, 23000, 23250, 23500, 24000, 24300, 24550],
        },
    ],
    options: {
        chart: {
            toolbar: {
                show: false,
            },
        },
        title: {
            show: false,
        },
        dataLabels: {
            enabled: false,
        },
        colors: ["#FFE7B8"],
        stroke: {
            lineCap: "round",
            curve: "smooth",
            width: 2,
        },
        markers: {
            size: 0,
        },
        xaxis: {
            axisTicks: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
            labels: {
                style: {
                    colors: "#A8A29E",
                    fontSize: "12px",
                    fontFamily: "inherit",
                    fontWeight: 400,
                },
            },
            categories: [0, 2, 4, 6, 8, 10, 12, 14, 16, 18],
        },
        yaxis: {
            labels: {
                formatter: (value: number) => `$${value}`,
                style: {
                    colors: "#A8A29E",
                    fontSize: "12px",
                    fontFamily: "inherit",
                    fontWeight: 400,
                },
            },
        },
        grid: {
            show: true,
            borderColor: "#374151",
            strokeDashArray: 5,
        },
        fill: {
            opacity: 0.8,
        },
        tooltip: {
            theme: "dark",
            x: {
                show: true,
            },
            y: {
                formatter: (value: number) => `$ ${value.toFixed(2)}`,
            },
        },
    } as ApexOptions,
};

function ProPlanChart() {
    const [selectPeriod, setSelectPeriod] = useState<Period>(periods[0]);

    return (
        <Card className="w-full space-y-4">
            <div className="space-y-4 lg:space-y-0 lg:flex justify-between">
                <div className="text-white text-xl font-light uppercase leading-normal flex items-center gap-1">
                    PRO PLAN $150K <QuestionIcon data={tooltipData}/>
                </div>

                <div>
                    <Dropdown
                        items={periods}
                        value={selectPeriod}
                        onChange={setSelectPeriod}
                        renderButtonContent={(item) => (
                            <div className="flex gap-2 justify-between w-full">
                                <div className="text-stone-400 text-base font-normal truncate">{item.text}</div>
                                <Image src="/assets/images/arrow-down.svg" alt='selection' width={24} height={24}/>
                            </div>
                        )}
                        renderOptionContent={(item) => (
                            <>
                                <div className="text-stone-400 text-base font-normal truncate">{item.text}</div>
                            </>
                        )}
                    />
                </div>
            </div>

            <div className="w-full h-[389px] rounded">
                <ReactApexChart
                    type={chartConfig.type}
                    height={chartConfig.height}
                    series={chartConfig.series}
                    options={chartConfig.options}
                />
            </div>
        </Card>
    );
}

interface QuestionIconProps {
    data: TooltipData;
}

const QuestionIcon: React.FC<QuestionIconProps> = ({data}) => {
    return (
        <BaseTooltip.Provider>
            <BaseTooltip.Root>
                <BaseTooltip.Trigger>
                    <div className="flex items-center">
                        <Image
                            className="inline"
                            src={"/assets/images/question-icon.svg"}
                            alt={"question icon"}
                            width={24}
                            height={24}
                        />
                    </div>
                </BaseTooltip.Trigger>
                <BaseTooltip.Portal>
                    <BaseTooltip.Content
                        sideOffset={10}
                        className={clsx('px-3 py-2 bg-black rounded-lg border border-neutral-700 box-border w-max max-w-[calc(100vw-10px)] text-stone-400')}>
                        <div className="w-[265px] text-stone-400 text-xs font-normal leading-tight">
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

                            <BaseTooltip.Arrow asChild>
                                <svg width="16" height="10" viewBox="0 2 16 10"
                                     className="absolute -top-[1px] -translate-x-1/2" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <g filter="url(#filter0_d_5037_5816)">
                                        <path d="M8.5 8L16.5 0H0.5L8.5 8Z" fill="black"/>
                                    </g>
                                    <defs>
                                        <filter id="filter0_d_5037_5816" x="0.5" y="0" width="16" height="10"
                                                filterUnits="userSpaceOnUse" colorInterpolationFilters="sRGB">
                                            <feFlood floodOpacity="0" result="BackgroundImageFix"/>
                                            <feColorMatrix in="SourceAlpha" type="matrix"
                                                           values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                           result="hardAlpha"/>
                                            <feOffset dy="2"/>
                                            <feComposite in2="hardAlpha" operator="out"/>
                                            <feColorMatrix type="matrix"
                                                           values="0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 1 0"/>
                                            <feBlend mode="normal" in2="BackgroundImageFix"
                                                     result="effect1_dropShadow_5037_5816"/>
                                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_5037_5816"
                                                     result="shape"/>
                                        </filter>
                                    </defs>
                                </svg>
                            </BaseTooltip.Arrow>
                        </div>
                    </BaseTooltip.Content>
                </BaseTooltip.Portal>
            </BaseTooltip.Root>
        </BaseTooltip.Provider>
    );
};

export default ProPlanChart;
