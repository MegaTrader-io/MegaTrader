import React from 'react';
import Image from "next/image";
import {Button} from "@/components/Button";
import Card from "@/components/Card";

function GetStartedNow() {
    return (
        <Card className="bg-primary w-full md:w-[519px] h-[750px] pt-8 !px-0 !pb-0 overflow-hidden relative">
            <div className="px-8">
                <h2 className="text-[#3d2900] text-xl font-bold leading-loose">
                    Start earning up to 90% profit share with your Megatrader funded account.
                </h2>
                <p className="text-[#3d2900] text-base font-normal leading-normal">No
                    minimum trading days on your evaluation. Unlocking Opportunities and Maximizing Potential in the
                    Dynamic World of Trading.
                </p>
            </div>
            <Image
                className="absolute bottom-0 w-full"
                src={'/assets/images/cloud-yellow.svg'}
                alt={'yellow-cloud'}
                width={519}
                height={674}
                style={{width: '100%', height: 'auto'}}
                quality={100}
            />

            <div className="absolute bottom-8 w-full px-8">
                <Button className="min-w-full">
                    GET STARTED NOW
                </Button>
            </div>

        </Card>
    );
}

export default GetStartedNow;