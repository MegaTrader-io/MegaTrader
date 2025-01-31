import React from 'react';
import Card, {CardTitle} from "@/components/Card";
import dynamic from "next/dynamic";
import {ApexOptions} from "apexcharts";

const ReactApexChart = dynamic(() => import("react-apexcharts"), {ssr: false});

const chartConfig = {
    type: "line" as const,
    height: '100%',
    series: [
        {
            name: "Earnings over time",
            data: [400, 300, 200, 100, 0],
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
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        },
        yaxis: {
            labels: {
                formatter: (value: number) => `${value}`,
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


function EarningsOverTime() {
    return (
        <Card className="w-full p-4 text-white">
            <CardTitle>
                Earnings over time
            </CardTitle>

            <div className="w-full h-[389px]">
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

export default EarningsOverTime;