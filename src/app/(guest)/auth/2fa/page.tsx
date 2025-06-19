'use client';

import InputText from "@/components/InputText";
import React, {useState} from "react";
import Alert from "@/components/Alert";
import {useRouter} from "next/navigation";
import {useLoading} from "@/context/LoadingContext";
import Image from "next/image";
import {sleep} from "@/commons/utils";

export default function Login() {
    const router = useRouter();
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
    const [validCode, setValidCode] = useState<boolean | undefined>(undefined)
    const {isLoading, setLoading} = useLoading();
    const [form, setForm] = useState<{ code: string }>({
        code: '',
    });

    async function handleSubmitCode(event: React.FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (!form.code.trim()) {
            setFieldErrors((prev) => ({...prev, code: "The code is required."}));
            return;
        }

        if (form.code.toString().length < 6) {
            setFieldErrors((prev) => ({...prev, code: "The code is invalid."}));
            return;
        }

        setLoading(true);

        await sleep(1200);

        setLoading(false);

        if (form.code === '123456') {
            localStorage.setItem('isLoggedIn', 'true');

            router.push(
                `/account-overview`
            );

            return;
        }

        setValidCode(false)
    }

    return (
        <>
            {validCode === false && (
                <Alert className="w-full text-mgt-dark"
                       type={'error'}
                       message={'The code you entered was wrong'}/>
            )}

            <Image src={'/assets/images/logo-mt.svg'} width={72} height={72} alt={'Logo Megatrader'}/>

            <div>
                <h1 className="text-white text-5xl font-light uppercase leading-[60px] mb-2">
                    Enter 2FA Code
                </h1>
                <h2 className="text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide">
                    Enter 6-digit code from authenticator app.
                </h2>
            </div>

            <form onSubmit={handleSubmitCode}
                  noValidate={true}
                  className="space-y-4 lg:my-8">

                <InputText
                    type="text"
                    placeholder="Enter your code"
                    name="code"
                    maxLength={6}
                    value={form.code}
                    onChange={(e) => {
                        const value = e.target.value;
                        setFieldErrors((prev) => ({...prev, code: ""}));
                        setForm(prev => ({...prev, [e.target.name]: value}));
                    }}
                    errorMessage={fieldErrors.code}
                />

                <button
                    type="submit"
                    disabled={isLoading}
                    className={`h-12 w-full px-4 disabled:opacity-30 py-3 rounded-xl border-2 justify-center items-center gap-2 inline-flex ${
                        isLoading
                            ? "bg-gray-500 border-gray-500 cursor-not-allowed"
                            : "bg-mgt-primary border-mgt-primary"
                    }`}
                >
                    <div className="text-slate-950 text-base font-medium uppercase leading-normal">
                        Sign In
                    </div>
                </button>
            </form>
        </>
    );
}
