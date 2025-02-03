'use client';

import React, {useState} from 'react';
import InputText from "@/components/InputText";
import {countries, defaultUser, languages} from "@/commons/data";
import {IUser} from "@/commons/interfaces";
import {Button} from "@/components/Button";

function Page() {
    const [user, setUser] = useState<IUser>(defaultUser)

    function changeFields(ev: React.ChangeEvent<HTMLSelectElement | HTMLInputElement>) {
        setUser(user => ({...user, [ev.target.name]: ev.target.value}))
    }

    function onSubmit(ev: React.ChangeEvent<HTMLFormElement>) {
        ev.preventDefault();
        console.info('submit stuff', user);
    }

    return (
        <div className="text-white w-full">
            <form onSubmit={onSubmit} noValidate={true} className="w-full text-white grid grid-cols-2 gap-4">
                <div className="space-y-4">
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal w-full">
                            Address
                            <InputText name={'address'} value={user.address} onChange={changeFields}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal w-full">
                            Zip-code
                            <InputText required={true} name={'zipCode'} value={user.zipCode} onChange={changeFields}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Country
                            <div className="relative w-full">
                                <select
                                    name="country"
                                    value={user.country}
                                    className="w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none"
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
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Language
                            <div className="relative w-full">
                                <select
                                    value={user.language}
                                    name="language"
                                    className="w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none"
                                    onChange={changeFields}
                                >
                                    <option value=""></option>
                                    {languages.map(language => (
                                        <option key={language.id} value={language.id}>{language.description}</option>
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
                            <InputText name={'city'} value={user.city} onChange={changeFields}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            State
                            <InputText name={'state'} value={user.state} onChange={changeFields}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Phone
                            <InputText name={'phone'} value={user.phone} onChange={changeFields}/>
                        </label>
                    </div>
                </div>
            </form>
        </div>
    );
}

export default Page;