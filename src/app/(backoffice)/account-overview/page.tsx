'use client';

import Card from "@/components/Card";
import {Button} from "@/components/Button";
import React, {useEffect, useRef, useState} from "react";
import Link from "next/link";
import {PlusIcon} from "@heroicons/react/16/solid";
import Dropdown from "@/components/Dropdown";
import Image from "next/image";
import Badge from "@/components/Badge";
import Tooltip from "@/app/(backoffice)/account-overview/_components/Tooltip";
import {CopyButton} from "@/components/CopyButton";
import Objectives from "@/app/(backoffice)/account-overview/_components/Objectives";
import {Account} from "@/commons/interfaces";
import AccountBalance from "@/app/(backoffice)/account-overview/_components/AccountBalance";
import EyeComponent from "@/components/EyeComponent";

const accounts: Account[] = [
    {
        id: 1,
        name: 'S1SEP2586479132',
        active: true,
        accountBalance: {
            currentBalance: "$145,166.78",
            currentEquity: "$145,166.78",
            high: "$150,000",
            low: "$145,166.78",
            weeklyNetPnL: "$0",
            bestDayPercentage: "-",
            bestDay: "-$73.40",
            worstDay: "-$4,524.54",
            avgWinningDay: "-",
            avgLosingDay: "-$1,610.74"
        }
    },
    {
        id: 2,
        name: 'S1SEP2586479133',
        active: false,
        accountBalance: {
            currentBalance: "$143,166.78",
            currentEquity: "$143,166.78",
            high: "$150,000",
            low: "$145,166.78",
            weeklyNetPnL: "$0",
            bestDayPercentage: "-",
            bestDay: "-$73.40",
            worstDay: "-$4,524.54",
            avgWinningDay: "-",
            avgLosingDay: "-$2,610.74"
        }
    },
];

const credentials = {
    login: 'pGd031d@hkh&Z~r1',
    password: 'pGd031d@hkh&Z~r1'
}

function toggleSecretValue(secretType: 'password' | 'text', element: HTMLDivElement, value: string) {
    element.innerText = secretType === 'text' ? value : '•'.repeat(value.length);
}

export default function AccountOverView() {
    const defaultMask = 'password';
    const loginMaskRef = useRef<HTMLDivElement>(null);
    const passwordMaskRef = useRef<HTMLDivElement>(null);
    const [selectedAccount, setSelectedAccount] = useState<Account>(accounts[0]);

    function handlerToggleSecretsKeys(type: 'password' | 'text') {
        [
            {element: loginMaskRef.current, value: credentials.login},
            {element: passwordMaskRef.current, value: credentials.password}
        ].forEach(item => {
            if (item.element) {
                toggleSecretValue(type, item.element, credentials.login)
            }
        })
    }

    useEffect(() => {
        handlerToggleSecretsKeys(defaultMask);
    }, [])

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

                    <div className="inline-flex items-center">
                        <div
                            className="gap-2 pl-0 pr-4 py-2 border-r border-neutral-700 inline-flex items-center relative">
                            <div
                                className="text-white">
                                Login :
                            </div>

                            <div
                                ref={loginMaskRef}
                                className="text-stone-400 text-base font-light leading-normal">
                                ••••••••••••
                            </div>

                            <CopyButton value={credentials.login}/>
                        </div>

                        <div className="gap-2 pl-4 pr-0 py-2 inline-flex items-center">
                            <div
                                className="text-white">
                                Password :
                            </div>

                            <div
                                ref={passwordMaskRef}
                                className="text-stone-400 text-base font-light leading-normal">
                                ••••••••••••
                            </div>

                            <CopyButton value={credentials.password}/>
                            <EyeComponent type={defaultMask} onChange={handlerToggleSecretsKeys}/>
                        </div>
                    </div>
                </div>
            </Tooltip>
        </div>

        <div className="grid grid-cols-[827px_auto] gap-4 w-full">
            <AccountBalance account={selectedAccount}/>
            <Objectives/>
        </div>
    </>
}