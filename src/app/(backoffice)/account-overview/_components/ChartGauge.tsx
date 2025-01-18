import React from 'react';
import dynamic from "next/dynamic";
import {ApexOptions} from "apexcharts";

const ReactApexChart = dynamic(() => import("react-apexcharts"), {ssr: false});

const chartConfig = {
    series: [137.37],
    options: {
        chart: {
            height: 350,
            type: 'radialBar',
            offsetY: -10
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                dataLabels: {
                    name: {
                        fontSize: '16px',
                        color: undefined,
                        offsetY: 120
                    },
                    value: {
                        offsetY: 30,
                        fontSize: '20px',
                        fontWeight: 300,
                        color: '#FFB34A',
                        lineHeight: 24,
                        formatter: function (value) {
                            return `$${value.toFixed(2)}`
                        }
                    },
                },
                track: {
                    background: '#292525'
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                shadeIntensity: 0.15,
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 13, 37]
            },
        },
        stroke: {
            dashArray: 4
        },
        labels: [''],
    } as ApexOptions,
}

function ChartGauge() {
    return (
        <ReactApexChart options={chartConfig.options} series={chartConfig.series} type="radialBar" height={'100%'}/>
    );
}

export default ChartGauge;