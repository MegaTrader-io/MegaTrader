import {PlanInterface} from "@/commons/interfaces";
import React, {useState} from "react";
import Card from "@/components/card";
import Link from "@/components/link"
import {motion} from 'framer-motion'
import Image from "next/image";


export default function SubscriptionCard({plan}: { plan: PlanInterface }) {
    const setIsHovered = useState(false)[1];

    return (
        <motion.div
            whileHover={{scale: 1.05}}
            onHoverStart={() => setIsHovered(true)}
            onHoverEnd={() => setIsHovered(false)}
        >
            <Card
                key={plan.id}
                className={`group bg-[#1e1e1e]/70  p-5 border w-full border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex`}>
                <div className="relative z-10 w-full">
                    <h3 className="text-white text-xl font-medium mb-4 uppercase leading-6">{plan.level}</h3>
                    <div className="mb-4">
                        <span className={`text-white text-5xl font-light leading-[60px]`}>{plan.total_peer_year}</span>
                    </div>
                    <div>
                        <span
                            className={`text-white text-[32px] font-light leading-10`}>{plan.total_peer_month.split('/')[0]}</span>
                        <span className="text-stone-400 text-xl font-light">/MO</span>
                    </div>

                    <ul className="space-y-2 my-5 text-gray-300">
                        <li className="flex items-center gap-2">
                            <Image
                                src="/assets/images/check.svg"
                                alt="Check"
                                width={24}
                                height={24}
                            />
                            <span className={`${plan.colorItem}`}>Maximum Loss Limit: {plan.max_loss_limit}</span>
                        </li>
                        <li className="flex items-center gap-2">
                            <Image
                                src="/assets/images/check.svg"
                                alt="Check"
                                width={24}
                                height={24}
                            />
                            <span className={`${plan.colorItem}`}>Maximum Position Size: {plan.max_position_size}</span>
                        </li>
                        <li className="flex items-center gap-2">
                            <Image
                                src="/assets/images/check.svg"
                                alt="Check"
                                width={24}
                                height={24}
                            />
                            <span className={`${plan.colorItem}`}>Profit Target: {plan.profit_target}</span>
                        </li>
                    </ul>

                    <Link as={"button"}
                          className={`w-full !bg-[#ffb34a] !text-black transition-colors duration-300 !leading-6`}>
                        GET PLAN
                    </Link>
                </div>
            </Card>
        </motion.div>
    )
}
