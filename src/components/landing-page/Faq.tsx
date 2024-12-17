import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/react'
import {ArrowDownIcon, ArrowUpIcon} from '@heroicons/react/24/outline'

interface Faq {
    question: string;
    answer: string;
}

const faqs: Faq[] = [
    {
        question: '1. What is MegaTrader?',
        answer: 'xxxx'
    },
    {
        question: '2. How does MegaTrader work?',
        answer: 'xxxx'
    },
    {
        question: '3. Can I trade on multiple accounts?',
        answer: 'xxxx'
    },
    {
        question: '4. Is MegaTrader focused only on futures trading?',
        answer: 'xxxx'
    },
    {
        question: '5. How much does it cost to get started with MegaTrader?',
        answer: 'xxxx'
    },
    {
        question: '6. Is MegaTrader compliant with industry regulations?',
        answer: 'xxxx'
    },
];

const FaqSection = () => {
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


export default FaqSection;