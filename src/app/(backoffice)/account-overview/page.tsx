'use client';

import Card from "@/components/Card";
import {Button} from "@/components/Button";
import React, {useState} from "react";
import Link from "next/link";
import {PlusIcon} from "@heroicons/react/16/solid";
import Dropdown from "@/components/Dropdown";
import Image from "next/image";
import Badge from "@/components/Badge";
import Tooltip from "@/app/(backoffice)/account-overview/_components/Tooltip";
import {CopyButton} from "@/components/CopyButton";

interface Account {
    id: number
    name: string
    active: boolean
}

const accounts: Account[] = [
    {id: 1, name: 'S1SEP2586479132', active: true},
    {id: 2, name: 'S1SEP2586479133', active: false},
];

const credentials = {
    login: 'pGd031d@hkh&Z~r1',
    password: 'pGd031d@hkh&Z~r1'
}

export default function AccountOverView() {
    const [selectedAccount, setSelectedAccount] = useState(accounts[0]);

    return <>
        <div className="w-full">
            <Card className="w-full grid grid-cols-[auto_1fr_auto] items-center justify-between gap-4">
                <div className="w-[256px]">
                    <Dropdown
                        items={accounts}
                        value={selectedAccount}
                        onChange={setSelectedAccount}
                        renderButtonContent={(item) => (
                            <div className="flex gap-2 items-center">
                                <Badge shape="pill"
                                       variant={item.active ? 'secondary' : 'error'}>{item.active ? 'ACTIVE' : 'INACTIVE'}
                                </Badge>
                                <div className="text-stone-400 text-base font-normal truncate">{item.name}</div>
                                <Image src="/assets/images/arrow-down.svg" alt='selection' width={24} height={24}/>
                            </div>
                        )}
                        renderOptionContent={(item) => (
                            <>
                                <Badge shape="pill"
                                       variant={item.active ? 'secondary' : 'error'}>{item.active ? 'ACTIVE' : 'INACTIVE'}</Badge>
                                <div className="text-stone-400 text-base font-normal truncate">{item.name}</div>
                            </>
                        )}
                    />
                </div>

                <div className="w-full">
                    <Link href="#" className="text-base btn-link">
                        Manage Subscription
                    </Link>
                </div>
                <div className="flex gap-2">
                    <Button variant="dark">
                        RESET
                    </Button>
                    <Button variant="dark"
                            icon={<PlusIcon/>}
                            iconPosition="left">
                        CREATE NEW
                    </Button>
                </div>
            </Card>


            <Tooltip>
                <div
                    className="flex items-center justify-between p-3 relative bg-neutral-950 rounded-lg border border-solid border-[#1e1e1e]">
                    <Image src='/assets/images/tradovate-t-blue.svg' alt='tradovate blue' width={133}
                           height={40}></Image>

                    <div className="inline-flex items-center relative flex-[0_0_auto]">
                        <div
                            className="gap-2 pl-0 pr-4 py-2 border-r [border-right-style:solid] border-neutral-700 inline-flex items-center relative flex-[0_0_auto]">
                            <div
                                className="relative w-fit mt-[-1.00px] font-body-md-light font-[number:var(--body-md-light-font-weight)] text-white text-[length:var(--body-md-light-font-size)] tracking-[var(--body-md-light-letter-spacing)] leading-[var(--body-md-light-line-height)] whitespace-nowrap [font-style:var(--body-md-light-font-style)]">
                                Login :
                            </div>

                            <div
                                className="relative w-[154px] mt-[-1.00px] font-body-md-light font-[number:var(--body-md-light-font-weight)] text-stone-400 text-[length:var(--body-md-light-font-size)] text-center tracking-[var(--body-md-light-letter-spacing)] leading-[var(--body-md-light-line-height)] overflow-hidden text-ellipsis [display:-webkit-box] [-webkit-line-clamp:1] [-webkit-box-orient:vertical] [font-style:var(--body-md-light-font-style)]">
                                {credentials.login}
                            </div>

                            <CopyButton value={credentials.login}/>
                        </div>

                        <div className="gap-2 pl-4 pr-0 py-2 inline-flex items-center relative flex-[0_0_auto]">
                            <div
                                className="relative w-fit mt-[-1.00px] font-body-md-light font-[number:var(--body-md-light-font-weight)] text-white text-[length:var(--body-md-light-font-size)] tracking-[var(--body-md-light-letter-spacing)] leading-[var(--body-md-light-line-height)] whitespace-nowrap [font-style:var(--body-md-light-font-style)]">
                                Password :
                            </div>

                            <div
                                className="relative w-fit mt-[-1.00px] font-body-md-light font-[number:var(--body-md-light-font-weight)] text-stone-400 text-[length:var(--body-md-light-font-size)] tracking-[var(--body-md-light-letter-spacing)] leading-[var(--body-md-light-line-height)] whitespace-nowrap [font-style:var(--body-md-light-font-style)]">
                                {credentials.password}
                            </div>

                            <CopyButton value={credentials.password}/>
                        </div>
                    </div>
                </div>
            </Tooltip>
        </div>
    </>
}