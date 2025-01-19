import React, {useState} from "react";
import dynamic from "next/dynamic";
import {ApexOptions} from "apexcharts";

const ReactApexChart = dynamic(() => import("react-apexcharts"), {ssr: false});

function convertAvgWinningTraderValue(value: number, maxValue: number) {
    return (value * 100) / maxValue;
}

function ChartGauge({value, maxValue}: { value: number; maxValue: number }) {
    const [chartConfig] = useState({
        series: [convertAvgWinningTraderValue(value, maxValue)],
        options: {
            chart: {
                height: 350,
                type: "radialBar",
                offsetY: -10,
            },
            colors: ["#FFB34A"], // Color base del inicio de la barra
            plotOptions: {
                radialBar: {
                    startAngle: -135,
                    endAngle: 135,
                    hollow: {
                        size: "55%",
                        background: "transparent",
                    },
                    track: {
                        background: "#292525", // Color del track inactivo
                        strokeWidth: "100%",
                    },
                    dataLabels: {
                        showOn: "always",
                        name: {
                            show: false,
                        },
                        value: {
                            offsetY: 10,
                            fontSize: "24px",
                            fontWeight: 500,
                            color: "#FFB34A",
                            formatter: function () {
                                return `$${value.toFixed(2)}`;
                            },
                        },
                    },
                },
            },
            fill: {
                type: "gradient",
                gradient: {
                    shade: "dark",
                    type: "vertical",
                    gradientToColors: ["#2DD4BF"],
                    shadeIntensity: 1,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 100],
                },
            },
            stroke: {
                lineCap: "round", // Estilo redondeado en los extremos de la barra
            },
            labels: [value.toString()],
        } as ApexOptions,
    });

    return (
        <ReactApexChart
            options={chartConfig.options}
            series={chartConfig.series}
            type="radialBar"
            height={350}
        />
    );
}

export default ChartGauge;
