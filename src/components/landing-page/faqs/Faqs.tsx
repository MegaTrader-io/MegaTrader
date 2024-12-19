import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/react'
import {ArrowDownIcon, ArrowUpIcon} from '@heroicons/react/24/outline'
import React from "react";

interface Faqs {
    question: string;
    answer: React.ReactElement | null;
}

function Question1() {
    return (
        <>
            question 1
        </>
    )
}

function Question2() {
    return (
        <p className="text-base/7 text-gray-300">
            MegaTrader is a proprietary trading firm (prop firm) specializing in futures trading, offering traders
            cutting-edge tools and capital to maximize their success.
        </p>
    )
}

function Question3() {
    return (
        <>
            question 3
        </>
    )
}

function Question4() {
    return (
        <>
            question 4
        </>
    )
}

function Question5() {
    return (
        <>
            question 5
        </>
    )
}

function Question6() {
    return (
        <>
            question 6
        </>
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
                    <Disclosure key={faq.question} as="div" className="mb-2">
                        <DisclosureButton
                            className="group bg-[#1e1e1e] p-4 rounded-2xl flex w-full items-center justify-between text-left  text-white">
                            <span className="text-white text-xl font-light w-full leading-6 py-2">{faq.question}</span>
                            <span className="ml-6 flex h-7 items-center">
                                  <ArrowDownIcon aria-hidden="true" className="size-6 group-data-[open]:hidden"/>
                                  <ArrowUpIcon aria-hidden="true" className="size-6 group-[&:not([data-open])]:hidden"/>
                            </span>
                        </DisclosureButton>

                        <DisclosurePanel as="div">
                            {faq.answer}
                        </DisclosurePanel>
                    </Disclosure>
                ))}
            </div>
        </section>
    )
}


export default FaqsSection;