'use client';

import InputText from "@/components/InputText";
import {InputCheckbox} from "@/components/InputCheckbox";
import Link from "next/link";
import Badge from "@/components/Badge";
import Image from "next/image";
import React, {useState} from "react";
import {XMarkIcon} from "@heroicons/react/16/solid";


export default function Login() {
    const [form, setForm] = useState({
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        password: '',
        confirm_password: '',
    });
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);

    function submitForm(event: React.FormEvent<HTMLFormElement>) {
        event.preventDefault();

        setIsSubmitting(true);
        setFieldErrors({});

        setTimeout(() => {
            setIsSubmitting(false);

            setFieldErrors({
                form: "Something went wrong. Please check if your data is correct.",
            });
        }, 2000);
    }

    // const isFormValid = email && password && !emailError;

    return (
        <div className="flex flex-1">
            <div
                className="flex w-full lg:w-2/5 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none  xl:px-24">
                <div className="mx-auto w-full max-w-[409px]">
                    <div className="w-full mx-auto space-y-8">
                        {fieldErrors.form && (
                            <div
                                className="p-4 bg-[#1e1e1e] rounded-lg justify-start items-start gap-4 inline-flex overflow-hidden">
                                <div className="w-6 h-6 rounded-full bg-mgt-error">
                                    <XMarkIcon className="w-6 h-6"/>
                                </div>
                                <div
                                    className="grow shrink basis-0 self-stretch text-rose-400 text-base font-normal leading-normal">{fieldErrors.form}
                                </div>
                            </div>
                        )}

                        <div>
                            <h1 className="text-white text-5xl font-light uppercase leading-[60px] mb-2">
                                Register
                            </h1>
                            <h2
                                className="text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide"
                            >
                                Create your account to get started!
                            </h2>
                        </div>

                        <form onSubmit={submitForm} className="space-y-4 lg:my-8">
                            <div>
                                <InputText
                                    type="text"
                                    placeholder="First Name"
                                    name="first_name"
                                    value={form.first_name}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        setForm(prev => ({...prev, [e.target.name]: value}));
                                    }}
                                    errorMessage={fieldErrors.first_name}
                                />
                            </div>

                            <div>
                                <InputText
                                    type="text"
                                    placeholder="Last Name"
                                    name="last_name"
                                    value={form.last_name}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        setForm(prev => ({...prev, [e.target.name]: value}));
                                    }}
                                    errorMessage={fieldErrors.last_name}
                                />
                            </div>

                            <div>
                                <InputText
                                    type="text"
                                    placeholder="Email"
                                    name="email"
                                    value={form.email}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        setForm(prev => ({...prev, [e.target.name]: value}));
                                    }}
                                    errorMessage={fieldErrors.email}
                                />
                            </div>

                            <div>
                                <InputText
                                    type="text"
                                    placeholder="Phone Number"
                                    name="phone"
                                    value={form.phone}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        setForm(prev => ({...prev, [e.target.name]: value}));
                                    }}
                                    errorMessage={fieldErrors.phone}
                                />
                            </div>

                            <div>
                                <InputText
                                    type="password"
                                    placeholder="Password"
                                    name="password"
                                    value={form.password}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        setForm(prev => ({...prev, [e.target.name]: value}));
                                    }}
                                    errorMessage={fieldErrors.password}
                                />
                            </div>


                            <div>
                                <InputText
                                    type="password"
                                    placeholder="Confirm Password"
                                    name="confirm_password"
                                    value={form.confirm_password}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        setForm(prev => ({...prev, [e.target.name]: value}));
                                    }}
                                    errorMessage={fieldErrors.confirm_password}
                                />
                            </div>


                            <div className="inline-flex gap-2 items-center w-full">
                                <InputCheckbox
                                    value="1"
                                    name="remember"
                                >
                                    <div className="select-none">
                                        <span className="text-base text-white"> Agree to our</span>
                                        {' '}
                                        <Link href="#" className="text-base btn-link flex-inline">
                                            Privacy Policy
                                        </Link>
                                        {' '}
                                        <span className="text-white">and</span>
                                        {' '}
                                        <Link href="#" className="text-base btn-link flex-inline">
                                            Refund Policy
                                        </Link>
                                    </div>
                                </InputCheckbox>

                            </div>

                            <button
                                type="submit"
                                disabled={isSubmitting}
                                className={`h-12 w-full px-4 disabled:opacity-30 py-3 rounded-xl border-2 justify-center items-center gap-2 inline-flex ${
                                    isSubmitting
                                        ? "bg-gray-500 border-gray-500 cursor-not-allowed"
                                        : "bg-mgt-primary border-mgt-primary"
                                }`}
                            >
                                <div
                                    className="text-slate-950 text-base font-normal uppercase leading-normal"
                                >
                                    {isSubmitting ? "Loading..." : "Register"}
                                </div>
                            </button>
                        </form>

                        <div className="text-center space-y-2">
                            <Link
                                href="#"
                                className="text-center w-full text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide"
                            >
                                Already have an account?
                            </Link>

                            <Link
                                href="/auth/login"
                                className="h-12 w-full px-4 py-3 bg-stone-800 rounded-xl border border-neutral-700 justify-center items-center gap-2 inline-flex"
                            >
                                <div className="text-neutral-50 text-base font-normal uppercase leading-normal">
                                    Go to Login
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <div className="relative hidden w-0 flex-1 lg:block bg-[#1e1e1e] pl-[120px] overflow-hidden">
                <Badge className="mt-[91px] mb-[17px]">Start earning up to 90% profit</Badge>
                <p className="text-stone-400 text-base font-normal leading-normal max-w-[434px]">
                    No minimum trading days on your evaluation. Unlocking opportunities and maximizing potential in the
                    dynamic world of trading.
                </p>

                <div
                    className="h-full w-full mt-[111px] overflow-hidden relative bg-[#151211] rounded-tl-[36px] shadow-[0px_30px_35px_32px_rgba(0,0,0,0.20)] border-l-8 border-t-8 border-[#474b54]"
                >
                    <div className="mx-auto w-10/12 h-0">
                        <Image
                            src="/assets/images/img_3.png"
                            alt="Trading Platform Interface"
                            width={1000}
                            height={700}
                            layout="responsive"
                            quality={100}
                        />
                    </div>
                </div>
            </div>
        </div>
    );
}
