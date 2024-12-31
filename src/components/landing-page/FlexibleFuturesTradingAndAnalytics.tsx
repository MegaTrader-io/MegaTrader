import React, {useState} from "react";
import {CheckCircleIcon} from "@heroicons/react/16/solid";
import Link from "@/components/link";
import Image from "next/image";
import clsx from "clsx";

interface Option {
    id: number
    title: string
    panel: {
        title: string,
        content: string,
        button: {
            text: string,
            href: string,
        },
        image: {
            url: string,
        }
    }
}

const Options: Option[] = [
    {
        id: 1,
        title: 'Trading Edge',
        panel: {
            title: 'Unlock Precision Trading',
            content: 'Leverage advanced tools designed for precision and performance. Real-time insights and analytics put you ahead in the market.',
            button: {
                text: 'GET STARTED',
                href: '#',
            },
            image: {
                url: '/assets/images/flexible-futures/trading-edge.svg'
            }
        }
    },
    {
        id: 2,
        title: 'Market Insights',
        panel: {
            title: 'Your Window to the Market',
            content: 'Gain access to comprehensive data, trends, and insights. Make informed decisions effortlessly with actionable information.',
            button: {
                text: 'GET STARTED',
                href: '#',
            },
            image: {
                url: '/assets/images/flexible-futures/market-insights.svg'
            }
        }
    },
    {
        id: 3,
        title: 'Quick Execution',
        panel: {
            title: 'Speed Meets Reliability',
            content: 'Execute trades in milliseconds with robust and dependable infrastructure, ensuring you\'re always ahead of the curve.',
            button: {
                text: 'GET STARTED',
                href: '#',
            },
            image: {
                url: '/assets/images/flexible-futures/quick-execution.svg'
            }
        }
    },
    {
        id: 4,
        title: 'Custom Dashboard',
        panel: {
            title: 'Tailored for You',
            content: 'Personalize your workspace to fit your style and needs. Optimize your tools and environment for maximum efficiency.',
            button: {
                text: 'GET STARTED',
                href: '#',
            },
            image: {
                url: '/assets/images/flexible-futures/custom-dashboard.svg'
            }
        }
    },
    {
        id: 5,
        title: 'Trade Confidence',
        panel: {
            title: 'Empowered by Excellence',
            content: 'Built on a foundation of security, speed, and support, giving you the confidence to navigate the market like a pro.',
            button: {
                text: 'GET STARTED',
                href: '#',
            },
            image: {
                url: '/assets/images/flexible-futures/trade-confidence.svg'
            }
        }
    }
]

function OptionPanel({option}: { option: Option }) {
    return <div
        className="lg:h-[396px] w-full bg-[#1e1e1e] rounded-2xl shadow-[0px_20px_20px_20px_rgba(0,0,0,0.10)] justify-start items-center inline-flex overflow-hidden">
        <div className="grid lg:grid-cols-[511px_1fr] lg:items-center">
            <div className="p-8 space-y-8 order-last lg:order-none">
                <h2 className="text-white text-2xl font-light uppercase leading-7">{option.panel.title}</h2>
                <p className="text-stone-400 text-xl font-normal leading-loose lg:leading-8">
                    {option.panel.content}
                </p>

                <Link href={option.panel.button.href} className="bg-mgt-primary !text-black">
                    {option.panel.button.text}
                </Link>
            </div>
            <div className="order-first lg:order-none">
                <Image
                    className="w-[308px] mx-auto lg:w-[641px]"
                    src={option.panel.image.url}
                    alt={option.panel.title}
                    width={641}
                    height={396}
                />
            </div>
        </div>
    </div>
}

const FlexibleFuturesTradingAndAnalytics = ({className = ''}: { className?: string }) => {
    const [optionID, setOptionID] = useState(1)

    return (
        <section
            className={clsx('mb-8 w-full lg:px-16 bg-gradient-to-b from-[#1e1e1e] to-[#131210] rounded-2xl', className)}>
            <h2 className="text-5xl text-white text-center font-light leading-[60px] py-8">
                FLEXIBLE FUTURES<br/>
                <span className="text-mgt-primary leading-[60px]">TRADING AND ANALYTICS</span>
            </h2>
            <div>
                <div className="gap-2 pb-8 hidden lg:flex justify-around">
                    {Options.map((option) => (
                        <button key={option.id} onClick={() => setOptionID(option.id)}
                                className={clsx('group gap-2 hidden lg:flex w-full text-left text-stone-400 rounded-xl focus:outline-none', {'is-selected': option.id === optionID})}>
                            <div
                                className="py-3 px-4 rounded-xl border border-stone-400 w-full group-[.is-selected]:bg-[#f1a035] group-[.is-selected]:border-transparent group-[.is-selected]:text-black">
                                <div className="flex gap-2 items-center font-normal text-nowrap leading-6 text-base">
                                    {option.id === optionID &&
                                        <CheckCircleIcon aria-hidden="true"
                                                         className="w-6 h-6 text-black"/>} {option.title}
                                </div>
                            </div>
                        </button>
                    ))}
                </div>

                <select
                    className="inline-block mb-8 h-12 px-4 py-3 md:hidden
                    relative w-full appearance-none rounded-lg sm:py-[calc(theme(spacing[1.5])-1px)] pl-[calc(theme(spacing[3.5])-1px)] pr-[calc(theme(spacing.10)-1px)] sm:pl-[calc(theme(spacing.3)-1px)] sm:pr-[calc(theme(spacing.9)-1px)] [&_optgroup]:font-semibold text-base/6 text-zinc-950 placeholder:text-zinc-500 sm:text-sm/6 dark:text-white dark:*:text-white border border-zinc-950/10 data-[hover]:border-zinc-950/20 dark:border-white/10 dark:data-[hover]:border-white/20 bg-transparent dark:bg-white/5 dark:*:bg-zinc-800 focus:outline-none data-[invalid]:border-red-500 data-[invalid]:data-[hover]:border-red-500 data-[invalid]:dark:border-red-600 data-[invalid]:data-[hover]:dark:border-red-600 data-[disabled]:border-zinc-950/20 data-[disabled]:opacity-100 dark:data-[hover]:data-[disabled]:border-white/15 data-[disabled]:dark:border-white/15 data-[disabled]:dark:bg-white/[2.5%]"
                    value={optionID}
                    onChange={(e) => setOptionID(Number(e.target.value))}>
                    {Options.map((option, index) => (
                        <option key={index} value={option.id}>
                            {option.title}
                        </option>
                    ))}
                </select>

                <OptionPanel option={Options.find(o => o.id === optionID)!}/>
            </div>
        </section>
    )
}

export default FlexibleFuturesTradingAndAnalytics;