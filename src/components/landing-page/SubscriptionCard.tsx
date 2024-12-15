import {PlanInterface} from "@/commons/interfaces";
import React from "react";
import Card from "@/components/card";
import Link from "@/components/link";

export default function SubscriptionCard({plan}: { plan: PlanInterface }) {
    const isPremium = plan.level === 'PREMIUM';
    const isBasic = plan.level === 'BASIC';
    // const bgColorYellow = '!bg-[#ffb34a]';
    // const bgColorBlackLight = 'bg-[#1e1e1e]/70';

    return <Card
        key={plan.id}
        className={`group ${isBasic ? `isBasic` : ''}  ${isPremium ? `isPremium` : ''}  bg-[#1e1e1e]/70  p-4 border border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex`}>
        <div className="text-white  text-xl font-medium">
            {plan.level}
        </div>
        <div className="flex justify-between w-full text-white group text-[32px] font-light leading-10">
            <div className="text-white text-[40px] font-light">
                {plan.total_peer_year}
            </div>
            <div
                className="w-full align-bottom h-full flex justify-end items-end text-2xl font-medium text-right text-[#ffb34a]  group-[.isBasic]:text-teal-400">
                {plan.total_peer_month}
            </div>
        </div>
        <div className="mt-5 mb-2 w-full">
            <div
                className="text-stone-400  text-base font-normal leading-normal">Maximum
                Loss Limit
            </div>
            <div
                className=" text-stone-400  text-base font-bold leading-normal">{plan.max_loss_limit}</div>
        </div>
        <div className="my-2">
            <div
                className="text-stone-400  text-base font-normal leading-normal">Maximum
                Position
                Size
            </div>
            <div
                className=" text-stone-400  text-base font-bold leading-normal">{plan.max_position_size}</div>
        </div>
        <div className="my-2">
            <div
                className="text-stone-400  text-base font-normal leading-normal">Profit
                Target
            </div>
            <div
                className=" text-stone-400  text-base font-bold leading-normal">{plan.profit_target}</div>
        </div>

        <Link as={"button"}
              className="bg-[#ffb34a] text-slate-950  group-[.isPremium]:bg-[#292524] group-[.isPremium]:text-white">
            GET PLAN
        </Link>
    </Card>
}
