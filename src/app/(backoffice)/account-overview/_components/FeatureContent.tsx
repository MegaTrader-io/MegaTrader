'use client'

import React, {useEffect, useRef, useState} from 'react';
import Card from "@/components/Card";
import {Button} from "@/components/Button";
import ExclamationIcon from "@/components/ExclamationIcon";
import GaugeSVG from "@/app/(backoffice)/account-overview/_components/GaugeSVG";
import {formatCurrency} from "@/commons/utils";
import Tooltip from "@/components/Tooltip";
import TrendIndicator from "@/app/(backoffice)/account-overview/_components/TrendIndicator";
import {ChevronLeftIcon, ChevronRightIcon} from "@heroicons/react/16/solid";
import clsx from "clsx";

const Options: { id: string, label: string }[] = [
    {id: 'overview', label: 'Overview'},
    {id: 'e_mini_sp_500', label: 'E-mini S&P 500'},
    {id: 'e_mini_nasdaq_100', label: 'E-mini NASDAQ 100'},
    {id: 'e_mini_russell_2000', label: 'E-mini Russell 2000'},
    {id: 'e_mini_natural_gas', label: 'E-mini Natural Gas'},
    {id: 'nikkei_nkd', label: 'Nikkei NKD'},
    {id: 'australian_dollar', label: 'Australian Dollar'},
    {id: 'british_pound', label: 'British Pound'},
];

function FeatureContent() {
    const [selection, setSelection] = useState<string>('overview');
    const [showLeftGradient, setShowLeftGradient] = useState(false);
    const [showRightGradient, setShowRightGradient] = useState(false);
    const [showArrows, setShowArrows] = useState(false);
    const [chartMetrics, setChartMetrics] = useState({
        chart1: {value: 0, min: 0, max: 0},
        chart2: {value: 0, min: 0, max: 0},
        chart3: {value: 0, min: 0, max: 0},
    })
    const scrollContainerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const handleResize = () => {
            if (!scrollContainerRef.current) return;
            const container = scrollContainerRef.current;
            setShowArrows(container.scrollWidth > container.clientWidth);
            setShowLeftGradient(container.scrollLeft > 0);
            setShowRightGradient(container.scrollLeft + container.clientWidth < container.scrollWidth);
        };

        const handleScroll = handleResize;

        const container = scrollContainerRef.current;
        if (!container) return;

        const observer = new MutationObserver(handleResize);
        observer.observe(container, {childList: true, subtree: true});
        container.addEventListener('scroll', handleScroll);
        window.addEventListener('resize', handleResize);

        handleResize();

        return () => {
            observer.disconnect();
            container.removeEventListener('scroll', handleScroll);
            window.removeEventListener('resize', handleResize);
        };
    }, []);

    useEffect(() => {
        const {chart1, chart2, chart3} = (function () {
            const chart1 = {value: 0, min: 2.60, max: 1083.00};
            const chart2 = {value: 0, min: 1, max: 100};
            const chart3 = {value: 0, min: -3.24, max: -4354.50};

            chart1.value = parseFloat((Math.random() * (chart1.max - chart1.min) + chart1.min).toFixed(2));
            chart2.value = Math.random() * (chart2.max - chart2.min) + chart2.min;
            chart3.value = parseFloat((Math.random() * (chart1.max - chart1.min) + chart1.min).toFixed(2));

            return {
                chart1, chart2, chart3
            };
        })();

        setChartMetrics({chart1, chart2, chart3})
    }, [selection])

    const scrollLeft = () => {
        if (scrollContainerRef.current) {
            scrollContainerRef.current.scrollBy({left: -150, behavior: "smooth"});
        }
    };

    const scrollRight = () => {
        if (scrollContainerRef.current) {
            scrollContainerRef.current.scrollBy({left: 150, behavior: "smooth"});
        }
    };

    const handleButtonClick = (optionId: string) => {
        setSelection(optionId);
        if (!scrollContainerRef.current) return;

        const button = document.getElementById(`btn-${optionId}`);
        if (!button) return;

        const container = scrollContainerRef.current;
        const containerRect = container.getBoundingClientRect();
        const buttonRect = button.getBoundingClientRect();

        if (buttonRect.left < containerRect.left) {
            container.scrollBy({left: buttonRect.left - containerRect.left - 10, behavior: "smooth"});
        } else if (buttonRect.right > containerRect.right) {
            container.scrollBy({left: buttonRect.right - containerRect.right + 10, behavior: "smooth"});
        }
    };

    const changeOption = (ev: React.ChangeEvent<HTMLSelectElement>) => {
        const value = ev.target.value;
        setSelection(value)
    }

    return (
        <div className="w-full space-y-2 lg:space-y-2">
            <div className="hidden lg:flex">
                <div className="w-full flex items-center justify-between space-x-2 rounded-xl relative">
                    {showArrows && (
                        <Button
                            variant={'dark'}
                            onClick={scrollLeft}
                            icon={<ChevronLeftIcon className="h-6 w-6 text-white"/>}
                            className="w-12 rounded-full bg-neutral-800 hover:bg-neutral-700 transition"
                        >
                        </Button>
                    )}

                    {showLeftGradient && (
                        <div
                            className="w-12 h-full bg-gradient-to-l from-transparent to-[#131210] absolute left-[48px] z-10"></div>
                    )}

                    <div
                        ref={scrollContainerRef}
                        className="flex gap-2 overflow-x-auto scrollbar-hide px-0.5 py-1 w-full"
                    >
                        {Options.map(option => (
                            <Button
                                id={`btn-${option.id}`}
                                variant={option.id === selection ? "primary" : 'dark'}
                                key={option.id}
                                onClick={() => handleButtonClick(option.id)}
                                className={clsx(`whitespace-nowrap !normal-case`, {'text-black': option.id === selection, '!text-stone-400': option.id !== selection})}
                            >
                                {option.label}
                            </Button>
                        ))}
                    </div>

                    {showRightGradient && (
                        <div
                            className="w-12 h-full bg-gradient-to-r from-transparent to-[#131210] absolute right-[56px]"></div>
                    )}

                    {showArrows && (
                        <Button
                            variant={'dark'}
                            icon={<ChevronRightIcon className="h-6 w-6 text-white"/>}
                            onClick={scrollRight}
                            className="w-12 rounded-full bg-neutral-800 hover:bg-neutral-700 transition"
                        >
                        </Button>
                    )}
                </div>
            </div>

            <div className="lg:hidden relative w-full">
                <select
                    className="w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none"
                    onChange={changeOption}>
                    {Options.map(option => (
                        <option key={option.id} value={option.id}>
                            {option.label}
                        </option>
                    ))}
                </select>
                <div className="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_5269_2288" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
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

            <Card className="w-full space-y-8">
                <div className="gap-4 flex justify-between items-center">
                    <div
                        className="px-3 sm:mb-auto py-1 gap-2 grid grid-cols-[auto_24px_auto] items-center bg-stone-800 rounded-2xl">
                        <div className="text-xs font-medium text-white truncate uppercase leading-normal">
                            AVG. PROFITABILITY PER TRADE
                        </div>

                        <GenericExclamationTooltip>
                            <>
                                <div className="text-white text-xs font-bold leading-tight">Avg. Profitability per
                                    trade
                                </div>
                                <p className="leading-tight">
                                    Average Profitability Per Trade (APPT) is the average amount you can expect to win
                                    or lose per trade
                                    based on your Average Winning Trade, Average Losing Trade, and Winning Trade %.
                                    Average profit or
                                    loss includes all fees and commissions.
                                </p>
                                <p className="leading-tight">
                                    <span className="text-white">Tip:</span> This is your average P&L per trade. Keep
                                    this number
                                    positive to stay profitable.
                                </p>
                            </>
                        </GenericExclamationTooltip>

                        <TrendIndicator value={-127.16}/>
                    </div>
                    <QuestionTooltip/>
                </div>
                <div
                    className="space-y-[35px] md:space-y-0 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[288px_288px_288px] justify-between w-full px-4">
                    <div className="gap-2 flex flex-col">
                        <div className="flex gap-2 justify-center">
                            <span className="text-stone-400 text-base font-normal">Avg. Winning Trade</span>
                            <GenericTooltip classNameIcon={'text-stone-400'}>
                                <>
                                    <div className="text-white text-xs font-bold leading-tight">Avg. Winning trade</div>
                                    <p className="leading-tight">
                                        Average profit of all winning trades
                                    </p>
                                    <p className="leading-tight">
                                        <span className="text-white">Tip:</span> To be a successful trader, profits
                                        should always be larger than losses.
                                    </p>
                                </>
                            </GenericTooltip>
                        </div>

                        <div
                            className="h-[288px] w-[288px] mx-auto justify-center items-center flex">
                            <GaugeSVG
                                value={chartMetrics.chart1.value === 0 ? 0 : chartMetrics.chart1.value * 100 / chartMetrics.chart1.max}
                                minValue={formatCurrency(chartMetrics.chart1.min)}
                                maxValue={formatCurrency(chartMetrics.chart1.max)}
                                centerValue={`${formatCurrency(chartMetrics.chart1.value)}`}
                            />
                        </div>
                    </div>
                    <div className="gap-2 flex  flex-col">
                        <div className="flex gap-2 justify-center">
                            <span className="text-stone-400 text-base font-normal">Winning Trade %</span>
                            <GenericTooltip classNameIcon={'text-stone-400'}>
                                <>
                                    <div className="text-white text-xs font-bold leading-tight">Winning trade %</div>
                                    <p className="leading-tight">
                                        Number of winning trades out of all your trades (excludes breakeven trades)
                                    </p>
                                    <p className="leading-tight">
                                        <span className="text-white">Tip:</span> Over time, your winning trade
                                        percentage will likely be near 50%. Knowing that, think about how important your
                                        average winning trade and average losing trade are - keep the math on your side.
                                    </p>
                                </>
                            </GenericTooltip>
                        </div>
                        <div
                            className="h-[288px] w-[288px] mx-auto justify-center items-center flex">
                            <GaugeSVG
                                value={chartMetrics.chart2.value}
                                minValue={chartMetrics.chart2.min.toString()}
                                maxValue={chartMetrics.chart2.max.toString()}
                                centerValue={`${chartMetrics.chart2.value.toFixed(0)}%`}
                            />
                        </div>
                    </div>
                    <div className="gap-2 flex  flex-col">
                        <div className="flex gap-2 justify-center">
                            <span className="text-stone-400 text-base font-normal">Avg. Losing Trade</span>
                            <GenericTooltip classNameIcon={'text-stone-400'}>
                                <>
                                    <div className="text-white text-xs font-bold leading-tight">Avg. Losing Trade</div>
                                    <p className="leading-tight">
                                        Average loss of all losing trades.
                                    </p>
                                    <p className="leading-tight">
                                        <span className="text-white">Tip:</span> Focusing on smaller risks and getting
                                        out of bad trades early is how professional traders stay in the game. When the
                                        trade is proven wrong, get out!
                                    </p>
                                </>
                            </GenericTooltip>
                        </div>

                        <div
                            className="h-[288px] w-[288px] mx-auto justify-center items-center flex">
                            <GaugeSVG
                                value={Math.abs(chartMetrics.chart3.value === 0 ? 0 : chartMetrics.chart3.value * 100 / chartMetrics.chart3.max)}
                                minValue={formatCurrency(chartMetrics.chart3.min)}
                                maxValue={formatCurrency(chartMetrics.chart3.max)}
                                centerValue={`${formatCurrency(chartMetrics.chart3.value * -1)}`}
                            />
                        </div>
                    </div>
                </div>


                <div className="w-full flex">
                    <div
                        className="max-w-[300px] px-3 py-1 gap-2 grid grid-cols-[auto_24px_auto] items-center justify-center    bg-stone-800 rounded-2xl">
                        <div
                            className="text-xs truncate font-medium text-white uppercase leading-normal">
                            REWARD-TO-RISK RATIO
                        </div>

                        <GenericExclamationTooltip>
                            <>
                                <div className="text-white text-xs font-bold leading-tight">Reward-to-risk ratio
                                </div>
                                <p className="leading-tight">
                                    Measures the potential reward (profit) you achieve per trade VS the risk (losses)
                                    you
                                    take.
                                </p>
                                <p className="leading-tight">
                                    <span className="text-white">Tip:</span> One of the most important metrics to
                                    successful trading! Less risk and more reward increases your probability of
                                    continued profitability.
                                </p>
                            </>
                        </GenericExclamationTooltip>

                        <div className="flex items-center">
                            <div
                                className="text-white text-xs font-medium uppercase leading-normal">
                                1:1.52
                            </div>
                        </div>
                    </div>
                </div>
            </Card>
        </div>
    );
}


function GenericTooltip({children, classNameIcon = 'text-white'}: {
    classNameIcon?: string,
    children?: React.ReactElement
}) {
    return (
        <Tooltip
            content={
                <div className="space-y-2">
                    {children}
                </div>}>
            <ExclamationIcon className={classNameIcon}/>
        </Tooltip>
    )
}

function QuestionTooltip() {
    return (
        <Tooltip
            content={
                <div className="space-y-2">
                    <div className="text-white text-xs font-bold leading-tight">Keep the math on your side</div>
                    <p className="leading-tight">
                        Average winning trades should always be grater than average losing trades, and your
                        reward/risk
                        ratio
                        should have a direct correlation to your winning trade percentage. For example: if you
                        apply a 2:1
                        reward to risk ratio and your winning trade percentage is 50%, congratulations, you’re a
                        profitable
                        trader!
                    </p>
                </div>
            }>
            <ExclamationIcon className="text-white"/>
        </Tooltip>
    );
}

function GenericExclamationTooltip({children, classNameIcon = 'text-white'}: {
    children?: React.ReactElement,
    classNameIcon?: string,
}) {
    return (
        <Tooltip
            content={
                <div className="space-y-2">
                    {children}
                </div>}>
            <ExclamationIcon className={classNameIcon}/>
        </Tooltip>
    )
}

export default FeatureContent;