'use client';

import Card, {CardTitle} from "@/components/Card";
import React from "react";
import EarnWithMegatrader from "@/app/(backoffice)/affiliates/_components/EarnWithMegatrader";
import InviteYourFriends from "@/app/(backoffice)/affiliates/_components/InviteYourFriends";
import {Button} from "@/components/Button";
import EarningsOverTime from "@/app/(backoffice)/affiliates/_components/EarningsOverTime";

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
        <div className="grid grid-cols-1 md:grid-cols-4 xl:grid-cols-12 gap-4 w-full">
            <Card className="col-span-1 md:col-span-4 xl:col-span-4  w-full bg-primary p-4 space-y-2">
                <div className="flex justify-between">
                    <div className="text-[#131210] text-base font-bold leading-normal">
                        Total sold
                    </div>
                    <div className="text-[#131210] text-base font-bold leading-normal">$</div>
                </div>
                <div className="flex justify-between">
                    <div className="text-[#131210] text-[40px] font-lightuppercase leading-[48px]">
                        $5072,00
                    </div>
                    <Button variant={'dark'} className="text-base">
                        REQUEST PAYOUT
                    </Button>
                </div>
            </Card>
            <Card className="col-span-1 md:col-span-2 xl:col-span-4 w-full p-4 text-white">
                <div className="text-white text-base font-bold leading-normal">
                    Total profit
                </div>
                <div className="text-white text-[40px] font-light uppercase leading-[48px]">
                    $399,00
                </div>
                <div className="text-stone-400 text-base font-normal leading-normal">
                    +$120 from last month
                </div>
            </Card>
            <Card className="col-span-1 md:col-span-2 xl:col-span-4 w-full p-4 text-white">
                <div className="text-white text-base font-bold leading-normal">
                    Total sold
                </div>
                <div className="text-white text-[40px] font-light uppercase leading-[48px]">
                    7
                </div>
                <div className="text-stone-400 text-base font-normal leading-normal">
                    +2 from last month
                </div>
            </Card>
        </div>
        <EarningsOverTime/>
        <Card className="w-full p-4 text-white space-y-4">
            <CardTitle>
                Quick actions
            </CardTitle>

            <div className="flex gap-2">
                <Button iconPosition={'left'} icon={<>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_5397_318" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                              width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_5397_318)">
                            <path
                                d="M5 22C4.45 22 3.97917 21.8042 3.5875 21.4125C3.19583 21.0208 3 20.55 3 20V6C3 5.45 3.19583 4.97917 3.5875 4.5875C3.97917 4.19583 4.45 4 5 4H6V2H8V4H16V2H18V4H19C19.55 4 20.0208 4.19583 20.4125 4.5875C20.8042 4.97917 21 5.45 21 6V20C21 20.55 20.8042 21.0208 20.4125 21.4125C20.0208 21.8042 19.55 22 19 22H5ZM5 20H19V10H5V20Z"
                                fill="white"/>
                        </g>
                    </svg>
                </>} variant={'dark'}>
                    Schedule Payouts
                </Button>
                <Button iconPosition={'left'} icon={<>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_5397_375" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                              width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_5397_375)">
                            <path
                                d="M6 22C5.45 22 4.97917 21.8042 4.5875 21.4125C4.19583 21.0208 4 20.55 4 20V4C4 3.45 4.19583 2.97917 4.5875 2.5875C4.97917 2.19583 5.45 2 6 2H14L20 8V20C20 20.55 19.8042 21.0208 19.4125 21.4125C19.0208 21.8042 18.55 22 18 22H6ZM13 9H18L13 4V9Z"
                                fill="white"/>
                        </g>
                    </svg>
                </>} variant={'dark'}>
                    View Reports
                </Button>
                <Button iconPosition={'left'} icon={<>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_5397_348" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                              width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_5397_348)">
                            <path
                                d="M5.1 16.05C4.73333 15.4167 4.45833 14.7667 4.275 14.1C4.09167 13.4333 4 12.75 4 12.05C4 9.81667 4.775 7.91667 6.325 6.35C7.875 4.78333 9.76667 4 12 4H12.175L10.575 2.4L11.975 1L15.975 5L11.975 9L10.575 7.6L12.175 6H12C10.3333 6 8.91667 6.5875 7.75 7.7625C6.58333 8.9375 6 10.3667 6 12.05C6 12.4833 6.05 12.9083 6.15 13.325C6.25 13.7417 6.4 14.15 6.6 14.55L5.1 16.05ZM12.025 23L8.025 19L12.025 15L13.425 16.4L11.825 18H12C13.6667 18 15.0833 17.4125 16.25 16.2375C17.4167 15.0625 18 13.6333 18 11.95C18 11.5167 17.95 11.0917 17.85 10.675C17.75 10.2583 17.6 9.85 17.4 9.45L18.9 7.95C19.2667 8.58333 19.5417 9.23333 19.725 9.9C19.9083 10.5667 20 11.25 20 11.95C20 14.1833 19.225 16.0833 17.675 17.65C16.125 19.2167 14.2333 20 12 20H11.825L13.425 21.6L12.025 23Z"
                                fill="white"/>
                        </g>
                    </svg>
                </>} variant={'dark'}>
                    Refresh Data
                </Button>
            </div>
        </Card>
    </>
}