import React, {useEffect, useState} from 'react';
import clsx from "clsx";
import AccountStatus from "@/app/(backoffice)/account-overview/_components/AccountStatus";
import Image from "next/image";
import Dialog from "@/components/Dialog";
import {Account} from "@/commons/interfaces";
import TradingPlanIcon from "@/components/TradingPlanIcon";
import {CheckCircleIcon} from "@heroicons/react/16/solid";
import {Button} from "@/components/Button";

interface DropdownDialogProps<T> {
    items: T[];
    value: T;
    onChange: (item: T) => void;
    disabled?: boolean;
}

export default function DropdownDialog<T extends Account>({
                                                              items = [],
                                                              value,
                                                              onChange
                                                          }: DropdownDialogProps<T>) {
    const [open, setOpen] = useState(false);
    const [selected, setSelected] = useState(value);

    useEffect(() => {
        if (open) {
            setSelected(value);
        }
    }, [open, value]);

    const switchOption = (item: T) => {
        setSelected(item);
    };

    const selectAccount = () => {
        if (onChange) onChange(selected);
        setOpen(false)
    }

    const closeModal = () => {
        setOpen(false)
    }

    return (
        <>
            <button
                onClick={() => setOpen(true)}
                className="rounded-xl p-3 w-full h-12 bg-stone-800 border border-neutral-700 text-white focus:ring-gray-700 disabled:bg-stone-600 disabled:text-stone-800">
                <div className="flex gap-2">
                    <AccountStatus status={selected.status} size={'sm'}/>
                    <div
                        className="text-left text-neutral-50 text-base font-normal truncate grow w-0 sm:w-full">{selected.name}</div>
                    <Image src="/assets/images/arrow-down.svg" alt='selection' width={24} height={24}/>
                </div>
            </button>

            <Dialog
                className="w-[calc(100vw-32px)] sm:w-[428px]"
                showModal={open}
                onClose={closeModal}
                childrenClassName={''}
                title={'SELECT ACCOUNT'}>
                <div className="flex items-center h-full sm:h-auto">
                    <div className="w-full space-y-8">
                        <div>
                            <div className="grid grid-cols-2 gap-2">
                                {items.map((item) => {
                                    return <div
                                        onClick={() => {
                                            switchOption(item)
                                        }}
                                        className={clsx('cursor-pointer flex pl-4 pr-3 py-3 gap-2 items-center border-transparent rounded-xl',
                                            {
                                                'bg-stone-800': selected.id === item.id
                                            })}
                                        key={item.id}>
                                        <div className="text-center space-y-2 relative select-none w-full">
                                            <div className="w-full justify-center flex relative">
                                                <TradingPlanIcon
                                                    tradingType={item.tradingType}
                                                    status={item.status}/>

                                                {selected.id === item.id &&
                                                    <CheckCircleIcon
                                                        className="w-6 h-6 fill-primary absolute right-0 -top-1"/>}
                                            </div>
                                            <div>
                                                <div
                                                    className="text-center text-white text-base font-medium leading-normal">
                                                    {item.planDetail.level} <span
                                                    className="capitalize">{item.planDetail.planType}</span> Plan
                                                </div>
                                                <div
                                                    className="text-stone-400 text-sm font-medium uppercase leading-tight truncate">
                                                    {item.name}</div>
                                            </div>
                                        </div>
                                    </div>
                                })}
                            </div>
                        </div>
                        <div className="flex gap-2 justify-between sm:justify-end">
                            <Button className="w-full sm:w-auto" variant='light' onClick={closeModal} styleType='text'>
                                CANCEL
                            </Button>
                            <Button className="w-full sm:w-auto" variant={'dark'} onClick={selectAccount}>
                                SELECT
                            </Button>
                        </div>
                    </div>
                </div>
            </Dialog>
        </>
    );
}