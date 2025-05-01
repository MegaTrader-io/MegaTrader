'use client';

import InputText from "@/components/InputText";
import {useRouter} from "next/navigation";
import Link from "next/link";
import React, {useEffect, useState} from "react";
import {ChevronLeftIcon} from "@heroicons/react/16/solid";
import Alert from "@/components/Alert";
import {TARGET_EMAIL} from "@/commons/credentials";
import {useLoading} from "@/context/LoadingContext";
import {Button} from "@/components/Button";
import Image from "next/image";

export default function ChangePassword() {
    const router = useRouter();
    const {setLoading, isLoading} = useLoading();
    const [form, setForm] = useState({
        email: "",
        password: "",
        confirm_password: "",
    });
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});

    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const email = params.get("email") || "";
        setForm((prev) => ({...prev, email}));
    }, []);

    function submitForm(event: React.FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (!form.password.trim()) {
            setFieldErrors((prev) => ({...prev, password: "Password is required."}));
            return;
        }

        if (!form.confirm_password.trim()) {
            setFieldErrors((prev) => ({...prev, confirm_password: "Confirmation password is required."}));
            return;
        }

        if (form.password !== form.confirm_password) {
            setFieldErrors({
                password: "The passwords do not match.",
                confirm_password: "The passwords do not match.",
            });
            return;
        }

        setLoading(true);
        setFieldErrors({});

        setTimeout(() => {
            setLoading(false);

            if (form.email === TARGET_EMAIL) {
                router.push(
                    `/auth/login?success-message=Your password has been changed successfully. Log in to your account.`
                );
                return;
            }

            setFieldErrors({
                form: "Something went wrong. Please try again later.",
            });
        }, 2000);
    }

    return (
        <>
            {fieldErrors.form && <Alert type="error" message={fieldErrors.form}/>}

            <Image src={'/assets/images/logo-mt.svg'} width={72} height={72} alt={'Logo Megatrader'}/>

            <div>
                <h1 className="text-white xl:text-nowrap text-5xl font-light uppercase leading-[60px] mb-2">
                    New password
                </h1>
                <h2 className="text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide">
                    Enter and confirm your new password.
                </h2>
            </div>

            <form onSubmit={submitForm} className="space-y-4 lg:my-8">
                <div>
                    <InputText
                        type="password"
                        placeholder="Password"
                        name="password"
                        value={form.password}
                        onChange={(e) => {
                            const value = e.target.value;
                            setForm((prev) => ({...prev, [e.target.name]: value}));
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
                            setForm((prev) => ({...prev, [e.target.name]: value}));
                        }}
                        errorMessage={fieldErrors.confirm_password}
                    />
                </div>

                <Button type="submit" disabled={isLoading} className="w-full !font-medium">
                    Update
                </Button>

                <Link
                    href="/auth/login"
                    className="h-12 w-full px-4 py-3 justify-center items-center gap-2 inline-flex"
                >
                    <div className="text-neutral-50 text-base font-medium uppercase leading-normal flex">
                        <ChevronLeftIcon className="w-6 h-6 text-white"/> Return to login
                    </div>
                </Link>
            </form>
        </>
    );
}
