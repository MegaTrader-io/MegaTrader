import React from "react";
import Image from "next/image";
import {Button} from "@/components/Button";
import GetStartedNow from "@/app/(backoffice)/payouts/_components/GetStartedNow";
import Actions from "@/app/(backoffice)/payouts/_components/Actions";
import Card from "@/components/Card";
import Badge from "@/components/Badge";
import clsx from "clsx";
import InputText from "@/components/InputText";
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from "@/components/Table";

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
                <Card className="!mt-8">
                    <div className="flex w-full justify-between items-center">
                        <div
                            className="h-6 text-white text-xl font-light uppercase leading-normal">
                            Quick actions
                        </div>
                        <Button variant={'dark'}>
                            FIND MORE
                        </Button>
                    </div>

                    <div className="grid grid-cols-[250px_auto] gap-2 my-4">
                        <div className="relative w-full">
                            <select
                                name="link"
                                className={clsx('align-middle w-full h-12 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none')}
                            >
                                <option value="">Filyter payouts</option>
                            </select>
                            <div className="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <mask id="mask0_5269_2288" style={{maskType: 'alpha'}}
                                          maskUnits="userSpaceOnUse" x="0" y="0"
                                          width="24" height="24">
                                        <rect width="24" height="24" fill="#D9D9D9"/>
                                    </mask>
                                    <g mask="url(#mask0_5269_2288)">
                                        <path d="M12 15L7 10H17L12 15Z" fill="white"/>
                                    </g>
                                </svg>
                            </div>
                        </div>

                        <div className="relative flex">
                            <InputText name={'search'}
                                       className="pr-12 placeholder:text-stone-400"
                                       placeholder="Search here"
                                       searchInput={true}/>
                        </div>
                    </div>

                    <div className="overflow-x-auto">
                        <Table>
                            <TableHead className="text-xs">
                                <TableRow className="text-white">
                                    <TableHeader>Date</TableHeader>
                                    <TableHeader>Status</TableHeader>
                                    <TableHeader>Charges</TableHeader>
                                    <TableHeader>Refunds</TableHeader>
                                    <TableHeader>Fees</TableHeader>
                                    <TableHeader className="text-right">Total</TableHeader>
                                </TableRow>
                            </TableHead>
                            <TableBody className="p-0">
                                <TableRow className="text-stone-400 text-xs font-normal leading-tight">
                                    <TableCell className="py-4">July 31, 2022</TableCell>
                                    <TableCell className="py-4">
                                        <Badge shape={'pill'} variant={'primary'}>PENDING</Badge>
                                    </TableCell>
                                    <TableCell className="py-4">$910,00</TableCell>
                                    <TableCell className="py-4">$00,00</TableCell>
                                    <TableCell className="py-4">
                                        <span className="text-rose-400">-$910,00</span>
                                    </TableCell>
                                    <TableCell className="py-4 text-right">$546,00</TableCell>
                                </TableRow>
                                <TableRow className="text-stone-400 text-xs font-normal leading-tight">
                                    <TableCell className="py-4">July 30, 2022</TableCell>
                                    <TableCell className="py-4">
                                        <Badge shape={'pill'}>PAID</Badge>
                                    </TableCell>
                                    <TableCell className="py-4">$910,00</TableCell>
                                    <TableCell className="py-4">
                                        <span className="text-rose-400">-$910,00</span>
                                    </TableCell>
                                    <TableCell className="py-4">
                                        $00,00
                                    </TableCell>
                                    <TableCell className="py-4 text-right">$546,00</TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </Card>
            </div>
            <GetStartedNow/>
        </div>
    </>
}