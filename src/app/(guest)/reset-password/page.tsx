'use client';

import InputText from "@/components/InputText";
import Link from "next/link";
import React, {useState, useEffect} from "react";
import {ChevronLeftIcon} from "@heroicons/react/16/solid";
import Alert from "@/components/Alert";
import {TARGET_EMAIL} from "@/commons/credentials";

export default function ResetPassword() {
    const [email, setEmail] = useState("");
    const [emailError, setEmailError] = useState("");
    const [successMessage, setSuccessMessage] = useState('');
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

        setTimeout(() => {
            setIsSubmitting(false);

            if (email === TARGET_EMAIL) {
                setEmail('')
                setSuccessMessage('The email have been sent successfully. Visit your email address, and follow the link to change your password.')
                return
            }

            setFieldErrors({
                form: "Something went wrong. Please make sure this email address exist.",
            });
        }, 800);
    }

    return (
        <>
            {fieldErrors.form && (
                <Alert type="error" message={fieldErrors.form}/>
            )}

        {successMessage && (<Alert type="success" message={successMessage}/>)}

            <div>
                <h1 className="text-white text-5xl font-light uppercase leading-[60px] mb-2">
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
        </>
    );
}
