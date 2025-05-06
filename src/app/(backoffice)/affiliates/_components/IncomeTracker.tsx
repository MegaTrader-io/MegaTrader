import React, {useEffect, useState} from 'react';
import Card, {CardTitle} from "@/components/Card";
import Image from "next/image";
import {incomeTrackerPeriods} from "@/commons/data";
import {Period} from "@/commons/interfaces";
import ChartBar from "@/components/ChartBar";

function IncomeTracker() {
    const [dataChart, setDataChart] = useState<{ data: number[], labels: string[] } | null>(null);
    const [selectPeriod, setSelectPeriod] = useState<Period>(incomeTrackerPeriods[2]);

    function changeValue(e: React.ChangeEvent<HTMLSelectElement>) {
        const id = e.target.value
        const period = incomeTrackerPeriods.find(period => period.id === id)!
        setSelectPeriod(period);
    }

    useEffect(() => {
        if (selectPeriod.id === 'last_30_days') {
            setDataChart({
                labels: Array(30).fill('').map((_, index) => (index + 1).toString().padStart(2, '0')),
                data: [
                    1114,
                    1457,
                    2284,
                    1726,
                    1931,
                    1355,
                    909,
                    1114,
                    1457,
                    2284,
                    1726,
                    1931,
                    1355,
                    909,
                    1114,
                    1457,
                    2284,
                    1726,
                    1931,
                    1355,
                    909,
                    1114,
                    1457,
                    2284,
                    1726,
                    1931,
                    1355,
                    909,
                    1114,
                    1457
                ]
            });
        } else if (selectPeriod.id === 'last_14_days') {
            setDataChart({
                labels: Array(14).fill('').map((_, index) => (index + 1).toString().padStart(2, '0')),
                data: [
                    1114,
                    1457,
                    2284,
                    1726,
                    1931,
                    1355,
                    909,
                    1114,
                    1457,
                    2284,
                    1726,
                    1931,
                    1355,
                    909,
                ]
            });
        } else if (selectPeriod.id === 'last_7_days') {
            setDataChart({
                labels: Array(7).fill('').map((_, index) => (index + 1).toString().padStart(2, '0')),
                data: [
                    1114,
                    1457,
                    2284,
                    1726,
                    1931,
                    1355,
                    909,
                ]
            });
        }
    }, [selectPeriod.id]);

    return (
        <Card id="income-tracker" className="w-full p-4 text-white space-y-4 md:space-y-0">
            <div className="space-y-4 md:space-y-0 md:flex md:justify-between md:items-center">
                <CardTitle className="flex items-center gap-2">
                    <div>
                        <div className="flex gap-2 items-center">
                            <Image src={'/assets/images/chart.svg'} alt={'chart'} width={24} height={24}/>
                            <span className="text-2xl font-medium uppercase leading-7">Income Tracker</span>
                        </div>
                        <div
                            className="normal-case text-stone-400 text-base font-medium leading-normal">Track
                            changes in income over time and access detailed data on each payments received.
                        </div>
                    </div>
                </CardTitle>
                <div className="space-y-4 md:space-y-0 md:flex items-center gap-4">
                    <div className="relative md:w-[250px]">
                        <select
                            className="w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none"
                            defaultValue={selectPeriod.id}
                            onChange={changeValue}>
                            {incomeTrackerPeriods.map(option => (
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
            <div className="md:grid md:grid-cols-[200px_auto] align-bottom gap-4">
                <div className={'h-full flex items-end'}>
                    <div>
                        <div
                            className="self-stretch text-teal-500 justify-start text-Success-500 text-5xl font-light  uppercase leading-[60px]">+20%
                        </div>
                        <div
                            className="self-stretch text-stone-400 justify-start text-Text-Body text-base font-medium leading-normal">This
                            week income is higher than last week’s.
                        </div>
                    </div>
                </div>
                <div className="w-full md:px-4 pt-16 h-auto overflow-x-scroll">
                    {dataChart && (
                        <ChartBar
                            data={dataChart.data}
                            labels={dataChart.labels}
                        />
                    )}
                </div>
            </div>
        </Card>
    );
}

export default IncomeTracker;