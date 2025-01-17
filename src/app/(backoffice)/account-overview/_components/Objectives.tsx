import React from 'react';
import Card from "@/components/Card";
import {CheckCircleIcon, XCircleIcon} from "@heroicons/react/16/solid";

function Objectives() {
    return (
        <Card className="space-y-4">
            <div className="text-white text-xl font-light uppercase leading-normal">Objectives</div>

            <div className="space-y-2">
                <div className="flex gap-2">
                    <div className="flex items-center">
                        <CheckCircleIcon className="w-6 h-6 text-primary"/>
                    </div>
                    <div>
                        <div className="gap-2 flex items-center">
                            <div className="text-white text-base font-light">Reach and maintain the $9,000+</div>
                            <div
                                className="text-mgt-link text-xs font-normal underline leading-tight">Profit target
                            </div>
                        </div>
                        <div aria-hidden="true" className="my-2">
                            <div className="overflow-hidden rounded-full bg-neutral-700">
                                <div style={{width: '37.5%'}} className="h-2 bg-primary"/>
                            </div>
                        </div>
                        <div className="text-base font-light text-right">
                            <span className="text-primary">$2,900</span>
                            <span className="text-stone-400 mx-1">/</span>
                            <span className="text-stone-400">$9,000</span>
                        </div>
                    </div>
                </div>
                <div className="flex gap-2">
                    <div className="flex items-center">
                        <CheckCircleIcon className="w-6 h-6 text-primary"/>
                    </div>
                    <div>
                        <div>
                            <div className="text-white text-base font-light leading-normal">Best day cannot be greater
                                than 50% of your
                                total profit.<span
                                    className="ml-2 text-mgt-link text-xs font-normal underline leading-tight">Consistency Target
                            </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div className="text-white text-xl font-light uppercase leading-normal">RULE</div>
            <div className="flex gap-2 ">
                <div className="flex items-center">
                    <XCircleIcon className="w-6 h-6 text-mgt-error"/>
                </div>
                <div>
                    <div className="flex flex-col">
                        <div className="text-white text-base font-light leading-normal">Do not let your account balance
                            hit or go below $145,500. <span className="ml-2 text-mgt-link text-xs font-normal underline leading-tight">Maximum Loss Limit</span>
                        </div>

                    </div>
                </div>
            </div>

        </Card>
    )
}

export default Objectives;