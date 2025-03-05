'use client'

import React, {useState} from "react";
import {usePathname, useRouter} from "next/navigation";
import clsx from "clsx";
import Link from "@/components/Link";
import Card from "@/components/Card";
import Avatar from "@/app/(backoffice)/profile/_components/Avatar";
import InputText from "@/components/InputText";
import {IOption, IUser} from "@/commons/interfaces";
import {defaultUser} from "@/commons/data";
import {CheckCircleIcon} from "@heroicons/react/16/solid";
import Pencil from "@/components/Pencil";
import {XCircleIcon} from "@heroicons/react/20/solid";

const Options: IOption[] = [
    {url: '/profile/identity-verification', label: 'Identity verification'},
    {url: '/profile/personal-information', label: 'Personal information'},
    {url: '/profile/password', label: 'Password'},
    {url: '/profile/two-factor-authentication', label: 'Two factor-authentication'},
]

function Links({options}: { options: IOption[] }) {
    const currentPath = usePathname()
    const router = useRouter()

    return <>
        <div className="block sm:hidden">
            <div className="relative w-full">
                <select
                    name="link"
                    value={currentPath}
                    className={clsx('w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none')}
                    onChange={(ev: React.ChangeEvent<HTMLSelectElement>) => {
                        router.push(ev.target.value)
                    }}
                >
                    {options.map(option => (
                        <option key={option.url} value={option.url}>{option.label}</option>
                    ))}
                </select>
                <div className="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_5269_2288" style={{maskType: 'alpha'}}
                              maskUnits="userSpaceOnUse" x="0" y="0"
                              width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_5269_2288)">
                            <path d="M12 15L7 10H17L12 15Z" fill="white"/>
                        </g>
                    </svg>
                </div>
            </div>
        </div>
        <div className="hidden md:flex md:gap-2 lg:block lg:space-y-2">
            {options.map(option => (
                <Link
                    key={option.url}
                    className={clsx('btn-dark-link truncate  !normal-case', {'!bg-primary !text-black': currentPath === option.url})}
                    href={option.url}>
                    {option.label}
                </Link>
            ))}
        </div>
    </>
}

const Layout = ({children,}: {
    children: React.ReactNode
}) => {
    const currentPath = usePathname()
    const [user] = useState<IUser>({...defaultUser, verified: currentPath !== '/profile/personal-information'})

    return <div className="flex flex-col space-y-8 lg:space-y-0 lg:grid lg:grid-cols-12 w-full lg:gap-4">
        <div className="lg:col-span-3 w-full">
            <Links options={Options}/>
        </div>
        <div className="lg:col-span-9 w-full space-y-8">
            <Card className="order-1 w-full lg:order-none mx-auto space-y-8">
                <div className="md:grid md:grid-cols-[auto_1fr] md:gap-8 space-y-8 md:space-y-0">
                    <div className="w-full justify-center items-center flex md:block">
                        <Avatar user={user}/>
                    </div>
                    <div>
                        <div className="md:grid md:grid-cols-2 gap-4 space-y-4 sm:space-y-0">
                            <div
                                className="space-y-2 flex-col sm:flex-row sm:space-y-0 md:col-span-2 md:flex md:items-center md:gap-4">
                                <div
                                    className="text-center items-center gap-2 flex justify-center">
                                    <Pencil/>
                                    <span
                                        className="text-stone-400 text-base font-normal leading-normal">
                                         Member since: {user.memberSince}
                                    </span>
                                </div>

                                <div
                                    className={clsx('flex justify-center items-center gap-1', {
                                        'text-secondary': user.verified,
                                        'text-rose-500': !user.verified
                                    })}>

                                    {user.verified
                                        ? <CheckCircleIcon className="w-5 h-5"/>
                                        : <XCircleIcon className="w-5 h-5"/>}

                                    {user.verified ? 'VERIFIED' : 'NOT VERIFIED'}
                                </div>
                            </div>
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
                            <div className="col-span-2">
                                <label className="text-stone-400 text-base font-bold leading-normal">
                                    Email
                                    <InputText readOnly={true} name={'email'} value={user.email}/>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </Card>
            {children}
        </div>
    </div>
}

export default Layout;