'use client';

import InputText from "@/components/ui/input-text";
import Link from "next/link";
import Badge from "@/components/ui/badge";
import Image from "next/image";
import React, {useState, useEffect} from "react";
import {XMarkIcon, ChevronLeftIcon} from "@heroicons/react/16/solid";
import {useRouter} from 'next/navigation'

export default function Login() {
    const router = useRouter();
    const [email, setEmail] = useState("");
    const [emailError, setEmailError] = useState("");
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [debouncedEmail, setDebouncedEmail] = useState(email);

    function validateEmail(email: string): boolean {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    useEffect(() => {
        const handler = setTimeout(() => {
            setDebouncedEmail(email);
        }, 500);

        return () => clearTimeout(handler);
    }, [email]);

    useEffect(() => {
        if (!debouncedEmail) {
            setEmailError("");
            return;
        }

        if (!validateEmail(debouncedEmail)) {
            setEmailError("Ups... The email is not correct");
        } else {
            setEmailError("");
        }
    }, [debouncedEmail]);

    function submitForm(event: React.FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (!email.trim()) {
            setEmailError("The email field is required");
            return;
        }

        if (!validateEmail(email)) {
            setEmailError("Ups... The email is not correct");
            return;
        }

        setIsSubmitting(true);
        setFieldErrors({});

        router.push("/reset-password/change");
    }

    // const isFormValid = email && password && !emailError;

    return (
        <div className="flex flex-1">
            <div className="flex w-full lg:w-2/5 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none  xl:px-24">
                <div className="mx-auto w-full max-w-[409px]">
                    <div className="w-full mx-auto space-y-8">
                        {fieldErrors.form && (
                            <div
                                className="p-4 bg-[#1e1e1e] rounded-lg justify-start items-start gap-4 inline-flex overflow-hidden">
                                <div className="w-6 h-6 rounded-full bg-mgt-text-error">
                                    <XMarkIcon className="w-6 h-6"/>
                                </div>
                                <div
                                    className="grow shrink basis-0 self-stretch text-rose-400 text-base font-normal leading-normal">{fieldErrors.form}
                                </div>
                            </div>
                        )}

                        <div>
                            <h1 className="text-white text-nowrap text-5xl font-light uppercase leading-[60px] mb-2">
                                Reset password
                            </h1>
                            <h2
                                className="text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide"
                            >
                                Enter your email here to reset your password.
                            </h2>
                        </div>

                        <form onSubmit={submitForm} className="space-y-4 lg:my-8">
                            <div>
                                <InputText
                                    type="email"
                                    placeholder="Email"
                                    name="email"
                                    value={email}
                                    onChange={(e) => {
                                        const value = e.target.value;
                                        setEmail(value);

                                        if (!value) {
                                            setEmailError("");
                                            setFieldErrors((prev) => ({...prev, email: ""}));
                                            return;
                                        }
                                    }}
                                    errorMessage={emailError || fieldErrors.email}
                                />
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
                                    {isSubmitting ? "Loading..." : "Send email"}
                                </div>
                            </button>

                            <Link
                                href="/auth/login"
                                className="h-12 w-full px-4 py-3 justify-center items-center gap-2 inline-flex"
                            >
                                <div
                                    className="text-neutral-50 text-base font-normal uppercase leading-normal flex">
                                    <ChevronLeftIcon className="w-6 h-6 text-white"/> Return to login
                                </div>
                            </Link>
                        </form>
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
