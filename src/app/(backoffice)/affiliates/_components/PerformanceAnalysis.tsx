import React, {useEffect, useRef, useState} from 'react';
import Card, {CardTitle} from "@/components/Card";
import dynamic from "next/dynamic";
import {chartAffiliatesConfig, periods} from "@/commons/data";
import {Period} from "@/commons/interfaces";

const ReactApexChart = dynamic(() => import("react-apexcharts"), {ssr: false});

function PerformanceAnalysis() {
    const container = useRef<HTMLDivElement | null>(null);
    const [selectPeriod, setSelectPeriod] = useState<Period>(periods[0]);

    function changeValue(e: React.ChangeEvent<HTMLSelectElement>) {
        const id = e.target.value
        const period = periods.find(period => period.id === id)!
        setSelectPeriod(period);
    }

    useEffect(() => {
        if (!container.current) {
            return;
        }

        const rerenderAxis = () => {
            console.info('rerenderAxis')
            const axisTexts = document.querySelector('.apexcharts-canvas svg .apexcharts-xaxis .apexcharts-xaxis-texts-g');
            if (!axisTexts) {
                return;
            }

            const texts = Array.from(axisTexts.querySelectorAll('text'));

            texts.forEach((text) => {
                const x = Number(text.getAttribute('x') || 0);
                const y = Number(text.getAttribute('y') || 0);

                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('cx', x.toString());
                circle.setAttribute('cy', (y - 2).toString());
                circle.setAttribute('r', '15');
                circle.setAttribute('fill', '#3a3a3a');

                const clonedText = text.cloneNode(true) as SVGTextElement;

                axisTexts.insertBefore(circle, text);
                text.remove();

                axisTexts.after(clonedText, text);
            });
        };

        const observer = new MutationObserver(rerenderAxis);
        observer.observe(container.current, {childList: true, subtree: true});

        rerenderAxis();

        return () => {
            observer.disconnect();
        };
    }, []);


    return (
        <Card id="performance-analysis" className="w-full p-4 text-white space-y-4 md:space-y-0">
            <div className="space-y-2 md:space-y-0 md:flex md:justify-between md:items-center">
                <CardTitle className="flex items-center gap-2">
                    <span>Performance Analysis</span>
                </CardTitle>
                <div className="flex items-center gap-4">
                    <div className="flex gap-4">
                        <div className="flex gap-2">
                            <div className="w-6 h-6 bg-blue-500 rounded-full"></div>
                            <div
                                className="justify-start text-white text-base font-medium leading-normal">Visits
                            </div>
                        </div>
                        <div className="flex gap-2">
                            <div className="w-6 h-6 bg-teal-500 rounded-full"></div>
                            <div
                                className="justify-start text-white text-base font-medium leading-normal">Conversions
                            </div>
                        </div>
                    </div>

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
            <div ref={container} className="w-full h-[389px] overflow-x-scroll sm:overflow-hidden">
                <div className="h-full" style={{minWidth: '500px'}}>
                    <ReactApexChart
                        type={chartAffiliatesConfig.type}
                        height={chartAffiliatesConfig.height}
                        series={chartAffiliatesConfig.series}
                        options={chartAffiliatesConfig.options}
                    />
                </div>
            </div>
        </Card>
    );
}

export default PerformanceAnalysis;