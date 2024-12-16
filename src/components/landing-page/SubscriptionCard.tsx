import {PlanInterface} from "@/commons/interfaces";
import React, {useState} from "react";
import Card from "@/components/card";
import {CheckIcon} from '@heroicons/react/24/solid'
import Link from "@/components/link"
import {motion} from 'framer-motion'


export default function SubscriptionCard({plan}: { plan: PlanInterface }) {
    const [isHovered, setIsHovered] = useState(false)

    const isPremium = plan.level === 'PREMIUM';
    const isBasic = plan.level === 'BASIC';

    return (
        <motion.div
            whileHover={{scale: 1.05}}
            onHoverStart={() => setIsHovered(true)}
            onHoverEnd={() => setIsHovered(false)}
        >
            <Card
                key={plan.id}
                className={`group ${isBasic ? `isBasic` : ''}  ${isPremium ? `isPremium` : ''} bg-[#1e1e1e]/70  p-4 border w-full border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex`}>
                <div className="relative z-10 w-full">
                    <h3 className="text-xl font-bold mb-4 text-white">{plan.level}</h3>
                    <div className="mb-6">
                        <span className={`text-5xl font-bold ${plan.color}`}>{plan.total_peer_year}</span>
                    </div>
                    <div className="mb-6">
                    <span
                        className={`text-3xl font-semibold ${plan.color}`}>{plan.total_peer_month.split('/')[0]}</span>
                        <span className="text-gray-400">/MO</span>
                    </div>

                    <ul className="space-y-4 mb-8 text-gray-300">
                        <li className="flex items-center gap-2">
                            <CheckIcon className={`w-5 h-5 ${plan.color}`}/>
                            <span>Maximum Loss Limit: {plan.max_loss_limit}</span>
                        </li>
                        <li className="flex items-center gap-2">
                            <CheckIcon className={`w-5 h-5 ${plan.color}`}/>
                            <span>Maximum Position Size: {plan.max_position_size}</span>
                        </li>
                        <li className="flex items-center gap-2">
                            <CheckIcon className={`w-5 h-5 ${plan.color}`}/>
                            <span>Profit Target: {plan.profit_target}</span>
                        </li>
                    </ul>

                    <Link as={"button"}
                          className={`w-full ${plan.buttonColor} transition-colors duration-300`}>
                        GET PLAN
                    </Link>
                </div>
            </Card>
        </motion.div>
    )
}
