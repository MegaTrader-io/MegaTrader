import Card from "@/components/Card";
import React from "react";

export default function AccountOverView() {
    return <>
        <div className="grid grid-cols-12 gap-4 w-full">
            <Card className="col-span-7 w-full p-6 text-white">
                EARN WITH MEGATRADER
            </Card>
            <Card className="col-span-5 w-full p-6 text-white">
                INVITE YOUR FRIENDS
            </Card>
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