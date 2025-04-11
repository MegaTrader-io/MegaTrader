'use client'

import React, {useState} from 'react';
import clsx from "clsx";
import {AnimatePresence, motion} from "framer-motion";
import {ArrowUpRightIcon} from "@heroicons/react/16/solid";

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

const CustomBarChar: React.FC<ChartBarProps> = ({xAxis, series, yAxis}) => {
    const [selectedIndex, setSelectedIndex] = useState<number | null>(null);
    const [hoverIndex, setHoverIndex] = useState<number | null>(null);

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
                            {'border-t border-white/10': idx > 0}
                        )}
                        style={{bottom: `${(y / maxY) * 100}%`}}
                    >
            <span className="absolute -translate-x-10 -translate-y-1/2 text-right">
              {y}
            </span>
                    </div>
                ))}
            </div>

            <div className="relative flex gap-2 h-full w-full">
                {xAxis.map((label, index) => {
                    const stackedBars = series.map((serie) => ({
                        height: (serie.data[index] / maxY) * 90,
                        color: serie.color,
                    }));
                    const isSelected = selectedIndex === index;
                    const isHovered = hoverIndex === index;

                    return (
                        <div key={index}
                             id={`bar_${index}`}
                             onMouseEnter={() => setHoverIndex(index)}
                             onMouseLeave={() => setHoverIndex(null)}
                             onClick={() => setSelectedIndex(index)}
                             className="flex flex-col items-center justify-end flex-1 max-h-[387px]">
                            <div className="flex flex-col justify-end w-[36px] relative h-full">
                                <AnimatePresence>
                                    {(index == 3 || isHovered || isSelected) && (
                                        <motion.div
                                            initial={{opacity: 0, y: 10}}
                                            animate={{opacity: 1, y: 0}}
                                            exit={{opacity: 0, y: 10}}
                                            className="border border-neutral-700 absolute
                                             z-10 !translate-x-[-42%] translate-y-[-230px] rounded-2xl
                                                inline-flex flex-col justify-center
                                                 items-center gap-2"
                                        >
                                            <div
                                                className="rounded-2xl relative bg-[#131210] flex flex-col space-y-2 p-4">
                                                <div className="flex">
                                                    <div className="grid grid-cols-[24px_1fr] gap-2 items-center">
                                                        <div className={'w-6 h-6 flex justify-center items-center'}>
                                                            <div className={'rounded-full size-3/4 bg-[#3b82f6]'}></div>
                                                        </div>
                                                        <div className="text-base w-[62px] truncate">
                                                            387
                                                        </div>
                                                    </div>
                                                    <div className="flex items-center">
                                                        <div className="text-base text-stone-400">
                                                            108
                                                        </div>
                                                        <ArrowUpRightIcon className="w-6 h-6 text-white"/>
                                                    </div>
                                                </div>
                                                <div className="flex">
                                                    <div className="flex gap-2 items-center">
                                                        <div className={'w-6 h-6 flex justify-center items-center'}>
                                                            <div className={'rounded-full size-3/4 bg-[#14b8a6]'}></div>
                                                        </div>
                                                        <div className="text-base w-[62px] truncate">
                                                            1250
                                                        </div>
                                                    </div>
                                                    <div className="flex items-center">
                                                        <div className="text-base text-stone-400">
                                                            247
                                                        </div>
                                                        <ArrowUpRightIcon className="w-6 h-6 text-white"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                className="absolute bg-[#131210] border border-neutral-700 -bottom-[12px] w-6 h-6 -z-[1] left-[50%]"
                                                style={{'transform': 'translate(-50%) rotate(45deg)'}}>
                                            </div>
                                        </motion.div>
                                    )}
                                </AnimatePresence>

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
                            <div
                                className="mt-2 text-base w-9 h-9 rounded-full font-medium flex items-center justify-center transition-colors bg-neutral-700 text-white group-hover:bg-neutral-600">
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
