'use client'

import React, {useEffect, useRef, useState} from 'react';
import clsx from "clsx";
import {AnimatePresence, motion} from "framer-motion";
import {ArrowUpRightIcon} from "@heroicons/react/16/solid";
import {createPortal} from "react-dom";
import NoData from "@/components/NoData";

interface SeriesItem {
    name: string;
    data: number[];
    color: string;
}

export interface ChartBarProps {
    internalId: number;
    xAxis: string[];
    series: SeriesItem[];
    yAxis?: number[];
}

const CustomBarChar: React.FC<ChartBarProps> = ({internalId, xAxis, series, yAxis}) => {
    const [selectedIndex, setSelectedIndex] = useState<number | null>(null);
    const [hoverIndex, setHoverIndex] = useState<number | null>(null);
    const barRefs = useRef<(HTMLDivElement | null)[]>([]);
    const panelChartWrapperRef = useRef<HTMLDivElement | null>(null);
    const panelBarRefs = useRef<HTMLDivElement | null>(null);
    const panelLinesRef = useRef<HTMLDivElement | null>(null);

    const updatePanelWidth = () => {
        if (!panelLinesRef.current) return;
        const panelLines = panelLinesRef.current;

        panelLines.style.width = ``;

        if (!panelChartWrapperRef.current) return;
        const panelChartWrapper = panelChartWrapperRef.current;

        if (!panelBarRefs.current) return;
        const panelBars = panelBarRefs.current;

        const maxWidthLabel = Math.max(...[...panelLines.querySelectorAll('span')].map(element => element.getBoundingClientRect().width), 0);

        const marginLeft = 40;
        panelBars.style.marginLeft = `${marginLeft}px`;
        panelBars.style.width = `${panelBars.style.width || 0 - marginLeft}px`;
        panelLines.style.width = `${panelChartWrapper.scrollWidth + maxWidthLabel - 16 - 8}px`;
    };

    useEffect(() => {
        updatePanelWidth();

        console.info('internalId', internalId);
        window.addEventListener('resize', updatePanelWidth);

        return () => {
            window.removeEventListener('resize', updatePanelWidth);
        };
    }, [internalId]);

    if (!series?.length || !xAxis?.length) {
        return <NoData/>
    }

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

    function getRectByBarRef(index: number) {
        const barSelected: HTMLDivElement = barRefs.current[index]!;
        let barHeight = 0;
        barSelected.querySelectorAll('.bar-item').forEach(bar => {
            barHeight += Number(bar.getBoundingClientRect().height);
        });
        return {height: barHeight, barSelected: barSelected.getBoundingClientRect()};
    }

    return (
        <div ref={panelChartWrapperRef} className="chart-wrapper relative w-full text-white">
            <div ref={panelLinesRef}
                 className="panel-lines absolute top-0 -bottom-1 right-0 left-0 z-0 overflow-hidden"
            >
                {computedYAxis.map((y, idx) => {
                    // Establecer el 0 en el 2% y el máximo (último) en el 95%
                    let bottomPosition = (y / maxY) * 100;
                    if (y === 0) {
                        bottomPosition = 6; // El valor de 0 en y se coloca en 2%
                    } else if (y === maxY) {
                        bottomPosition = 98; // El valor máximo de y se coloca en 95%
                    }

                    return (
                        <div
                            key={idx}
                            className={clsx(
                                'absolute w-full text-sm font-medium text-stone-500 ml-10 leading-tight',
                                {'border-t border-white/10': idx > 0}
                            )}
                            style={{bottom: `${bottomPosition}%`}}
                        >
                             <span className="absolute -translate-x-10 -translate-y-1/2 min-w-[25px] text-right">
                                 {y}
                             </span>
                        </div>
                    );
                })}
            </div>
            <div ref={panelBarRefs} className="bars relative flex gap-2 h-full">
                {xAxis.map((label, index) => {
                    const stackedBars = series.map((serie) => ({
                        height: (serie.data[index] / maxY) * 90,
                        total: serie.data[index],
                        color: serie.color,
                    }));
                    const isSelected = selectedIndex === index;
                    const isHovered = hoverIndex === index;

                    let rect: { height: number; barSelected: DOMRect } | undefined = undefined;

                    if (isSelected) {
                        rect = getRectByBarRef(selectedIndex!);
                    }
                    if (isHovered) {
                        rect = getRectByBarRef(hoverIndex!);
                    }

                    return (
                        <div key={index}
                             id={`bar_${index}`}
                             onMouseEnter={() => setHoverIndex(index)}
                             onMouseLeave={() => setHoverIndex(null)}
                             onClick={() => setSelectedIndex(index)}
                             className="grid grid-rows-[1fr_36px] justify-center gap-2 items-center w-full h-[387px]"
                        >
                            <div
                                ref={(el) => {
                                    if (el) {
                                        barRefs.current[index] = el;
                                    }
                                }}
                                className="bar_container flex flex-col justify-end w-[36px] relative h-full"
                            >
                                <>
                                    {(rect && (isHovered || isSelected)) && (
                                        createPortal(
                                            <AnimatePresence>
                                                <motion.div
                                                    initial={{
                                                        opacity: 0,
                                                        x: rect.barSelected.left + rect.barSelected.width / 2 + (window.scrollX || 0) - 90,
                                                        y: rect.barSelected.top + window.scrollY - (rect.height - 409 + 88 + 88 + 8)
                                                    }}
                                                    animate={{
                                                        opacity: 1,
                                                        x: rect.barSelected.left + rect.barSelected.width / 2 + (window.scrollX || 0) - 90,
                                                        y: rect.barSelected.top + window.scrollY - (rect.height - 409 + 88 + 88 + 8)
                                                    }}
                                                    exit={{
                                                        opacity: 0,
                                                        x: rect.barSelected.left + rect.barSelected.width / 2 + (window.scrollX || 0) - 90,
                                                        y: rect.barSelected.top + window.scrollY - (rect.height - 409 + 88 + 88 + 8)
                                                    }}
                                                    className={clsx(
                                                        'tooltip shadow-[0px_16px_16px_16px_rgba(0,0,0,0.20)]',
                                                        'border border-neutral-700 absolute z-10 rounded-2xl inline-flex flex-col justify-center items-center gap-2'
                                                    )}
                                                    style={{position: 'absolute'}}
                                                >
                                                    <div
                                                        className="rounded-2xl relative bg-[#131210] flex flex-col space-y-2 p-4">
                                                        {stackedBars.map((serie, serieIndex) => (
                                                            <div key={`row_${serieIndex}`} className="flex">
                                                                <div
                                                                    className="grid grid-cols-[24px_1fr] gap-2 items-center">
                                                                    <div
                                                                        className="w-6 h-6 flex justify-center items-center">
                                                                        <div
                                                                            className={`rounded-full size-3/4 ${serie.color}`}>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        className="text-base w-[62px] truncate text-white">
                                                                        {serie.total}
                                                                    </div>
                                                                </div>
                                                                <div className="flex items-center">
                                                                    <div className="text-base text-stone-400">
                                                                        108
                                                                    </div>
                                                                    <ArrowUpRightIcon className="w-6 h-6 text-white"/>
                                                                </div>
                                                            </div>
                                                        ))}
                                                    </div>

                                                    <div
                                                        className="absolute bg-[#131210] border border-neutral-700 -bottom-[12px] w-6 h-6 -z-[1] left-[50%]"
                                                        style={{transform: 'translate(-50%) rotate(45deg)'}}
                                                    />
                                                </motion.div>
                                            </AnimatePresence>,
                                            document.body
                                        )
                                    )}
                                </>

                                {stackedBars.map((bar, idx) => (
                                    <div
                                        key={idx}
                                        className={clsx('bar-item w-[36px] flex', {
                                            'rounded-tl-[64px] rounded-tr-[64px]': idx === 0,
                                            'rounded-bl-[64px] rounded-br-[64px]': idx === stackedBars.length - 1,
                                            [bar.color]: true
                                        })}
                                        style={{
                                            height: `${bar.height}%`,
                                            bottom: `${stackedBars.slice(0, idx).reduce((acc, b) => acc + b.height, 0)}%`,
                                        }}
                                    >
                                        <span className={`bg-[${bar.color}]`}></span>
                                    </div>
                                ))}
                            </div>
                            <div
                                className="h-9 w-9 rounded-full font-medium flex items-center justify-center transition-colors bg-neutral-700 text-white group-hover:bg-neutral-600"
                            >
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
