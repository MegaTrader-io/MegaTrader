import React, { useState } from "react";
import dynamic from "next/dynamic";
import { ApexOptions } from "apexcharts";

const ReactApexChart = dynamic(() => import("react-apexcharts"), { ssr: false });

function convertAvgWinningTraderValue(value: number, maxValue: number) {
    return (value * 100) / maxValue;
}

function ChartGaugeWithNeedle({
                                  value,
                                  maxValue,
                              }: {
    value: number;
    maxValue: number;
}) {
    const [chartConfig] = useState({
        series: [convertAvgWinningTraderValue(value, maxValue)],
        options: {
            chart: {
                type: "radialBar",
                width: "100%", // Ajustar al ancho del contenedor padre
                height: "100%", // Ajustar al alto del contenedor padre
                offsetY: 0, // Centrar el gráfico
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
            labels: [value.toString()],
        } as ApexOptions,
    });

    // Cálculo del ángulo de la aguja basado en el valor
    const calculateNeedleRotation = (val: number): number => {
        const startAngle = -135; // Ángulo inicial del gráfico
        const endAngle = 135; // Ángulo final del gráfico
        const clampedValue = Math.max(0, Math.min(100, val)); // Asegurarse de que esté entre 0 y 100
        return (clampedValue / 100) * (endAngle - startAngle) + startAngle;
    };

    const needleRotation = calculateNeedleRotation(
        convertAvgWinningTraderValue(value, maxValue)
    );

    return (
        <div
            style={{
                width: "100%",
                height: "100%",
                position: "relative",
            }}
        >
            {/* Gráfico Radial */}
            <ReactApexChart
                options={chartConfig.options}
                series={chartConfig.series}
                type="radialBar"
                width="100%"
                height="100%"
            />

            {/* Aguja SVG */}
            <svg
                style={{
                    position: "absolute",
                    top: 0,
                    left: 0,
                    width: "100%",
                    height: "100%",
                    pointerEvents: "none", // No afecta la interacción
                }}
                xmlns="http://www.w3.org/2000/svg"
            >
                {/* Contenedor de la Aguja */}
                <g
                    transform={`rotate(${needleRotation} 50 50)`}
                    style={{
                        transformOrigin: "center",
                        transformBox: "fill-box",
                    }}
                >
                    <line
                        x1="50%"
                        y1="50%"
                        x2="50%"
                        y2="20%"
                        stroke="#FFB34A"
                        strokeWidth="3"
                        strokeLinecap="round"
                    />
                    <circle
                        cx="50%"
                        cy="50%"
                        r="4"
                        fill="#FFB34A"
                        stroke="none"
                    />
                </g>
            </svg>
        </div>
    );
}

export default ChartGaugeWithNeedle;
