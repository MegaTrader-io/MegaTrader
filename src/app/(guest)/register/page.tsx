'use client';

import InputText from "@/components/InputText";
import {InputCheckbox} from "@/components/InputCheckbox";
import Link from "next/link";
import React, {useState} from "react";
import {XMarkIcon} from "@heroicons/react/16/solid";


export default function Login() {
    const [form, setForm] = useState({
        fullName: '',
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

    return (
        <>
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
                        placeholder="Full Name"
                        name="fullName"
                        value={form.fullName}
                        onChange={(e) => {
                            const value = e.target.value;
                            setForm(prev => ({...prev, [e.target.name]: value}));
                        }}
                        errorMessage={fieldErrors.fullName}
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
                            <span className="text-xs sm:text-base text-white"> Agree to our</span>
                            {' '}
                            <Link href="#" className="text-xs sm:text-base btn-link flex-inline">
                                Privacy Policy
                            </Link>
                            {' '}
                            <span className="text-white text-xs sm:text-base">and</span>
                            {' '}
                            <Link href="#" className="text-xs sm:text-base btn-link flex-inline">
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
        </>
    );
}
