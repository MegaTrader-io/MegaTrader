import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/react'
import {ArrowDownIcon, ArrowUpIcon} from '@heroicons/react/24/outline'
import React from "react";

interface Faqs {
    question: string;
    answer: React.ReactElement | null;
}

function Question1() {
    return (
        <p className="text-base/7 text-gray-300">
            MegaTrader is a proprietary trading firm (prop firm) specializing in futures trading, offering traders
            cutting-edge tools and capital to maximize their success.
        </p>
    )
}

function Question2() {
    return (
        <p className="text-base/7 text-gray-300">
            MegaTrader provides funded futures trading accounts to evaluate traders' skills. If you perform
            successfully, you can earn profits without risking your own capital.
        </p>
    )
}

function Question3() {
    return (
        <p className="text-base/7 text-gray-300">
            Yes! MegaTrader allows you to trade on multiple accounts simultaneously, offering flexibility and
            scalability for skilled traders.
        </p>
    )
}

function Question4() {
    return (
        <p className="text-base/7 text-gray-300">
            Absolutely. MegaTrader is exclusively dedicated to futures trading, ensuring you have access to
            specialized tools, resources, and opportunities tailored for this market.
        </p>
    )
}

function Question5() {
    return (
        <div className="text-left">
            <p className="text-base/7 text-gray-300">
                MegaTrader offers three account options:<br/>
                You can choose the plan that best fits your trading goals and budget.<br/>
                - Basic Plan: $89.99/month for a $50k account<br/>
                - Premium Plan: $149.99/month for a $100k account<br/>
                - Unlimited Plan: $199.99/month for a $150k account<br/>
            </p>
        </div>
    )
}

function Question6() {
    return (
        <p className="text-base/7 text-gray-300">
            Yes! MegaTrader adheres to all industry regulations and compliance standards to provide a safe,
            secure, and professional trading environment.
        </p>
    )
}

const faqs: Faqs[] = [
    {
        question: '1. WHAT IS MEGATRADER?',
        answer: <Question1/>
    },
    {
        question: '2. HOW DOES MEGATRADER WORK?',
        answer: <Question2/>
    },
    {
        question: '3. CAN I TRADE ON MULTIPLE ACCOUNTS?',
        answer: <Question3/>
    },
    {
        question: '4. IS MEGATRADER FOCUSED ONLY ON FUTURES TRADING?',
        answer: <Question4/>
    },
    {
        question: '5. HOW MUCH DOES IT COST TO GET STARTED WITH MEGATRADER?',
        answer: <Question5/>
    },
    {
        question: '6. IS MEGATRADER COMPLIANT WITH INDUSTRY REGULATIONS?',
        answer: <Question6/>
    },
];

const FaqsSection = () => {
    return (
        <section className="mx-auto max-w-[1030px]">
            <h2 className="text-5xl text-white text-center py-8 font-light leading-[60px]">
                FREQUENTLY ASKED QUESTIONS
            </h2>
            <div>
                {faqs.map((faq) => (
                    <Disclosure key={faq.question} as="div" className="mb-2 group">
                        <DisclosureButton
                            className=" bg-[#1e1e1e] p-4 group-data-[open]:border group-data-[open]:border-neutral-700    group-data-[open]:bg-[#131210] rounded-2xl flex w-full items-center flex-col text-left ">
                            <div className="flex  w-full items-center justify-between">
                                <div
                                    className="text-white text-xl font-light w-full leading-6 py-2">{faq.question}
                                </div>
                                <div className="w-6 h-6 items-center  text-white">
                                    <ArrowDownIcon aria-hidden="true" className="w-6 h-6 group-data-[open]:hidden"/>
                                    <ArrowUpIcon aria-hidden="true"
                                                 className="w-6 h-6 group-[&:not([data-open])]:hidden"/>
                                </div>
                            </div>
                            <DisclosurePanel as="div" className="text-base text-white py-2 text-left w-full">
                                {faq.answer}
                            </DisclosurePanel>
                        </DisclosureButton>
                    </Disclosure>
                ))}
            </div>
        </section>
    )
}


export default FaqsSection;