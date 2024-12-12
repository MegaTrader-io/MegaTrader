import {PlanInterface} from "@/commons/interfaces";
import React from "react";
import Card from "@/components/card";
import Link from "@/components/link";

export default function SubscriptionCard({plan}: { plan: PlanInterface }) {
    const isPremium = plan.level === 'PREMIUM';
    const bgColorYellow = '!bg-[#ffb34a]';
    const bgColorBlackLight = 'bg-[#1e1e1e]/70';

    return <Card
        key={plan.id}
        className={`group ${isPremium ? `isPremium ${bgColorYellow}` : bgColorBlackLight}  p-4 border border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex`}>
        <div className="text-white group-[.isPremium]:text-[#131210] text-xl font-medium">
            {plan.level}
        </div>
        <div className="grid grid-cols-2 text-white group text-[32px] font-light leading-10">
            <div className="text-white group-[.isPremium]:text-[#131210]">
                {plan.total_peer_year}
            </div>
            <div className="text-[#ffb34a] group-[.isPremium]:text-[#131210]">
                {plan.total_peer_month}
            </div>
        </div>
        <div className="mt-5 mb-2 w-full">
            <div
                className="text-stone-400 group-[.isPremium]:text-[#131210] text-base font-normal leading-normal">Maximum
                Loss
                Limit
            </div>
            <div
                className=" text-stone-400 group-[.isPremium]:text-[#131210] text-base font-bold leading-normal">{plan.max_loss_limit}</div>
        </div>
        <div className="my-2">
            <div
                className="text-stone-400 group-[.isPremium]:text-[#131210] text-base font-normal leading-normal">Maximum
                Position
                Size
            </div>
            <div
                className=" text-stone-400 group-[.isPremium]:text-[#131210] text-base font-bold leading-normal">{plan.max_position_size}</div>
        </div>
        <div className="my-2">
            <div
                className="text-stone-400 group-[.isPremium]:text-[#131210] text-base font-normal leading-normal">Profit
                Target
            </div>
            <div
                className=" text-stone-400 group-[.isPremium]:text-[#131210] text-base font-bold leading-normal">{plan.profit_target}</div>
        </div>

        <Link as={"button"}
              className="!bg-[#ffb34a] group-[.isPremium]:!bg-stone-800 text-slate-950 group-[.isPremium]:text-white">
            GET PLAN
        </Link>
    </Card>
}
