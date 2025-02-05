import React from "react";
import Image from "next/image";
import {Button} from "@/components/Button";
import GetStartedNow from "@/app/(backoffice)/payouts/_components/GetStartedNow";
import Actions from "@/app/(backoffice)/payouts/_components/Actions";
import Card from "@/components/Card";
import Badge from "@/components/Badge";

export default function AccountOverView() {
    return <>
        <div className="flex w-full justify-between items-center">
            <Image
                src={'/assets/images/payouts.svg'}
                alt={'payouts'}
                width={201}
                height={70}
            />

            <Button>REQUEST PAYOUT</Button>
        </div>
        <div className="grid grid-cols-[1fr_auto] w-full items-start gap-16">
            <div className="space-y-4">
                <Actions/>
                <Card className="grid grid-cols-[1fr_34px_1fr]">
                    <div className="w-full space-y-2">
                        <div>
                            <div className="w-full flex">
                                <div className="text-white text-base font-bold leading-normal flex-1">
                                    Previous payout
                                </div>
                                <div className="text-white text-base font-bold leading-normal">
                                    July 30, 2022
                                </div>
                            </div>
                            <div className="w-full flex items-center">
                                <div
                                    className="text-white text-[40px] font-light uppercase leading-[48px]  flex-1">
                                    $7,962.34
                                </div>
                                <Badge shape={'pill'}>PAID</Badge>
                            </div>
                        </div>
                        <Button size={'sm'} variant={'dark'}>
                            VIRE TRANSACTION
                        </Button>
                    </div>
                    <Image src={'/assets/images/line.svg'} className="mx-4" alt={'line'} width={2} height={108}/>
                    <div className="w-full space-y-2">
                        <div>
                            <div className="w-full flex">
                                <div className="text-white text-base font-bold leading-normal flex-1">
                                    Previous payout
                                </div>
                                <div className="text-white text-base font-bold leading-normal">
                                    July 30, 2022
                                </div>
                            </div>
                            <div className="w-full flex items-center">
                                <div
                                    className="text-white text-[40px] font-light uppercase leading-[48px]  flex-1">
                                    2,468.29
                                </div>
                                <Badge shape={'pill'} variant={'primary'}>PENDING</Badge>
                            </div>
                        </div>
                        <Button size={'sm'} variant={'dark'}>
                            VIRE TRANSACTION
                        </Button>
                    </div>
                </Card>
                <Card>
                    <div className="w-full space-y-2">
                        <div>
                            <div className="w-full flex">
                                <div className="text-white text-base font-bold leading-normal flex-1">
                                    Previous payout
                                </div>
                                <div className="text-white text-base font-bold leading-normal">
                                    July 30, 2022
                                </div>
                            </div>
                            <div className="w-full flex items-center">
                                <div
                                    className="text-white text-[40px] font-light uppercase leading-[48px] flex-1">
                                    $399,00
                                </div>
                                <Badge shape={'pill'}>PAID</Badge>
                            </div>
                        </div>
                        <Button size={'sm'} variant={'dark'}>
                            VIRE TRANSACTION
                        </Button>
                    </div>
                </Card>
            </div>
            <GetStartedNow/>
        </div>
    </>
}