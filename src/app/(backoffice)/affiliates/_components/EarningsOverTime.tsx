import React, {useState} from 'react';
import Card, {CardTitle} from "@/components/Card";
import dynamic from "next/dynamic";
import {chartAffiliatesConfig, periods} from "@/commons/data";
import {Period} from "@/commons/interfaces";
import Tooltip from "@/components/Tooltip";
import ExclamationIcon from "@/components/ExclamationIcon";

const ReactApexChart = dynamic(() => import("react-apexcharts"), {ssr: false});

function EarningsOverTime() {
    const [selectPeriod, setSelectPeriod] = useState<Period>(periods[0]);

    function changeValue(e: React.ChangeEvent<HTMLSelectElement>) {
        const id = e.target.value
        const period = periods.find(period => period.id === id)!
        setSelectPeriod(period);
    }

    return (
        <Card className="w-full p-4 text-white space-y-4 md:space-y-0">
            <div className="space-y-2 md:space-y-0 md:flex md:justify-between md:items-center">
                <CardTitle className="flex items-center gap-2">
                    <span>Earnings over time</span> <Tooltip
                    content="Earnings over time">
                    <ExclamationIcon/>
                </Tooltip>
                </CardTitle>
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
            <div className="w-full h-[389px]">
                <ReactApexChart
                    type={chartAffiliatesConfig.type}
                    height={chartAffiliatesConfig.height}
                    series={chartAffiliatesConfig.series}
                    options={chartAffiliatesConfig.options}
                />
            </div>
        </Card>
    );
}

export default EarningsOverTime;