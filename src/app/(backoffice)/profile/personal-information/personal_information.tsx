'use client';

import React, {useState} from 'react';
import InputText from "@/components/InputText";
import {countries, defaultUser} from "@/commons/data";
import {IUser} from "@/commons/interfaces";
import {Button} from "@/components/Button";
import clsx from "clsx";
import Alert from "@/components/Alert";
import Avatar from "@/app/(backoffice)/profile/_components/Avatar";
import Pencil from "@/components/Pencil";
import {CheckCircleIcon} from "@heroicons/react/16/solid";
import {XCircleIcon} from "@heroicons/react/20/solid";
import {useLoading} from "@/context/LoadingContext";
import {sleep} from "@/commons/utils";

function PersonalInformation() {
    const {setLoading} = useLoading();
    const [user, setUser] = useState<IUser>(defaultUser)
    const [errors, setErrors] = useState<{ [key: string]: string }>({});
    const [updated, setUpdated] = useState<boolean>(false);
    const hasErrors = Object.values(errors).filter(error => error !== '').length > 0;
    const [defaultUserData] = useState<IUser>(defaultUser);
    const isDirty = JSON.stringify(defaultUserData) !== JSON.stringify(user);

    function changeFields(ev: React.ChangeEvent<HTMLSelectElement | HTMLInputElement>) {
        const {name, value} = ev.target;
        setUser(user => ({...user, [name]: value}));

        if (errors[name]) {
            setErrors(prevErrors => ({...prevErrors, [name]: ''}));
        }
    }


    function validateFields() {
        const newErrors: { [key: string]: string } = {};

        if (!user.address.trim()) newErrors.address = "This field is required.";
        if (!user.zipCode.trim()) newErrors.zipCode = "This field is required.";
        if (!user.city.trim()) newErrors.city = "This field is required.";
        if (!user.state.trim()) newErrors.state = "This field is required.";
        if (!user.country.trim()) newErrors.country = "This field is required.";
        if (!user.phone.trim()) newErrors.phone = "This field is required.";

        return newErrors;
    }


    async function onSubmit(ev: React.ChangeEvent<HTMLFormElement>) {
        ev.preventDefault();

        const validationErrors = validateFields();

        if (Object.keys(validationErrors).length > 0) {
            setErrors(validationErrors);
            setUpdated(false)
            return;
        }

        await sleep(1200);

        setLoading(true);

        console.info('submit stuff', user);
        setErrors({});
        setUpdated(true)

        setLoading(false);
    }

    return (
        <>
            <div className="space-y-8 grid grid-cols-[auto_1fr] gap-4">
                <div className="w-full">
                    <Avatar user={user}/>
                </div>
                <div className="flex items-center">
                    <div>
                        <div
                            className="text-center items-center gap-1 flex">
                            <Pencil/>
                            <span
                                className="text-stone-400 text-base font-normal leading-normal">
                                         Member since: {user.memberSince}
                                    </span>
                        </div>

                        <div
                            className={clsx('flex items-center gap-1', {
                                'text-secondary': user.verified,
                                'text-rose-500': !user.verified
                            })}>

                            {user.verified
                                ? <CheckCircleIcon className="w-5 h-5"/>
                                : <XCircleIcon className="w-5 h-5"/>}

                            {user.verified ? 'VERIFIED' : 'UNVERIFIED'}
                        </div>
                    </div>
                </div>
            </div>

            <div className="mb-4 mt-8">
                {hasErrors &&
                    <Alert className="w-full text-black" type="error"
                           message='Somethig went wrogn. Please try again later.'/>}
                {updated &&
                    <Alert className="w-full text-black" type="success"
                           message='Great! Your personal information have been updated successfully'/>}
            </div>

            <div className="w-full space-y-4">
                <form onSubmit={onSubmit}
                      className="w-full gap-4 space-y-4 text-white lg:grid lg:grid-cols-2 lg:space-y-0">
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

                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal w-full">
                            Address
                            <InputText name={'address'} placeholder={'Ex: Second Street'} value={user.address}
                                       onChange={changeFields}
                                       errorMessage={errors.address}/>
                        </label>
                    </div>

                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            City
                            <InputText name={'city'}
                                       placeholder={'Ex: Miami'}
                                       value={user.city}
                                       onChange={changeFields}
                                       errorMessage={errors.city}/>
                        </label>
                    </div>

                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal w-full">
                            Zip-code
                            <InputText name={'zipCode'}
                                       placeholder={'Ex: 269574'}
                                       value={user.zipCode}
                                       onChange={changeFields}
                                       errorMessage={errors.zipCode}/>
                        </label>
                    </div>

                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            State
                            <InputText
                                name={'state'}
                                placeholder={'Ex: 269574'}
                                value={user.state}
                                onChange={changeFields}
                                errorMessage={errors.state}/>
                        </label>
                    </div>

                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Country
                            <div>
                                <div className="relative w-full">
                                    <select
                                        name="country"
                                        value={user.country}
                                        onChange={changeFields}
                                        className={clsx(
                                            'w-full py-3 px-4 pr-10 font-medium rounded-xl border border-neutral-700 bg-[#1e1e1e]/70 appearance-none focus:outline-none',
                                            user.country === ''
                                                ? 'text-neutral-700 '
                                                : 'text-stone-400',
                                            errors.country
                                                ? 'ring-1 ring-red-500 border-transparent'
                                                : ''
                                        )}
                                        aria-describedby={errors.country ? 'country-error' : undefined}
                                    >
                                        <option value="" disabled hidden>Select a
                                            Country
                                        </option>
                                        {countries.map(country => (
                                            <option key={country.id}
                                                    value={country.id}>{country.description}</option>
                                        ))}
                                    </select>
                                    <div
                                        className="absolute inset-y-0 right-3 flex items-center pointer-events-none">
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
                                {errors.country && (
                                    <span
                                        id={`country-error`}
                                        className="text-rose-500 text-xs mt-4 leading-tight"
                                    >
                    {errors.country}
                </span>
                                )}
                            </div>
                        </label>
                    </div>

                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Phone
                            <InputText
                                placeholder={'44-666-77-888'}
                                name={'phone'}
                                value={user.phone}
                                onChange={changeFields}
                                errorMessage={errors.phone}/>
                        </label>
                    </div>

                    <div className="mt-4 md:mt-0">
                        <Button type='submit' disabled={!isDirty} variant={'primary'}>
                            Save changes
                        </Button>
                    </div>
                </form>
            </div>
        </>

    );
}

export default PersonalInformation;