'use client';

import Image from "next/image";
import React, {ChangeEvent, useEffect, useRef, useState} from "react";
import InputText from "@/components/InputText";
import clsx from "clsx";
import {Button} from "@/components/Button";
import {useLoading} from "@/context/LoadingContext";
import {IShowAlert} from "@/app/(backoffice)/refferals/page";
import Alert from "@/components/Alert";
import HomeLayout from "@/components/HomeLayout";
import {InputCheckbox} from "@/components/InputCheckbox";

const Home = () => {
    const [showAlert, setShowAlert] = useState<IShowAlert | null>(null);
    const {setLoading, isLoading: sendingEmail} = useLoading();
    const [form, setForm] = useState<{ email: string, email_consent: boolean }>({
        email: '',
        email_consent: false
    });
    const inputEmail = useRef<HTMLInputElement | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const canSubscribe = form.email_consent;

    useEffect(() => {
        inputEmail.current?.focus();
    }, [inputEmail]);

    const validateEmail = () => {
        if (inputEmail.current) {
            const value = inputEmail.current.value.trim();
            inputEmail.current.value = value;

            if (value === '') {
                return false;
            }

            if (!value) {
                inputEmail.current.value = '';
                setErrorMessage("This field is required");
                return false;
            }

            if (!inputEmail.current.validity.valid) {
                setErrorMessage("Invalid email");
                return false;
            }

            setErrorMessage(null);
            return true;
        }
        return false;
    };

    const handlerSubmitForm = async (ev: React.ChangeEvent<HTMLFormElement>) => {
        ev.preventDefault();

        if (!validateEmail()) {
            return;
        }

        inputEmail.current?.blur();

        setShowAlert(null);
        setLoading(true);

        try {
            const response = await fetch('/api/subscribe', {
                method: 'POST',
                body: JSON.stringify({
                    email: form.email
                })
            });

            const data: { success: boolean, message: string } = await response.json();

            if (!data.success) {
                setErrorMessage(data.message);
                return;
            }

            setForm({
                email: '',
                email_consent: false
            })

            setShowAlert({
                type: 'success',
                message: 'Congratulations! You have successfully subscribed.'
            })
        } catch (e) {
            console.info(e);
        } finally {
            setLoading(false);

            setTimeout(() => {
                inputEmail.current?.focus();
            }, 100);
        }
    }

    return <HomeLayout>
        <div
            className="mx-auto sm:w-[406px] xl:w-[406px] space-y-8 h-full">
            {showAlert && (
                <div className="w-full">
                    <Alert type={showAlert.type}
                           message={showAlert.message}/>
                </div>
            )}

            <div className="flex gap-4 items-center justify-center">
                <Image src={'/assets/images/logo-mt.svg'} width={60} height={60} alt={'Logo Megatrader'}/>
                <Image
                    src="../assets/images/megatrader-original.svg"
                    alt="Logo"
                    width={250}
                    height={45}
                    className="w-[250px] h-[44.63px]"
                />
            </div>

            <div className="space-y-4">
                <h1 className="self-stretch text-center justify-start text-white text-[40px] font-medium font-['Roboto'] uppercase leading-[48px]">
                    Coming soon!
                </h1>
                <h2 className="self-stretch text-center justify-start text-stone-400 text-base font-medium font-['Roboto'] leading-normal">
                    Empowering traders with innovative solutions, unmatched reliability, and tools designed to elevate
                    your trading journey to new heights.
                </h2>
            </div>

            <form noValidate={true} onSubmit={handlerSubmitForm}
                  className="grid grid-rows-2 md:flex items-start gap-2 my-4">
                <InputText
                    ref={inputEmail}
                    required={true}
                    type={'email'}
                    disabled={sendingEmail}
                    onBlur={validateEmail}
                    className={clsx(!!errorMessage ? 'placeholder:text-rose-500' : null)}
                    onChange={(e: ChangeEvent<HTMLInputElement>) => {
                        const value = e.target.value;
                        setForm(item => ({
                            ...item,
                            'email': value
                        }))
                    }}
                    value={form.email}
                    errorMessage={errorMessage}
                    placeholder={'Enter your email'}
                    name={'email'}/>

                <Button disabled={!canSubscribe || sendingEmail || !!errorMessage} type={'submit'}
                        className="w-full md:w-auto">
                    SUBSCRIBE
                </Button>
            </form>

            <div>
                <InputCheckbox
                    checked={form.email_consent}
                    onChange={(e) => {
                        const checked = e.target.checked;
                        setForm(item => ({
                            ...item,
                            'email_consent': checked
                        }))
                    }}
                    name="email_consent">
                    <p className="select-none w-full text-white text-base font-medium leading-normal">
                        I consent to the use of my email address to<br/> receive updates and launch announcements.
                    </p>
                </InputCheckbox>
            </div>
        </div>
    </HomeLayout>
}

export default Home;