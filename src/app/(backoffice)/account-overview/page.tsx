'use client';

import Card from "@/components/Card";
import {Button} from "@/components/Button";
import React, {useRef} from "react";
import Link from "next/link";
import {PlusIcon} from "@heroicons/react/16/solid";
import DropdownDialog from "@/components/DropdownDialog";
import Image from "next/image";
import {CopyButton} from "@/components/CopyButton";
import TooltipPanel from "@/app/(backoffice)/account-overview/_components/TooltipPanel";
import EyeComponent from "@/components/EyeComponent";
import useToggleSecretsKeys from "@/hooks/useToggleSecretsKeys";
import {accounts, credentials} from "@/commons/data";
import ProPlanChart from "@/app/(backoffice)/account-overview/_components/ProPlanChart";
import AccountStatus from "@/app/(backoffice)/account-overview/_components/AccountStatus";
import FeatureContent from "@/app/(backoffice)/account-overview/_components/FeatureContent";
import DailyJournal from "@/app/(backoffice)/account-overview/_components/DailyJournal";
import {ArrowUpRightIcon} from "@heroicons/react/16/solid";
import PopoverMenu from "@/components/backoffice/PopoverMenu";
import {EllipsisHorizontalIcon} from "@heroicons/react/24/solid";
import {useAccount} from "@/app/providers/AccountContext";
import {SkeletonTemplate} from "@/components/Skeleton";
import AccountPlanType from "@/app/(backoffice)/account-overview/_components/AccountPlanType";
import AccountSummary from "@/app/(backoffice)/account-overview/_components/AccountSummary";

export default function AccountOverView() {
    const {selectedAccount, setSelectedAccount, isLoadingAccount} = useAccount();
    const passwordMaskRef = useRef<HTMLDivElement>(null);

    const {toggleMask, currentMask} = useToggleSecretsKeys([
        {element: passwordMaskRef.current, value: credentials.password},
    ]);

    return <>
        <div className="w-full">
            <Card className="w-full flex md:grid md:grid-cols-[auto_1fr_auto] items-center justify-between gap-4">
                <div className="flex gap-4 items-center">
                    <AccountStatus status={selectedAccount.status}/>
                    <DropdownDialog
                        items={accounts}
                        value={selectedAccount}
                        onChange={setSelectedAccount}
                        renderButtonContent={(item) => (
                            <div className="grid grid-cols-[auto_auto_24px] gap-2 items-center">
                                <AccountStatus status={item.status} circleOnly={true}/>
                                <div className="text-stone-400 text-base font-normal truncate">{item.name}</div>
                                <Image src="/assets/images/arrow-down.svg" alt='selection' width={24} height={24}/>
                            </div>
                        )}
                        renderOptionContent={(item) => (
                            <>
                                <AccountStatus status={item.status} circleOnly={true}/>
                                <div className="text-stone-400 text-base font-normal truncate">{item.name}</div>
                            </>
                        )}
                    />
                </div>

                {/* desktop */}
                <div className="hidden md:block w-full">
                    <div className='flex flex-col gap-1'>
                        <AccountPlanType accountType={selectedAccount.accountType}/>
                        <Link href="#" className="text-[#ffd78a] text-xs font-medium underline leading-tight">Manage
                            Subscription
                        </Link>
                    </div>
                </div>
                <div className="hidden lg:flex gap-2">
                    <Button variant="dark">
                        RESET
                    </Button>
                    <Button variant="dark"
                            icon={<PlusIcon/>}
                            iconPosition="left">
                        CREATE NEW
                    </Button>
                </div>

                <div className="text-white lg:hidden">
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
            <TooltipPanel>
                <div
                    className="flex flex-col gap-[17px] lg:flex-row lg:items-center lg:justify-between p-3 relative bg-neutral-950 rounded-lg border border-solid border-[#1e1e1e]">
                    <div
                        className="justify-center flex gap-[17px] flex-col sm:flex-row sm:items-center sm:w-auto sm:justify-between">

                        <Image
                            className="mx-auto sm:mx-0"
                            src='/assets/images/tradovate-t-blue.svg' alt='tradovate blue'
                            width={133}
                            height={40}/>

                        {isLoadingAccount && (<div className="min-w-[145px] h-[24px]">
                            <SkeletonTemplate/>
                        </div>)}

                        {!isLoadingAccount && (
                            <Button variant={'dark'}
                                    iconPosition='left'
                                    size='sm'
                                    className="w-full sm:w-auto"
                                    icon={<ArrowUpRightIcon className="text-white"/>}>
                                LUNCH PLATFORM
                            </Button>
                        )}

                    </div>

                    <div className="sm:text-right lg:inline-flex lg:items-center">
                        <div
                            className="gap-2 pl-0 pr-4 py-2 sm:border-r border-neutral-700 inline-flex items-center relative">
                            <div
                                className="text-white">
                                Login :
                            </div>

                            {isLoadingAccount && (<div className="min-w-[120px] h-[24px]">
                                <SkeletonTemplate/>
                            </div>)}

                            {!isLoadingAccount && (
                                <>
                                    <div
                                        className="text-stone-400 text-base font-light leading-normal">
                                        {credentials.login}
                                    </div>

                                    <CopyButton value={credentials.login}/>
                                </>
                            )}
                        </div>

                        <div className="gap-2 sm:pl-4 pr-0 py-2 inline-flex items-center text-white">
                            <div
                                className="text-white">
                                Password :
                            </div>

                            {isLoadingAccount && (<div className="min-w-[120px] h-[24px]">
                                <SkeletonTemplate/>
                            </div>)}

                            {!isLoadingAccount && (
                                <>
                                    <div
                                        ref={passwordMaskRef}
                                        className="text-stone-400 text-base font-light leading-normal">
                                        ••••••••••••
                                    </div>

                                    <CopyButton value={credentials.password}/>
                                    <EyeComponent type={currentMask} onChange={toggleMask}/>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </TooltipPanel>
        </div>

        <div
            className="w-full">
            <AccountSummary account={selectedAccount} isLoadingAccount={isLoadingAccount}/>
        </div>

        <ProPlanChart isLoadingAccount={isLoadingAccount}/>
        <FeatureContent isLoadingAccount={isLoadingAccount}/>
        <DailyJournal isLoadingAccount={isLoadingAccount}/>
    </>
}