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
        title: 'Finance & Trading',
        panel: {
            title: 'Automate & scale trading operations with ease.',
            content: 'Track trades, payouts, and performance in real-time. Integrate fiat and crypto transactions for efficient futures trading.',
            button: {
                text: 'GET STARTED',
                href: '#',
            },
            image: {
                url: '/assets/images/frame_monitor.svg'
            }
        }
    },
    {
        id: 2,
        title: 'Risk Management',
        panel: {
            title: 'Manage and analyze your risk exposure across trading accounts.',
            content: '\n' +
                'Monitor margin levels, stop-loss setups, and trading draw downs to maximize performance while staying secure.',
            button: {
                text: 'Explore Tools',
                href: '#',
            },
            image: {
                url: '/assets/images/frame_monitor.svg'
            }
        }
    },
    {
        id: 3,
        title: 'Compliance & Reporting',
        panel: {
            title: 'Streamline your compliance processes with automated reporting.',
            content: 'Access daily trade summaries, account funding details, and customizable financial projections to stay audit-ready.',
            button: {
                text: 'Download Reports',
                href: '#',
            },
            image: {
                url: '/assets/images/frame_monitor.svg'
            }
        }
    },
    {
        id: 4,
        title: 'Founders Dashboard',
        panel: {
            title: 'Empower your leadership team with a founder\'s view of all operations.',
            content: 'Get insights into trading activity, performance metrics, and account profitability at a glance.',
            button: {
                text: 'View Analytics',
                href: '#',
            },
            image: {
                url: '/assets/images/frame_monitor.svg'
            }
        }
    },
    {
        id: 5,
        title: 'Accounts & Contracts',
        panel: {
            title: 'Simplify the management of trading accounts and contracts.',
            content: '\n' +
                'Enable seamless onboarding for new traders, update terms, and monitor contract activity in one intuitive dashboard.',
            button: {
                text: 'Manage Accounts',
                href: '#',
            },
            image: {
                url: '/assets/images/frame_monitor.svg'
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