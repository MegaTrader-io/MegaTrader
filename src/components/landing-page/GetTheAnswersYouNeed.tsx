import React, {useState} from 'react';
import Faqs from "@/components/landing-page/Faqs";
import clsx from "clsx";
import {faqsData} from "@/commons/data";
import Card from "@/components/Card";
import {CopyButton, DefaultCopyIcon} from "@/components/CopyButton";

const defaultCategory = 'Getting Started';

const categories = [
    {
        name: 'Getting Started'
    },
    {
        name: 'Pricing & Payouts'
    },
    {
        name: 'Affiliate Program'
    },
    {
        name: 'Platform & Features'
    },
    {
        name: 'Account & Compliance'
    },
]

const email = 'support@megatrader.io';

function GetTheAnswersYouNeed() {
    const [categorySelected, setCategorySelected] = useState<string>(defaultCategory)

    const category = faqsData.find((item => item.category === categorySelected))!;

    return (
        <section id="faq" className="space-y-12 px-4">
            <div className="w-full space-y-4">
                <div
                    className="justify-start text-center text-white text-[40px] font-light uppercase leading-[48px]">
                    Get the Answers You Need
                </div>
                <div
                    className="mx-auto max-w-[700px] text-center text-xl leading-8 font-medium text-stone-400 md:max-w-[860px]">
                    From your first question to your last concern, we’ve answered it all—transparent policies, fast
                    support, and a process built for trader success.
                </div>
            </div>

            <div className="space-y-12 md:space-y-0 md:grid md:grid-cols-2 md:gap-12">
                <div className="space-y-12">
                    <div
                        className="justify-start text-white text-[40px] font-light uppercase leading-[48px]">
                        Faqs
                    </div>
                    <div
                        className="justify-start text-stone-400 text-xl font-medium leading-8">
                        Everything you need to know about features, membership, and troubleshooting.
                    </div>

                    <div>
                        <div className="lg:inline">
                            {categories.map((category, index,) => (
                                <div key={index} className={index > 1 ? "md:contents" : 'contents'}>
                                    <button
                                        onClick={() => setCategorySelected(category.name)}
                                        className={clsx('group px-3 py-2 rounded-[64px] inline-flex justify-start items-center gap-2 mr-2 mb-2', [category.name === categorySelected ? 'bg-teal-500 active' : 'bg-[#1e1e1e]'])}>
                                        <div
                                            className="justify-start text-teal-400 group-[.active]:text-[#1e1e1e] text-base font-medium font-['Roboto'] leading-normal">
                                            {category.name}
                                        </div>
                                    </button>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
                <div>
                    <Faqs faqs={category.faqs}/>

                    <Card className="space-y-4 mt-8 border-none border-transparent">
                        <div
                            className="justify-start text-white text-xl font-medium leading-8">Still
                            have questions?
                        </div>

                        <div
                            className="justify-start text-stone-400 text-base font-medium leading-8">Contact
                            our support team and we will make sure everything is clear and intuitive for you!
                        </div>

                        <div>
                            <div
                                className="w-full px-4 py-3 bg-[#1e1e1e]/70 rounded-xl outline outline-1 outline-offset-[-1px] outline-neutral-700 inline-flex justify-start items-center gap-2">
                                <div
                                    className="flex-1 justify-start text-stone-400 text-base font-medium font-['Roboto'] leading-normal">support@megatarder.io
                                </div>
                                <CopyButton className="text-[#ffd78a]" defaultIcon={<>
                                    <div className="flex items-center gap-2">
                                        <DefaultCopyIcon/>
                                        <div
                                            className="text-right justify-start text-[#ffd78a] text-sm font-medium  uppercase leading-tight">Copy
                                        </div>
                                    </div>
                                </>} value={email}/>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        </section>
    );
}

export default GetTheAnswersYouNeed;