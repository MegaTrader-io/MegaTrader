'use client';

import React, {useState} from 'react';
import InputText from "@/components/InputText";
import {countries, defaultUser, languages} from "@/commons/data";
import {IUser} from "@/commons/interfaces";
import {Button} from "@/components/Button";
import clsx from "clsx";
import Alert from "@/components/Alert";

function Page() {
    const [user, setUser] = useState<IUser>(defaultUser)
    const [errors, setErrors] = useState<{ [key: string]: string }>({});
    const [updated, setUpdated] = useState<boolean>(false);
    const hasErrors = Object.values(errors).filter(error => error !== '').length > 0;

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
        if (!user.language.trim()) newErrors.language = "This field is required.";
        if (!user.phone.trim()) newErrors.phone = "This field is required.";

        return newErrors;
    }

    function onSubmit(ev: React.ChangeEvent<HTMLFormElement>) {
        ev.preventDefault();

        const validationErrors = validateFields();

        if (Object.keys(validationErrors).length > 0) {
            setErrors(validationErrors);
            return;
        }

        console.info('submit stuff', user);
        setErrors({});
        setUpdated(true)
    }

    return (
        <div className="w-full space-y-4">
            {!updated && hasErrors &&
                <Alert className="w-full" type="error" message='Somethig went wrogn. Please try again later.'/>}
            {updated &&
                <Alert className="w-full" type="success"
                       message='Great! Your personal information have been updated successfully'/>}
            <form onSubmit={onSubmit} className="w-full text-white grid grid-cols-2 gap-4">
                <div className="space-y-4">
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal w-full">
                            Address
                            <InputText name={'address'} value={user.address} onChange={changeFields}
                                       errorMessage={errors.address}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal w-full">
                            Zip-code
                            <InputText name={'zipCode'} value={user.zipCode} onChange={changeFields}
                                       errorMessage={errors.zipCode}/>
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
                                        className={clsx('w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none',
                                            errors.country
                                                ? 'ring-1 ring-red-500 text-red-500 border-transparent'
                                                : ''
                                        )}
                                        onChange={changeFields}
                                    >
                                        <option value=""></option>
                                        {countries.map(country => (
                                            <option key={country.id} value={country.id}>{country.description}</option>
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
                            Language
                            <div>
                                <div className="relative w-full">
                                    <select
                                        value={user.language}
                                        name="language"
                                        className={clsx('w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none',
                                            errors.language
                                                ? 'ring-1 ring-red-500 text-red-500 border-transparent'
                                                : ''
                                        )}
                                        onChange={changeFields}
                                    >
                                        <option value=""></option>
                                        {languages.map(language => (
                                            <option key={language.id}
                                                    value={language.id}>{language.description}</option>
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

                                {errors.language && (
                                    <span
                                        id={`language-error`}
                                        className="text-rose-500 text-xs mt-4 leading-tight"
                                    >
                    {errors.language}
                </span>
                                )}

                            </div>
                        </label>
                    </div>

                    <div>
                        <Button type='submit' variant={'primary'}>
                            Save changes
                        </Button>
                    </div>
                </div>
                <div className="space-y-4">
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            City
                            <InputText name={'city'}
                                       value={user.city}
                                       onChange={changeFields}
                                       errorMessage={errors.city}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            State
                            <InputText
                                name={'state'}
                                value={user.state}
                                onChange={changeFields}
                                errorMessage={errors.state}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Phone
                            <InputText
                                name={'phone'}
                                value={user.phone}
                                onChange={changeFields}
                                errorMessage={errors.phone}/>
                        </label>
                    </div>
                </div>
            </form>
        </div>
    );
}

export default Page;