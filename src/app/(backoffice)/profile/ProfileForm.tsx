'use client';

import React, {useState} from 'react';
import {Button} from "@/components/Button";
import clsx from "clsx";
import PersonalInformation from "@/app/(backoffice)/profile/personal-information/personal_information";
import Select from "@/components/Select";
import Verification from "@/app/(backoffice)/profile/identity-verification/verification";

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

    function onChange(e: React.ChangeEvent<HTMLSelectElement>) {
        const value = e.target.value;
        const tabSelected = ITEMS.find(tab => tab.option === value);
        if (!tabSelected) {
            return;
        }

        changeTab(tabSelected);
    }

    return (
        <div className="lg:grid lg:grid-cols-[300px_64px_1fr] my-1">
            <div className="h-full">
                <div className="space-y-2 hidden sm:block">
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

                <div className="block sm:hidden">
                    <Select value={tab.option} onChange={onChange}>
                        {ITEMS.map((item, index) => (
                            <option key={index} value={item.option}>{item.title}</option>
                        ))}
                    </Select>
                </div>
            </div>
            <div className="flex justify-center">
                <div className="outline outline-1 outline-[#404040] my-8 w-full h-full lg:w-0 lg:my-0 "></div>
            </div>
            <div className="text-white">
                {tab.option === 'personal_information' && <PersonalInformation/>}
                {tab.option === 'verification' && <Verification/>}
            </div>
        </div>
    );
}

export default ProfileForm;