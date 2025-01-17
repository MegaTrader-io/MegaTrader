'use client';

import React, { useState } from 'react';
import Image from "next/image";
import Dropdown from "@/components/Dropdown";
import { periods } from "@/commons/data";
import Card from "@/components/Card";
import { Period } from "@/commons/interfaces";
import dynamic from 'next/dynamic';
import { ApexOptions } from "apexcharts";

const ReactApexChart = dynamic(() => import("react-apexcharts"), { ssr: false });

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
        colors: ["#FFE7B8"], // Color amarillo suave para la línea
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
            categories: [0, 2, 4, 6, 8, 10, 12, 14, 16, 18], // Intervalos del eje X
        },
        yaxis: {
            labels: {
                formatter: (value: number) => `$${value}`, // Formato en dólares
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
            borderColor: "#374151", // Color del grid para alinearse con el fondo oscuro
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
                formatter: (value: number) => `$ ${value.toFixed(2)}`, // Mostrar valores con formato
            },
        },
    } as ApexOptions,
};

function ProPlanChart() {
    const [selectPeriod, setSelectPeriod] = useState<Period>(periods[0]);

    return (
        <Card className="w-full space-y-4">
            <div className="flex justify-between">
                <div className="text-white text-xl font-light uppercase leading-normal flex items-center gap-1">
                    PRO PLAN $150K <Image className="inline" src={'/assets/images/question-icon.svg'}
                                          alt={'question icon'} width={24} height={24}></Image></div>

                <div>
                    <Dropdown
                        items={periods}
                        value={selectPeriod}
                        onChange={setSelectPeriod}
                        renderButtonContent={(item) => (
                            <div className="flex gap-2 items-center">
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

export default ProPlanChart;
