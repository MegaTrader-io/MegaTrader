'use client'

import React, {useEffect, useState} from "react";
import {usePathname, useRouter} from "next/navigation";
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
    const router = useRouter()

    return <>
        <div className="block md:hidden">
            <div className="relative w-full">
                <select
                    name="link"
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
        <div className="hidden md:flex md:gap-2">
            {options.map(option => (
                <Link
                    key={option.url}
                    className={clsx('btn-dark-link truncate !normal-case', {'!bg-primary !text-black': currentPath === option.url})}
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
    const [user, setUser] = useState<IUser>(defaultUser)

    useEffect(() => {
        setUser(user => ({...user, verified: currentPath !== '/profile/personal-information'}))
    }, [currentPath])

    return <>
        <>
            <div className="space-y-4 w-full">
                <div className="lg:space-y-4 md:space-y-0 grid grid-cols-[1fr_auto] w-full items-start">
                    <div className="order-2 mb-4 md:mb-8 lg:mb-0 lg:order-none col-span-2">
                        <Links options={Options}/>
                    </div>
                    <div className="order-3 w-full lg:order-none lg:pr-16">
                        {children}
                    </div>
                    <Card className="order-1 w-full md:w-[519px] !mb-8 lg:order-none mx-auto space-y-8">
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