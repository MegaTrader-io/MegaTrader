import React, {useEffect, useRef, useState} from 'react';
import Card, {CardTitle} from "@/components/Card";
import {periods} from "@/commons/data";
import {Period} from "@/commons/interfaces";
import CustomBarChar, {ChartBarProps} from "@/components/CustomBarChar";

function PerformanceAnalysis() {
    const container = useRef<HTMLDivElement | null>(null);
    const [chartData, setChartData] = useState<ChartBarProps | undefined>(undefined);
    const [selectPeriod, setSelectPeriod] = useState<Period>(periods[0]);

    useEffect(() => {
        if (selectPeriod.id === 'last_7_days') {
            setChartData(generateRandomData(7));
        } else if (selectPeriod.id === 'last_14_days') {
            setChartData({
                internalId: 0,
                xAxis: [],
                series: [],
                yAxis: []
            });
        } else if (selectPeriod.id === 'last_30_days') {
            setChartData(generateRandomData(30));
        }
    }, [selectPeriod.id]);

    function generateRandomData(days: number): ChartBarProps {
        try {
            const generateArray = (name: string): number[] => {
                if (days === 7 && name === 'visits') {
                    return [190, 90, 263, 0, 40, 150, 65];
                }

                if (days === 7 && name === 'conversions') {
                    return [100, 210, 130, 0, 290, 120, 160];
                }

                return Array.from({length: days}, () => Math.floor(Math.random() * 300) + 50);
            };

            const visits = generateArray('visits');
            const conversions = generateArray('conversions');

            if (visits.length !== conversions.length) {
                throw new Error("Las longitudes de 'visits' y 'conversions' no coinciden.");
            }

            return {
                internalId: new Date().getTime(),
                xAxis: Array.from({length: days}, (_, i) => (i + 1).toString().padStart(2, '0')),
                series: [
                    {
                        name: 'Visits',
                        color: 'bg-visits',
                        data: visits,
                    },
                    {
                        name: 'Conversions',
                        color: 'bg-conversions',
                        data: conversions,
                    },
                ],
            };
        } catch (error) {
            console.error("unable to process the data:", error);
            return {
                internalId: new Date().getTime(),
                xAxis: [],
                series: [],
            };
        }
    }

    function changeValue(e: React.ChangeEvent<HTMLSelectElement>) {
        const id = e.target.value;
        const period = periods.find((period) => period.id === id)!;
        setSelectPeriod(period);
    }

    return (
        <Card id="performance-analysis" className="w-full p-4 text-white space-y-4">
            <div className="space-y-2 md:space-y-0 md:flex md:justify-between md:items-center">
                <CardTitle className="flex items-center gap-2">
                    <span>Performance Analysis</span>
                </CardTitle>
                <div className="space-y-4 md:space-y-0 md:flex items-center gap-4">
                    <div className="flex gap-4">
                        <div className="flex gap-2">
                            <div className="w-6 h-6 bg-blue-500 rounded-full"></div>
                            <div className="justify-start text-white text-base font-medium leading-normal">
                                Visits
                            </div>
                        </div>
                        <div className="flex gap-2">
                            <div className="w-6 h-6 bg-teal-500 rounded-full"></div>
                            <div className="justify-start text-white text-base font-medium leading-normal">
                                Conversions
                            </div>
                        </div>
                    </div>
                    <div className="relative w-full">
                        <select
                            className="w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none"
                            defaultValue={selectPeriod.id}
                            onChange={changeValue}
                        >
                            {periods.map((option) => (
                                <option key={option.id} value={option.id}>
                                    {option.text}
                                </option>
                            ))}
                        </select>
                        <div className="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <mask
                                    id="mask0_5269_2288"
                                    style={{maskType: 'alpha'}}
                                    maskUnits="userSpaceOnUse"
                                    x="0"
                                    y="0"
                                    width="24"
                                    height="24"
                                >
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
            <div ref={container}
                 className="container-chart w-full h-[409px] overflow-x-auto">
                {chartData && <CustomBarChar {...chartData} />}
            </div>
        </Card>
    );
}

export default PerformanceAnalysis;
