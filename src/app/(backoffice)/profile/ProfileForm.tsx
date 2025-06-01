'use client';

import React, {useState} from 'react';
import {Button} from "@/components/Button";
import clsx from "clsx";

export type OPTIONS = 'personal_information' | 'verification' | 'password' | '2fa';

interface TabOption {
    option: OPTIONS,
    title: string
}

const ITEMS: TabOption[] = [
    {option: 'personal_information', title: 'Personal information'},
    {option: 'verification', title: 'Verification'},
    {option: 'password', title: 'Password'},
    {option: '2fa', title: '2FA (Two-factor-authentication)'},
];

function ProfileForm() {
    const [tab, setTab] = useState<TabOption>(ITEMS[0]);

    function changeTab(tab: TabOption) {
        setTab(tab);
    }

    return (
        <div className="grid grid-cols-[300px_64px_1fr] my-1">
            <div className="h-full space-y-2">
                {ITEMS.map((item, index) => (
                    <Button
                        key={index}
                        onClick={() => changeTab(item)}
                        className={clsx('w-full px-4 tracking-tight', {'bg-[#1e1e1e]': tab.option === item.option})}
                        styleType={'text'}
                        variant={'light'}
                        size={'md'}>
                        {item.title}
                    </Button>
                ))}
            </div>
            <div className="flex justify-center">
                <div className="outline outline-[0.1px] outline-[#1e1e1e] w-0 h-full"></div>
            </div>
            <div className="text-white">
                {JSON.stringify(tab)}
            </div>
        </div>
    );
}

export default ProfileForm;