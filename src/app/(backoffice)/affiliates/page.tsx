'use client';

import Card, {CardTitle} from "@/components/Card";
import React from "react";
import EarnWithMegatrader from "@/app/(backoffice)/affiliates/_components/EarnWithMegatrader";
import InviteYourFriends from "@/app/(backoffice)/affiliates/_components/InviteYourFriends";

export default function AccountOverView() {
    return <>
        <div className="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-12 lg:gap-4 w-full">
            <EarnWithMegatrader/>
            <InviteYourFriends/>
        </div>
        <Card className="w-full p-4 text-white">
            <CardTitle className="mb-4">
                SUMMARY
            </CardTitle>

            <div className="space-y-4 md:space-y-0 md:grid md:grid-cols-3">
                <div>
                    <h2 className="text-white text-base font-bold leading-normal">
                        Total Earnings
                    </h2>
                    <p
                        className="text-white text-[40px] font-light uppercase leading-[48px]">$5,471.00
                    </p>
                    <p className="text-teal-400 text-base font-normal leading-normal">
                        +12% from last month
                    </p>
                </div>
                <div>
                    <h2 className="text-white text-base font-bold leading-normal">
                        Conversion Rate
                    </h2>
                    <p
                        className="text-white text-[40px] font-light uppercase leading-[48px]">3.6%
                    </p>
                    <p className="text-rose-400 text-base font-normal leading-normal">
                        -0.8% from last month
                    </p>
                </div>
                <div>
                    <h2 className="text-white text-base font-bold leading-normal">
                        Active Referrals
                    </h2>
                    <p
                        className="text-white text-[40px] font-light uppercase leading-[48px]">3.6%
                    </p>
                    <p className="text-teal-400 text-base font-normal leading-normal">
                        +23 from last month
                    </p>
                </div>
            </div>


        </Card>
        <div className="grid grid-cols-3 gap-4 w-full h-32">
            <Card className="w-full bg-primary p-4 text-[#131210]">
            </Card>
            <Card className="w-full p-4 text-white">
            </Card>
            <Card className="w-full p-4 text-white">
            </Card>
        </div>
    </>
}