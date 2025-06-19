'use client';

import Card from "@/components/Card";
import {Button} from "@/components/Button";
import React, {useEffect, useRef, useState} from "react";
import Link from "next/link";
import {ArrowUpRightIcon, PlusIcon} from "@heroicons/react/16/solid";
import SelectAccountDialog from "@/components/SelectAccountDialog";
import {CopyButton} from "@/components/CopyButton";
import TooltipPanel from "@/app/(backoffice)/account-overview/_components/TooltipPanel";
import EyeComponent from "@/components/EyeComponent";
import useToggleSecretsKeys from "@/hooks/useToggleSecretsKeys";
import {accounts, credentials} from "@/commons/data";
import ProPlanChart from "@/app/(backoffice)/account-overview/_components/ProPlanChart";
import FeatureContent from "@/app/(backoffice)/account-overview/_components/FeatureContent";
import DailyJournal from "@/app/(backoffice)/account-overview/_components/DailyJournal";
import PopoverMenu from "@/components/backoffice/PopoverMenu";
import {EllipsisHorizontalIcon} from "@heroicons/react/24/solid";
import {useAccount} from "@/app/providers/AccountContext";
import AccountSummary from "@/app/(backoffice)/account-overview/_components/AccountSummary";
import {Account} from "@/commons/interfaces";
import Dialog from "@/components/Dialog";
import TradingLogo from "@/components/TradingLogo";
import DriverGuide from "@/components/on-boarding/DriverGuide";

export default function AccountOverView() {
    const {selectedAccount, setSelectedAccount, fetchAccount} = useAccount();
    const [modalType, setModalType] = useState<'breach_modal' | 'unpaid_modal' | 'congratulations_modal' | 'delete_account' | null>(null);
    const passwordMaskRef = useRef<HTMLDivElement>(null);

    const {toggleMask, currentMask} = useToggleSecretsKeys([
        {element: passwordMaskRef.current, value: credentials.password},
    ]);

    async function changeAccount(account: Account): Promise<{ account: Account }> {
        try {
            await fetchAccount(account);
            setSelectedAccount(account);

            switch (account.id) {
                case 1:
                    setModalType('breach_modal');
                    break;
                case 3:
                    setModalType('congratulations_modal');
                    break;
                case 4:
                    setModalType('unpaid_modal');
                    break;
            }

            return {account};
        } catch (error) {
            console.error('error handler:', error);
            throw error;
        }
    }

    function handleCloseDialog() {
        setModalType(null)
    }

    return <>
        <DriverGuide currentPath="/account-overview"/>

        <Dialog
            className="w-[calc(100vw-32px)] sm:w-[600px]"
            showModal={modalType === 'breach_modal'}
            onClose={handleCloseDialog}
            title={'BREACH ALERT'}>
            <div className="flex items-center h-full sm:h-auto">
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
                            className="text-center text-white text-2xl font-medium  uppercase leading-7">Your evaluation
                            has
                            failed!
                        </div>
                        <div
                            className="text-center text-stone-400 text-base font-medium  leading-normal">In order to
                            continue trading you need to reset your account.
                        </div>
                    </div>

                    <div className="flex justify-center">
                        <Button className="w-full sm:w-auto" variant={'primary'} onClick={() => {
                            setModalType(null)
                        }}>
                            RESET ACCOUNT
                        </Button>
                    </div>
                </div>
            </div>
        </Dialog>

        <Dialog
            className="w-[calc(100vw-32px)] sm:w-[600px]"
            showModal={modalType === 'unpaid_modal'}
            onClose={handleCloseDialog}
            title={'UNPAID ALERT'}>
            <div className="flex items-center h-full sm:h-auto">
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
                            className="text-center text-white text-2xl font-medium  uppercase leading-7">Your account
                            has
                            not been paid
                        </div>
                        <div
                            className="text-center text-stone-400 text-base font-medium  leading-normal">In order to
                            continue trading, you need to make a payment.
                        </div>
                    </div>

                    <div className="flex flex-col gap-2 sm:grid sm:grid-cols-2">
                        <Button className="order-2 sm:order-1 w-full sm:w-auto" variant='light'
                                styleType='text'
                                onClick={() => {
                                    setModalType('delete_account')
                                }}>
                            DELETE ACCOUNT
                        </Button>
                        <Button className="order-1 sm:order-2 w-full sm:w-auto" variant={'primary'} onClick={() => {
                            setModalType(null)
                        }}>
                            PAY NOW
                        </Button>
                    </div>
                </div>
            </div>
        </Dialog>

        <Dialog
            className="w-[calc(100vw-32px)] sm:w-[600px]"
            showModal={modalType === 'delete_account'}
            onClose={handleCloseDialog}
            title={'DELETE ACCOUNT'}>
            <div className="flex items-center h-full sm:h-auto">
                <div className="w-full space-y-8">
                    <div className="space-y-4">
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
                            className="text-center text-white text-2xl font-medium  uppercase leading-7">
                            ARE YOU SURE YOU WANT TO DELETE YOUR ACCOUNT?
                        </div>
                    </div>

                    <div className="flex flex-col gap-2 sm:grid sm:grid-cols-2">
                        <Button className="order-2 sm:order-1 w-full sm:w-auto" variant='light'
                                styleType='text'
                                onClick={() => {
                                    setModalType(null)
                                }}>
                            YES
                        </Button>
                        <Button className="order-1 sm:order-2 w-full sm:w-auto" variant={'primary'} onClick={() => {
                            setModalType(null)
                        }}>
                            NO
                        </Button>
                    </div>
                </div>
            </div>
        </Dialog>

        <Dialog
            className="w-[calc(100vw-32px)] sm:w-[600px]"
            showModal={modalType === 'congratulations_modal'}
            onClose={handleCloseDialog}
            title={'FUNDED ALERT'}>
            <div className="flex items-center h-full sm:h-auto">
                <div className="w-full space-y-8">
                    <div className="space-y-2">
                        <div className="w-full flex justify-center">
                            <svg width="443" height="126" viewBox="0 0 443 126" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M55.9997 30.052C56.1283 29.1719 57.2559 28.8852 57.7894 29.597L59.6986 32.1447C59.9235 32.4448 60.2974 32.5938 60.667 32.5308L63.8054 31.9954C64.6822 31.8458 65.3033 32.8296 64.7912 33.5569L62.9582 36.16C62.7423 36.4666 62.716 36.8682 62.8902 37.2003L64.3692 40.0196C64.7824 40.8073 64.0387 41.702 63.1888 41.4397L60.1467 40.5008C59.7883 40.3902 59.3983 40.4894 59.1363 40.7577L56.912 43.0355C56.2905 43.6719 55.2098 43.2411 55.1966 42.3517L55.1495 39.1683C55.1439 38.7933 54.9291 38.453 54.593 38.2868L51.7393 36.8752C50.942 36.4808 51.0178 35.3198 51.8596 35.0325L54.8726 34.0039C55.2275 33.8828 55.4848 33.5733 55.539 33.2022L55.9997 30.052Z"
                                    fill="#FFC666"/>
                                <path
                                    d="M54.2784 95.4561C54.4071 94.576 55.5347 94.2893 56.0681 95.0011L56.9791 96.2168C57.204 96.5168 57.5779 96.6659 57.9475 96.6028L59.445 96.3474C60.3218 96.1978 60.9429 97.1816 60.4308 97.9089L59.5562 99.1509C59.3403 99.4575 59.3141 99.8592 59.4883 100.191L60.1939 101.536C60.6072 102.324 59.8634 103.219 59.0135 102.957L57.5619 102.509C57.2036 102.398 56.8136 102.497 56.5516 102.765L55.4902 103.852C54.8688 104.489 53.7881 104.058 53.7749 103.168L53.7524 101.649C53.7469 101.275 53.532 100.934 53.1959 100.768L51.8343 100.094C51.037 99.7 51.1128 98.5391 51.9546 98.2517L53.3922 97.7609C53.7471 97.6398 54.0044 97.3303 54.0586 96.9593L54.2784 95.4561Z"
                                    fill="#60A5FA"/>
                                <path
                                    d="M310.569 47.5982C310.856 46.6531 312.194 46.6531 312.482 47.5982L313.704 51.6114C313.801 51.9303 314.05 52.1799 314.369 52.2769L318.382 53.4984C319.327 53.7861 319.327 55.1241 318.382 55.4117L314.369 56.6332C314.05 56.7303 313.801 56.9799 313.704 57.2987L312.482 61.3119C312.194 62.257 310.856 62.257 310.569 61.3119L309.347 57.2987C309.25 56.9799 309.001 56.7303 308.682 56.6332L304.669 55.4117C303.723 55.1241 303.723 53.7861 304.669 53.4984L308.682 52.2769C309.001 52.1799 309.25 51.9303 309.347 51.6114L310.569 47.5982Z"
                                    fill="#A78BFA"/>
                                <path
                                    d="M414.573 24.1153C414.541 23.128 415.807 22.6963 416.384 23.4981L417.602 25.1898C417.797 25.4603 418.113 25.616 418.447 25.605L420.53 25.5362C421.517 25.5036 421.949 26.7701 421.147 27.3472L419.455 28.565C419.185 28.7598 419.029 29.0765 419.04 29.4096L419.109 31.4929C419.142 32.4803 417.875 32.9119 417.298 32.1102L416.08 30.4185C415.885 30.148 415.569 29.9922 415.236 30.0032L413.152 30.0721C412.165 30.1047 411.733 28.8382 412.535 28.261L414.227 27.0432C414.497 26.8485 414.653 26.5318 414.642 26.1986L414.573 24.1153Z"
                                    fill="#A78BFA"/>
                                <rect x="126.029" y="0.455078" width="10" height="10"
                                      transform="rotate(16.1609 126.029 0.455078)" fill="#60A5FA"/>
                                <rect x="127.936" y="111.455" width="10" height="10"
                                      transform="rotate(40.1181 127.936 111.455)" fill="#FB923C"/>
                                <rect x="335.102" y="11.7061" width="10" height="10"
                                      transform="rotate(34.0869 335.102 11.7061)" fill="#2DD4BF"/>
                                <rect x="422.67" y="95.4551" width="10" height="10"
                                      transform="rotate(39.4175 422.67 95.4551)" fill="#A78BFA"/>
                                <path d="M307.492 81.4551C318.992 82.6217 343.992 92.1551 351.992 120.955"
                                      stroke="#A78BFA"
                                      strokeWidth="4"/>
                                <path d="M407.525 63.8994C411.662 58.198 424.34 47.9463 441.962 52.5506"
                                      stroke="#FB923C"
                                      strokeWidth="4"/>
                                <path d="M12.5938 46.7812C18.202 51.0429 28.1698 63.9453 23.1754 81.4616"
                                      stroke="#FB7185"
                                      strokeWidth="4"/>
                                <path d="M129.305 58.5342C126.268 62.1436 117.308 68.3818 105.758 64.4597"
                                      stroke="#2DD4BF"
                                      strokeWidth="4"/>
                                <path
                                    d="M217.117 18.1449C221.116 15.8587 226.025 15.8587 230.023 18.1449L262.057 36.4615C266.105 38.7765 268.604 43.083 268.604 47.7468V84.1633C268.604 88.8271 266.105 93.1336 262.057 95.4487L230.023 113.765C226.025 116.051 221.116 116.051 217.117 113.765L185.084 95.4487C181.035 93.1336 178.537 88.8272 178.537 84.1633V47.7468C178.537 43.083 181.035 38.7765 185.084 36.4615L217.117 18.1449Z"
                                    fill="#14B8A6"/>
                                <mask id="mask0_5950_20310" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse"
                                      x="202"
                                      y="44" width="44" height="44">
                                    <rect x="202.053" y="44.6445" width="43.0345" height="42.6207" fill="#D9D9D9"/>
                                </mask>
                                <g mask="url(#mask0_5950_20310)">
                                    <path
                                        d="M219.178 76.6096L208.957 66.4872L211.512 63.9566L219.178 71.5484L235.629 55.2549L238.185 57.7855L219.178 76.6096Z"
                                        fill="black"/>
                                </g>
                            </svg>

                        </div>

                        <div
                            className="text-center text-white text-5xl font-medium uppercase leading-[60px]">CONGRATS!
                        </div>
                        <div
                            className="text-center text-white text-2xl font-medium  uppercase leading-7">Your account
                            has
                            passed the evaluation
                        </div>
                        <div
                            className="text-center text-stone-400 text-base font-medium  leading-normal">In order to
                            continue trading, you need to activate it.
                        </div>
                    </div>
                    <div className="flex justify-center">
                        <Button className="w-full sm:w-auto" variant={'primary'} onClick={() => {
                            setModalType(null)
                        }}>
                            ACTIVATE ACCOUNT
                        </Button>
                    </div>
                </div>
            </div>
        </Dialog>

        <div className="w-full">
            <Card id="manage-subscription"
                  className="w-full flex md:grid md:grid-cols-[auto_1fr_auto] items-center justify-between gap-4">
                <div className="flex gap-4 items-center w-full">
                    <SelectAccountDialog
                        items={accounts}
                        value={selectedAccount}
                        onChange={changeAccount}
                    />
                </div>

                {/* desktop */}
                <div className="hidden md:block w-full">
                    <div className='flex flex-col gap-1'>
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
                    <PopoverMenu collisionPadding={33} className="block lg:hidden"
                                 icon={<EllipsisHorizontalIcon className="w-6 h-6 text-white"/>}>
                        <div className="gap1 flex flex-col">
                            <Link
                                href={'#'}
                                data-dismiss="true"
                                className={`text-stone-800 text-center text-xs font-bold uppercase leading-6 px-4 py-1 transition-all duration-200 hover:bg-neutral-300`}
                            >
                                RESET
                            </Link>
                            <Link
                                href={'#'}
                                data-dismiss="true"
                                className={`text-stone-800 text-center text-xs font-bold uppercase leading-6 px-4 py-1 transition-all duration-200 hover:bg-neutral-300`}
                            >
                                CREATE NEW
                            </Link>
                            <Link
                                href={'#'}
                                data-dismiss="true"
                                className={`text-stone-800 text-center text-xs font-bold uppercase leading-6 px-4 py-1 transition-all duration-200 hover:bg-neutral-300`}
                            >
                                MANAGE SUBSCRIPTION
                            </Link>
                        </div>
                    </PopoverMenu>
                </div>
            </Card>
            <TooltipPanel id="platform-access">
                <div
                    className="flex flex-col gap-[17px] lg:flex-row lg:items-center lg:justify-between p-3 relative bg-neutral-950 rounded-lg border border-solid border-[#1e1e1e]">
                    <div
                        className="justify-center flex gap-[17px] flex-col sm:flex-row sm:items-center sm:w-auto sm:justify-between">

                        <TradingLogo className="mx-auto sm:mx-0" tradingType={selectedAccount.tradingType}/>

                        <Button variant={'dark'}
                                iconPosition='left'
                                size='sm'
                                className="w-full sm:w-auto"
                                icon={<ArrowUpRightIcon className="text-white"/>}>
                            LUNCH PLATFORM
                        </Button>
                    </div>

                    <div className="text-left xs:text-right lg:inline-flex lg:items-center">
                        <div
                            className="gap-2 pl-0 pr-4 py-2 inline-flex items-center relative">
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

                        <div className="gap-2 sm:pl-4 pr-0 py-2 inline-flex items-center text-white">
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
            </TooltipPanel>
        </div>

        <div
            className="w-full">
            <AccountSummary account={selectedAccount}/>
        </div>

        <ProPlanChart account={selectedAccount}/>
        <FeatureContent/>
        <DailyJournal/>
    </>
}