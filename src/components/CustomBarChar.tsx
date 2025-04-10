import React from 'react';
import clsx from "clsx";

interface SeriesItem {
    name: string
    data: number[]
    color: string
}

export interface ChartBarProps {
    xAxis: string[]
    yAxis: number[]
    series: SeriesItem[]
}

export const CustomBarChar: React.FC<ChartBarProps> = ({xAxis, yAxis, series}) => {
    if (!series?.length || !xAxis?.length || !yAxis?.length) {
        return <p className="text-red-500">No data to display.</p>
    }

    const maxY = Math.max(...yAxis)

    return (
        <div className="relative w-full h-full text-white px-16">
            <div className="absolute inset-0 z-0 -bottom-[1px] my-10">
                {yAxis.map((y, idx) => {
                    return <div
                        key={idx}
                        className={clsx('absolute w-full text-sm font-medium text-stone-500 ml-10 leading-tight', {'border-t border-white/10': idx > 0})}
                        style={{
                            bottom: `${(y / maxY) * 100}%`,
                        }}
                    >
                        <span className="absolute -translate-x-10 -translate-y-1/2 text-right">{y}</span>
                    </div>
                })}
            </div>

            <div className="relative z-10 flex gap-2 h-full w-full">
                {xAxis.map((label, i) => {
                    const stackedBars = series.map((serie) => ({
                        height: (serie.data[i] / maxY) * 100,
                        color: serie.color,
                    }))

                    return (
                        <div key={i} className="flex flex-col items-center justify-end flex-1 h-[387px]">
                            <div className="flex flex-col justify-end w-[36px] relative h-[343px]">
                                {stackedBars.map((bar, idx) => (
                                    <div
                                        key={idx}
                                        title={idx.toString()}
                                        className={clsx('w-[36px] flex', {
                                            'rounded-bl-[64px] rounded-br-[64px]': idx === 1,
                                            'rounded-tl-[64px] rounded-tr-[64px]': idx === 0,
                                        })}
                                        style={{
                                            height: `${bar.height}%`,
                                            backgroundColor: bar.color,
                                            bottom: `${stackedBars
                                                .slice(0, idx)
                                                .reduce((acc, b) => acc + b.height, 0)}%`,
                                        }}>
                                    </div>
                                ))}
                            </div>
                            <span
                                className="mt-2 text-base w-9 h-9 rounded-full font-medium flex items-center justify-center transition-colors bg-neutral-700 text-white group-hover:bg-neutral-600">{label}</span>
                        </div>
                    )
                })}
            </div>
        </div>
    );
}