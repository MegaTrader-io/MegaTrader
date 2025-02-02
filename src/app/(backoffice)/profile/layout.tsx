'use client'

import React, {useState} from "react";
import {usePathname} from "next/navigation";
import clsx from "clsx";
import Link from "@/components/Link";
import Card from "@/components/Card";
import Avatar from "@/app/(backoffice)/profile/_components/Avatar";
import Badge from "@/components/Badge";
import InputText from "@/components/InputText";

interface IOption {
    url: string,
    label: string
}

export interface IUser {
    fullName: string,
    firstName: string,
    lastName: string,
    email: string,
    verified: boolean,
    memberSince: string
}

const Options: IOption[] = [
    {url: '/profile/identity-verification', label: 'Identity verification'},
    {url: '/profile/personal-information', label: 'Personal information'},
    {url: '/profile/password', label: 'Password'},
    {url: '/profile/two-factor-authentication', label: 'Two factor-authentication'},
]

const defaultUser: IUser = {
    fullName: 'JOHN DOE',
    firstName: 'Jane',
    lastName: 'Doe',
    email: 'janedoe@gmail.com',
    verified: true,
    memberSince: '21-12-2023'
}

function Links({options}: { options: IOption[] }) {
    const currentPath = usePathname()

    return <>
        {options.map(option => (
            <Link
                key={option.url}
                className={clsx('btn-dark-link !normal-case', {'!bg-primary !text-black': currentPath === option.url})}
                href={option.url}>
                {option.label}
            </Link>
        ))}
    </>
}

const Layout = ({children,}: {
    children: React.ReactNode
}) => {
    const [user] = useState<IUser>(defaultUser)

    return <>
        <>
            <div className="flex w-full gap-2">
                <Links options={Options}/>
            </div>
            <div className="grid grid-cols-[1fr_auto] w-full gap-16">
                <div className="w-full">
                    {children}
                </div>

                <Card className="w-[519px] space-y-8">
                    <div className="flex justify-center flex-col items-center gap-4">
                        <Avatar user={user}/>
                        <div className="grid grid-rows-3 gap-0.5">
                            <div
                                className="text-center text-white text-xl font-light uppercase leading-normal">{user.fullName}
                            </div>
                            <div className="flex justify-center">
                                <Badge shape={'pill'}>
                                    {user.verified ? 'VERIFIED' : 'NOT VERIFIED'}
                                </Badge>
                            </div>
                            <div
                                className="text-center text-stone-400 text-base font-normal leading-normal">
                                Member since: {user.memberSince}
                            </div>
                        </div>
                    </div>

                    <div className="space-y-4">
                        <div>
                            <label className="text-stone-400 text-base font-bold leading-normal">
                                First name
                                <InputText name={'first_name'} value={user.firstName}/>
                            </label>
                        </div>
                        <div>
                            <label className="text-stone-400 text-base font-bold leading-normal">
                                Last name
                                <InputText name={'last_name'} value={user.lastName}/>
                            </label>
                        </div>
                        <div>
                            <label className="text-stone-400 text-base font-bold leading-normal">
                                Email
                                <InputText name={'email'} value={user.email}/>
                            </label>
                        </div>
                    </div>
                </Card>
            </div>
        </>
    </>
}

export default Layout;