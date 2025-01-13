'use client';

import InputText from "@/components/ui/input-text";
import { InputCheckbox } from "@/components/ui/input-checkbox";
import Link from "next/link";
import Badge from "@/components/ui/badge";
import Image from "next/image";
import React, { useState, useEffect } from "react";
import Alert from "@/components/ui/Alert";

export default function Login() {
    const [successMessage, setSuccessMessage] = useState("");
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [emailError, setEmailError] = useState("");
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
    const [isSubmitting, setIsSubmitting] = useState(false);

    function validateEmail(email: string): boolean {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const message = params.get("success-message");
        if (message) {
            setSuccessMessage(message);
        }
    }, []);

    function submitForm(event: React.FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (!email.trim()) {
            setEmailError("The email field is required");
            return;
        }

        if (!password.trim()) {
            setEmailError("The password field is required");
            return;
        }

        if (!validateEmail(email)) {
            setEmailError("Ups... The email is not correct");
            return;
        }

        if (!email || !password) {
            setFieldErrors({ form: "Both fields are required" });
            return;
        }

        setIsSubmitting(true);
        setFieldErrors({});

        setTimeout(() => {
            setIsSubmitting(false);

            setFieldErrors({
                form: "Something went wrong. Please check your email or your password are correct.",
                email: "These credentials do not match our records.",
            });
        }, 2000);
    }

    return (
        <div className="flex flex-1">
            <div className="flex w-full lg:w-2/5 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none xl:px-24">
                <div className="mx-auto w-full max-w-[409px]">
                    <div className="w-full mx-auto space-y-8">
                        {fieldErrors.form && (
                            <Alert type="error" message={fieldErrors.form} />
                        )}

                        {successMessage && (
                            <Alert type="success" message={successMessage} />
                        )}

                        <div>
                            <h1 className="text-white text-5xl font-light uppercase leading-[60px] mb-2">
                                SIGN IN
                            </h1>
                            <h2 className="text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide">
                                Welcome back! Please enter your details.
                            </h2>
                        </div>

                        <form onSubmit={submitForm} className="space-y-4 lg:my-8">
                            <InputText
                                type="email"
                                placeholder="Email"
                                name="email"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                errorMessage={emailError || fieldErrors.email}
                            />

                            <InputText
                                type="password"
                                placeholder="Password"
                                name="password"
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                            />

                            <div className="flex items-center justify-between">
                                <InputCheckbox
                                    className="text-base"
                                    label="Remember me"
                                    value="1"
                                    name="remember"
                                />
                                <Link href="/reset-password" className="text-base btn-link">
                                    Forgot Password?
                                </Link>
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
                                <div className="text-slate-950 text-base font-normal uppercase leading-normal">
                                    {isSubmitting ? "Loading..." : "Sign In"}
                                </div>
                            </button>
                        </form>

                        <div className="text-center space-y-2">
                            <div className="text-center w-full text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide">
                                Don’t have an account?
                            </div>

                            <Link
                                href="/register"
                                className="h-12 w-full px-4 py-3 bg-stone-800 rounded-xl border border-neutral-700 justify-center items-center gap-2 inline-flex"
                            >
                                <div className="text-neutral-50 text-base font-normal uppercase leading-normal">
                                    Create account
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
