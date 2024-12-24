import React, {useState} from "react";
import {CheckCircleIcon} from "@heroicons/react/16/solid";
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from "@headlessui/react";
import Link from "@/components/link";
import Image from "next/image";

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


const FlexibleFuturesTradingAndAnalytics = () => {
    const [selectedIndex, setSelectedIndex] = useState(0)

    return (
        <section className="mb-8 w-full px-16 bg-gradient-to-b from-[#1e1e1e] to-[#131210] rounded-2xl">
            <h2 className="text-5xl text-white text-center font-light leading-[60px] py-8">
                FLEXIBLE FUTURES<br/>
                <span className="text-mgt-primary leading-[60px]">TRADING AND ANALYTICS</span>
            </h2>
            <TabGroup onChange={setSelectedIndex}>
                <TabList className="flex gap-2 pb-8">
                    {Options.map((option, index) => (
                        <Tab
                            key={option.id}
                            className="text-left py-3 px-4 text-stone-400 w-full rounded-xl border border-stone-400  focus:outline-none data-[selected]:bg-[#f1a035] data-[selected]:border-transparent data-[selected]:text-black">
                            <div className="flex gap-2 items-center font-normal text-nowrap leading-6 text-base">
                                {selectedIndex === index &&
                                    <CheckCircleIcon aria-hidden="true"
                                                     className="w-6 h-6 text-black"/>} {option.title}
                            </div>
                        </Tab>
                    ))}
                </TabList>
                <TabPanels
                    className="h-[396px] w-full bg-[#1e1e1e] rounded-2xl shadow-[0px_20px_20px_20px_rgba(0,0,0,0.10)] justify-start items-center inline-flex overflow-hidden">
                    {Options.map(option => (
                        <TabPanel key={option.id} className="grid grid-cols-[511px_1fr] items-center">
                            <div className="p-8 space-y-8">
                                <h2 className="text-white text-2xl font-light uppercase leading-73123">{option.panel.title}</h2>
                                <p className="text-stone-400 text-xl font-normal leading-8">
                                    {option.panel.content}
                                </p>

                                <Link href={option.panel.button.href} className="bg-mgt-primary !text-black">
                                    {option.panel.button.text}
                                </Link>
                            </div>
                            <div>
                                <Image
                                    src={option.panel.image.url}
                                    alt={option.panel.title}
                                    width={641}
                                    height={396}
                                />
                            </div>
                        </TabPanel>
                    ))}
                </TabPanels>
            </TabGroup>
        </section>
    )
}

export default FlexibleFuturesTradingAndAnalytics;