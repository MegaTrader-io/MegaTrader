'use client'

import React, {useEffect, useState} from "react";
import {usePathname} from "next/navigation";
import clsx from "clsx";
import Link from "@/components/Link";
import Card from "@/components/Card";
import Avatar from "@/app/(backoffice)/profile/_components/Avatar";
import Badge from "@/components/Badge";
import InputText from "@/components/InputText";
import {IOption, IUser} from "@/commons/interfaces";
import {defaultUser} from "@/commons/data";

const Options: IOption[] = [
    {url: '/profile/identity-verification', label: 'Identity verification'},
    {url: '/profile/personal-information', label: 'Personal information'},
    {url: '/profile/password', label: 'Password'},
    {url: '/profile/two-factor-authentication', label: 'Two factor-authentication'},
]

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
    const currentPath = usePathname()
    const [user, setUser] = useState<IUser>(defaultUser)

    useEffect(() => {
        setUser(user => ({...user, verified: currentPath !== '/profile/personal-information'}))
    }, [currentPath])

    return <>
        <>
            <div className="space-y-4 w-full">
                <div className="flex gap-2">
                    <Links options={Options}/>
                </div>
                <div className="grid grid-cols-[1fr_auto] w-full gap-16 items-start">
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
                                    <Badge shape={'pill'} variant={user.verified ? 'secondary' : 'error'}>
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
                                    <InputText readOnly={true} name={'first_name'} value={user.firstName}/>
                                </label>
                            </div>
                            <div>
                                <label className="text-stone-400 text-base font-bold leading-normal">
                                    Last name
                                    <InputText readOnly={true} name={'last_name'} value={user.lastName}/>
                                </label>
                            </div>
                            <div>
                                <label className="text-stone-400 text-base font-bold leading-normal">
                                    Email
                                    <InputText readOnly={true} name={'email'} value={user.email}/>
                                </label>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        </>
    </>
}

export default Layout;