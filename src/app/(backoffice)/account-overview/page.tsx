'use client';

import Card from "@/components/Card";
import {Button} from "@/components/Button";
import React, {useRef, useState} from "react";
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
import {Account} from "@/commons/interfaces";
import Dialog from "@/components/Dialog";

export default function AccountOverView() {
    const {selectedAccount, setSelectedAccount, isLoadingAccount} = useAccount();
    const passwordMaskRef = useRef<HTMLDivElement>(null);
    const [showModalBreach, setShowModalBreach] = useState(false)

    const {toggleMask, currentMask} = useToggleSecretsKeys([
        {element: passwordMaskRef.current, value: credentials.password},
    ]);

    function changeAccount(account: Account) {
        setSelectedAccount(account);
        setShowModalBreach(true)
    }

    function handleCloseDialog() {
        setShowModalBreach(false)
    }

    return <>
        <Dialog
            className="w-[600px]"
            showModal={showModalBreach}
            onClose={handleCloseDialog}
            title={'BREACH ALERT'}>
            <div className="w-full space-y-8">
                <div className="space-y-2">
                    <div className="w-full flex justify-center">
                        <svg width="106" height="94" viewBox="0 0 106 94" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M45.206 4.99999C48.6701 -1.00001 57.3304 -0.999995 60.7945 5.00001L104.096 80C107.56 86 103.23 93.5 96.3015 93.5H9.69896C2.77076 93.5 -1.55935 86 1.90475 80L45.206 4.99999Z"
                                fill="#F43F5E"/>
                            <path
                                d="M56.2559 37.375L55.6934 61.4453H50.8652L50.2793 37.375H56.2559ZM50.1152 68.8281C50.1152 67.9688 50.3965 67.25 50.959 66.6719C51.5371 66.0781 52.334 65.7812 53.3496 65.7812C54.3496 65.7812 55.1387 66.0781 55.7168 66.6719C56.2949 67.25 56.584 67.9688 56.584 68.8281C56.584 69.6562 56.2949 70.3672 55.7168 70.9609C55.1387 71.5391 54.3496 71.8281 53.3496 71.8281C52.334 71.8281 51.5371 71.5391 50.959 70.9609C50.3965 70.3672 50.1152 69.6562 50.1152 68.8281Z"
                                fill="white"/>
                        </svg>
                    </div>

                    <div
                        className="text-center text-white text-5xl font-medium uppercase leading-[60px]">Ups!
                    </div>
                    <div
                        className="text-center text-white text-2xl font-medium  uppercase leading-7">Your
                        evaluation has failed!
                    </div>
                    <div
                        className="text-center text-stone-400 text-base font-medium  leading-normal">In
                        order to continue trading you need to reset your account.
                    </div>
                </div>

                <div className="flex justify-center">
                    <Button variant={'primary'} onClick={() => {setShowModalBreach(false)}}>
                        RESET ACCOUNT
                    </Button>
                </div>
            </div>
        </Dialog>


        <div className="w-full">
            <Card className="w-full flex md:grid md:grid-cols-[auto_1fr_auto] items-center justify-between gap-4">
                <div className="flex gap-4 items-center">
                    <DropdownDialog
                        items={accounts}
                        value={selectedAccount}
                        onChange={changeAccount}
                        renderButtonContent={(item) => (
                            <button
                                className="rounded-xl p-3 w-full h-12 bg-stone-800 border border-neutral-700 text-white focus:ring-gray-700 disabled:bg-stone-600 disabled:text-stone-800">
                                <div className="grid grid-cols-[auto_auto_24px] gap-2 items-center">
                                    <AccountStatus status={item.status} circleOnly={true}/>
                                    <div className="text-stone-400 text-base font-normal truncate">{item.name}</div>
                                    <Image src="/assets/images/arrow-down.svg" alt='selection' width={24} height={24}/>
                                </div>
                            </button>
                        )}
                        renderOptionContent={(item) => (
                            <>
                                <AccountStatus size={'sm'} status={item.status}/>
                                <div className=" text-stone-400 text-base font-normal truncate">{item.name}</div>
                            </>
                        )}
                    />
                </div>

                {/* desktop */}
                <div className="hidden md:block w-full">
                    <div className='flex flex-col gap-1'>
                        <AccountPlanType accountType={selectedAccount.accountType}/>
                        <Link href="#" className="text-[#ffd78a] text-base font-medium underline leading-normal">Manage
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