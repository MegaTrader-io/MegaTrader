import React from 'react';
import clsx from "clsx";

interface SeriesItem {
    name: string;
    data: number[];
    color: string;
}

export interface ChartBarProps {
    xAxis: string[];
    series: SeriesItem[];
    yAxis?: number[];
}

const CustomBarChar: React.FC<ChartBarProps> = ({ xAxis, series, yAxis }) => {
    if (!series?.length || !xAxis?.length) {
        return <p className="text-red-500">No data to display.</p>;
    }

    // Función auxiliar para calcular un "número agradable"
    function niceNumber(x: number, round: boolean): number {
        const exponent = Math.floor(Math.log10(x));
        const fraction = x / Math.pow(10, exponent);
        let niceFraction: number;

        if (round) {
            if (fraction < 1.5) {
                niceFraction = 1;
            } else if (fraction < 3) {
                niceFraction = 2;
            } else if (fraction <= 7) {
                niceFraction = 5;
            } else {
                niceFraction = 10;
            }
        } else {
            if (fraction <= 1) {
                niceFraction = 1;
            } else if (fraction <= 2) {
                niceFraction = 2;
            } else if (fraction <= 5) {
                niceFraction = 5;
            } else {
                niceFraction = 10;
            }
        }
        return niceFraction * Math.pow(10, exponent);
    }

    // Función que genera ticks para el eje Y de forma similar a Excel, con un buffer del 10%.
    function generateYAxis(max: number, nTicks: number = 6): number[] {
        if (max <= 0) return [0];

        const bufferMax = max * 1.1;
        const rawSpacing = bufferMax / (nTicks - 1);
        const tickSpacing = niceNumber(rawSpacing, true);
        let niceMax = Math.ceil(max / tickSpacing) * tickSpacing;
        if (niceMax === max) {
            niceMax += tickSpacing;
        }
        const ticks: number[] = [];
        for (let tick = 0; tick <= niceMax; tick += tickSpacing) {
            ticks.push(tick);
        }
        return ticks;
    }

    // Si no se pasó yAxis, lo calculamos internamente:
    let computedYAxis: number[];
    if (!yAxis) {
        let maxStacked = 0;
        for (let i = 0; i < xAxis.length; i++) {
            const stackTotal = series.reduce((acc, cur) => acc + (cur.data[i] || 0), 0);
            if (stackTotal > maxStacked) {
                maxStacked = stackTotal;
            }
        }
        computedYAxis = generateYAxis(maxStacked, 6);
    } else {
        computedYAxis = yAxis;
    }

    const maxY = computedYAxis[computedYAxis.length - 1];

    return (
        <div className="relative w-full h-full text-white px-16">
            <div className="absolute inset-0 z-0 -bottom-[1px] my-10">
                {computedYAxis.map((y, idx) => (
                    <div
                        key={idx}
                        className={clsx(
                            'absolute w-full text-sm font-medium text-stone-500 ml-10 leading-tight',
                            { 'border-t border-white/10': idx > 0 }
                        )}
                        style={{ bottom: `${(y / maxY) * 100}%` }}
                    >
            <span className="absolute -translate-x-10 -translate-y-1/2 text-right">
              {y}
            </span>
                    </div>
                ))}
            </div>

            {/* Gráfica de barras */}
            <div className="relative flex gap-2 h-full w-full">
                {xAxis.map((label, i) => {
                    const stackedBars = series.map((serie) => ({
                        height: (serie.data[i] / maxY) * 100,
                        color: serie.color,
                    }));
                    return (
                        <div key={i} className="flex flex-col items-center justify-end flex-1 h-[387px]">
                            <div className="flex flex-col justify-end w-[36px] relative h-[343px]">
                                {stackedBars.map((bar, idx) => (
                                    <div
                                        key={idx}
                                        title={idx.toString()}
                                        className={clsx('w-[36px] flex', {
                                            'rounded-tl-[64px] rounded-tr-[64px]': idx === 0,
                                            'rounded-bl-[64px] rounded-br-[64px]': idx === stackedBars.length - 1,
                                        })}
                                        style={{
                                            height: `${bar.height}%`,
                                            backgroundColor: bar.color,
                                            bottom: `${stackedBars.slice(0, idx).reduce((acc, b) => acc + b.height, 0)}%`,
                                        }}
                                    ></div>
                                ))}
                            </div>
                            <div className="mt-2 text-base w-9 h-9 rounded-full font-medium flex items-center justify-center transition-colors bg-neutral-700 text-white group-hover:bg-neutral-600">
                                {label}
                            </div>
                        </div>
                    );
                })}
            </div>
        </div>
    );
};

export default CustomBarChar;
