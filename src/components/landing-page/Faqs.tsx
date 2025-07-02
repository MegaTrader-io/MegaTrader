import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/react'
import React from "react";
import clsx from "clsx";

export interface FAQ {
    question: string
    answer: string
}

function ArrowUp({className}: { className: string }) {
    return <div className={className}>
        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_8223_24371" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="30"
                  height="30">
                <rect width="30" height="30" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_8223_24371)">
                <path d="M13.75 16.25H6.25V13.75H13.75V6.25H16.25V13.75H23.75V16.25H16.25V23.75H13.75V16.25Z"
                      fill="white"/>
            </g>
        </svg>
    </div>
}

function ArrowDown({className}: { className: string }) {
    return <div className={className}>
        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_8280_2980" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="30"
                  height="30">
                <rect width="30" height="30" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_8280_2980)">
                <path d="M6.25 16.25V13.75H23.75V16.25H6.25Z" fill="white"/>
            </g>
        </svg>
    </div>
}


const FaqsSection = ({className = '', faqs = []}: { className?: string, faqs: FAQ[] }) => {
    return (
        <div className={clsx(`mx-auto max-w-[1030px] space-y-8`, className)}>
            {faqs.map((faq, index) => (
                <Disclosure key={faq.question} as="div" className="group" defaultOpen={index === 0}>
                    <DisclosureButton
                        className="px-4 group-data-[open]:border-none group-data-[open]:border-transparent group-data-[open]:bg-[#131210] rounded-2xl flex w-full items-center flex-col text-left ">
                        <div className="flex w-full items-center justify-between">
                            <div
                                className="text-white text-xl font-light w-full leading-6 uppercase py-2">{faq.question}
                            </div>
                            <div className="text-white">
                                <ArrowUp aria-hidden="true" className="group-data-[open]:hidden"/>
                                <ArrowDown aria-hidden="true"
                                           className="group-[&:not([data-open])]:hidden"/>
                            </div>
                        </div>
                        <DisclosurePanel as="div">
                            <p className="text-stone-400 justify-start text-base font-medium leading-6 pt-2">
                                {faq.answer}
                            </p>
                        </DisclosurePanel>
                    </DisclosureButton>
                </Disclosure>
            ))}
        </div>
    )
}

export default FaqsSection;