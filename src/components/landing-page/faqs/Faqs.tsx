import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/react'
import {ArrowDownIcon, ArrowUpIcon} from '@heroicons/react/24/outline'
import React from "react";

interface Faqs {
    question: string;
    answer: React.ReactElement|null;
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
        <>
            question 2
        </>
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
        question: '1. What is MegaTrader?',
        answer: <Question1 />
    },
    {
        question: '2. How does MegaTrader work?',
        answer: <Question2 />
    },
    {
        question: '3. Can I trade on multiple accounts?',
        answer: <Question3 />
    },
    {
        question: '4. Is MegaTrader focused only on futures trading?',
        answer: <Question4 />
    },
    {
        question: '5. How much does it cost to get started with MegaTrader?',
        answer: <Question5 />
    },
    {
        question: '6. Is MegaTrader compliant with industry regulations?',
        answer: <Question6 />
    },
];

const FaqsSection = () => {
    return (
        <section className="mx-auto max-w-[1030px]">
            <h2 className="text-5xl text-white text-center my-8 font-light leading-[60px]">
                FREQUENTLY ASKED QUESTIONS
            </h2>
            <dl className="mt-10 space-y-6 divide-y divide-white/10">
                {faqs.map((faq) => (
                    <Disclosure key={faq.question} as="div" className="pt-6">
                        <dt>
                            <DisclosureButton
                                className="group flex w-full items-start justify-between text-left text-white">
                                <span className="text-base/7 font-semibold">{faq.question}</span>
                                <span className="ml-6 flex h-7 items-center">
                      <ArrowDownIcon aria-hidden="true" className="size-6 group-data-[open]:hidden"/>
                      <ArrowUpIcon aria-hidden="true" className="size-6 group-[&:not([data-open])]:hidden"/>
                    </span>
                            </DisclosureButton>
                        </dt>
                        <DisclosurePanel as="dd" className="mt-2 pr-12">
                            <p className="text-base/7 text-gray-300">{faq.answer}</p>
                        </DisclosurePanel>
                    </Disclosure>
                ))}
            </dl>
        </section>
    )
}


export default FaqsSection;