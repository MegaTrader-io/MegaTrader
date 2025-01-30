'use client';

import Card from "@/components/Card";
import React from "react";
import EarnWithMegatrader from "@/app/(backoffice)/affiliates/_components/EarnWithMegatrader";
import InviteYourFriends from "@/app/(backoffice)/affiliates/_components/InviteYourFriends";

export default function AccountOverView() {
    return <>
        <div className="grid grid-cols-12 gap-4 w-full">
            <EarnWithMegatrader/>
            <InviteYourFriends/>
        </div>
        <Card className="w-full p-4 text-white">
            SUMMARY
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