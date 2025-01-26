'use client';

import Card from "@/components/Card";
import {Button} from "@/components/Button";
import React, {useRef, useState} from "react";
import Link from "next/link";
import {PlusIcon} from "@heroicons/react/16/solid";
import Dropdown from "@/components/Dropdown";
import Image from "next/image";
import Tooltip from "@/app/(backoffice)/account-overview/_components/Tooltip";
import {CopyButton} from "@/components/CopyButton";
import Objectives from "@/app/(backoffice)/account-overview/_components/Objectives";
import {Account} from "@/commons/interfaces";
import AccountBalance from "@/app/(backoffice)/account-overview/_components/AccountBalance";
import EyeComponent from "@/components/EyeComponent";
import useToggleSecretsKeys from "@/hooks/useToggleSecretsKeys";
import {accounts, credentials} from "@/commons/data";
import ProPlanChart from "@/app/(backoffice)/account-overview/_components/ProPlanChart";
import AccountStatus from "@/app/(backoffice)/account-overview/_components/AccountStatus";
import FeatureContent from "@/app/(backoffice)/account-overview/_components/FeatureContent";
import DailyJournal from "@/app/(backoffice)/account-overview/_components/DailyJournal";
import {ArrowUpRightIcon} from "@heroicons/react/16/solid";
import PopoverMenu from "@/components/backoffice/PopoverMenu";
import {Bars3Icon, EllipsisHorizontalIcon} from "@heroicons/react/24/solid";

export default function AccountOverView() {
    const passwordMaskRef = useRef<HTMLDivElement>(null);
    const [selectedAccount, setSelectedAccount] = useState<Account>(accounts[0]);

    const {toggleMask, currentMask} = useToggleSecretsKeys([
        {element: passwordMaskRef.current, value: credentials.password},
    ]);

    return <>
        <div className="w-full">
            <Card className="w-full flex lg:grid lg:grid-cols-[auto_1fr_auto] items-center justify-between gap-4">
                <div className="w-[271px]">
                    <Dropdown
                        items={accounts}
                        value={selectedAccount}
                        onChange={setSelectedAccount}
                        renderButtonContent={(item) => (
                            <div className="grid grid-cols-[auto_auto_24px] gap-2 items-center">
                                <AccountStatus status={item.status}/>
                                <div className="text-stone-400 text-base font-normal truncate">{item.name}</div>
                                <Image src="/assets/images/arrow-down.svg" alt='selection' width={24} height={24}/>
                            </div>
                        )}
                        renderOptionContent={(item) => (
                            <>
                                <AccountStatus status={item.status}/>
                                <div className="text-stone-400 text-base font-normal truncate">{item.name}</div>
                            </>
                        )}
                    />
                </div>

                {/* desktop */}
                <div className="hidden lg:block w-full">
                    <Link href="#" className="text-base btn-link">
                        Manage Subscription
                    </Link>
                </div>
                <div className="hidden md:flex gap-2">
                    <Button variant="dark">
                        RESET
                    </Button>
                    <Button variant="dark"
                            icon={<PlusIcon/>}
                            iconPosition="left">
                        CREATE NEW
                    </Button>
                </div>

                <div className="text-white md:hidden">
                    <PopoverMenu className="block lg:hidden"
                                 icon={<EllipsisHorizontalIcon className="w-6 h-6 text-white"/>}>
                        <div className="gap1 flex flex-col">
                            <Link
                                href={'#'}
                                className={`text-stone-800 text-center text-xs font-bold uppercase leading-6 px-4 py-1 transition-all duration-200 hover:bg-neutral-300`}
                            >
                                RESET
                            </Link>
                            <Link
                                href={'#'}
                                className={`text-stone-800 text-center text-xs font-bold uppercase leading-6 px-4 py-1 transition-all duration-200 hover:bg-neutral-300`}
                            >
                                CREATE NEW
                            </Link>
                            <Link
                                href={'#'}
                                className={`text-stone-800 text-center text-xs font-bold uppercase leading-6 px-4 py-1 transition-all duration-200 hover:bg-neutral-300`}
                            >
                                MANAGE SUBSCRIPTION
                            </Link>
                        </div>
                    </PopoverMenu>
                </div>
            </Card>
            <Tooltip>
                <div
                    className="flex items-center justify-between p-3 relative bg-neutral-950 rounded-lg border border-solid border-[#1e1e1e]">
                    <div className="flex text-white items-center gap-4">
                        <Image src='/assets/images/tradovate-t-blue.svg' alt='tradovate blue' width={133}
                               height={40}/>

                        <Button variant={'dark'}
                                iconPosition='left'
                                size='sm'
                                icon={<ArrowUpRightIcon className="text-white"/>}>
                            OPEN PLATFORM
                        </Button>
                    </div>

                    <div className="inline-flex items-center">
                        <div
                            className="gap-2 pl-0 pr-4 py-2 border-r border-neutral-700 inline-flex items-center relative">
                            <div
                                className="text-white">
                                Login :
                            </div>

                            <div
                                className="text-stone-400 text-base font-light leading-normal">
                                {credentials.login}
                            </div>

                            <CopyButton value={credentials.login}/>
                        </div>

                        <div className="gap-2 pl-4 pr-0 py-2 inline-flex items-center text-white">
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
                            <EyeComponent type={currentMask} onChange={toggleMask}/>
                        </div>
                    </div>
                </div>
            </Tooltip>
        </div>

        <div className="grid grid-cols-[827px_auto] gap-4 w-full">
            <AccountBalance account={selectedAccount}/>
            <Objectives/>
        </div>

        <ProPlanChart/>
        <FeatureContent/>
        <DailyJournal/>
    </>
}