import React, {useState} from 'react';
import clsx from "clsx";
import AccountStatus from "@/app/(backoffice)/account-overview/_components/AccountStatus";
import Image from "next/image";
import Dialog from "@/components/Dialog";
import {Account} from "@/commons/interfaces";

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

    const handleChange = (item: T) => {
        setSelected(item);
        if (onChange) onChange(item);
        setOpen(false)
    };

    return (
        <>
            <button
                onClick={() => setOpen(true)}
                className="rounded-xl p-3 w-full h-12 bg-stone-800 border border-neutral-700 text-white focus:ring-gray-700 disabled:bg-stone-600 disabled:text-stone-800">
                <div className="grid grid-cols-[8px_auto_24px] gap-2 items-center">
                    <AccountStatus status={selected.status} circleOnly={true}/>
                    <div
                        className="text-left text-stone-400 text-base font-normal truncate">{selected.name}</div>
                    <Image src="/assets/images/arrow-down.svg" alt='selection' width={24} height={24}/>
                </div>
            </button>

            <Dialog
                className="w-[calc(100vw-32px)] sm:w-[600px]"
                showModal={open}
                onClose={() => setOpen(false)}
                title={'SELECT ACCOUNT'}>
                <div className="flex items-center h-full sm:h-auto">
                    <div className="w-full space-y-8">
                        <div>
                            <ul className="grid grid-cols-2">
                                {items.map((item) => {
                                    return <li
                                        onClick={() => {
                                            handleChange(item)
                                        }}
                                        className={clsx('cursor-pointer flex  pl-4 pr-3 py-3 gap-2 items-center', {'bg-stone-800 rounded-xl border border-neutral-700': selected.id === item.id})}
                                        key={item.id}>

                                        <AccountStatus size={'sm'} status={item.status}/>
                                        <div
                                            className=" text-stone-400 text-base font-normal truncate">{item.name}</div>
                                    </li>
                                })}
                            </ul>
                        </div>
                    </div>
                </div>
            </Dialog>
        </>
    );
}