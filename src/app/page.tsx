'use client';

import Image from "next/image";
import React, {useRef, useState} from "react";
import InputText from "@/components/InputText";
import clsx from "clsx";
import {Button} from "@/components/Button";
import {useLoading} from "@/context/LoadingContext";
import {sleep} from "@/commons/utils";
import {TARGET_EMAIL} from "@/commons/credentials";
import {IShowAlert} from "@/app/(backoffice)/affiliates/page";
import Alert from "@/components/Alert";
import HomeLayout from "@/components/HomeLayout";

const Home = () => {
    const [showAlert, setShowAlert] = useState<IShowAlert | null>(null);
    const {setLoading, isLoading: sendingEmail} = useLoading();
    const inputEmail = useRef<HTMLInputElement | null>(null);
    const [email, setEmail] = useState('');
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const validateEmail = () => {
        if (inputEmail.current) {
            const value = inputEmail.current.value.trim();

            if (!value) {
                setErrorMessage("This field is required");
                return false;
            }

            if (!inputEmail.current.validity.valid) {
                setErrorMessage("Invalid email address");
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

        setLoading(true);
        await sleep(900);

        let result: IShowAlert = {
            type: 'error',
            message: 'Something went wrong. Check your internet connection and try again later.'
        }

        if (email === TARGET_EMAIL) {
            result = {
                type: 'success',
                message: 'Congratulations! You have successfully subscribed.'
            }
        }

        setShowAlert(result)
        setLoading(false);
        setEmail('')
    }

    return <HomeLayout>
        <div className="mx-auto sm:w-[406px] space-y-8 h-[calc(100dvh-64px)] content-center sm:h-full sm:content-normal">
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
                    Comming soon!
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
                    className={clsx(!!errorMessage ? 'placeholder:text-rose-500' : null)}
                    onChange={(e) => setEmail(e.target.value)}
                    value={email}
                    errorMessage={errorMessage}
                    placeholder={'Enter your email'}
                    name={'email'}/>
                <Button disabled={sendingEmail} type={'submit'} className="w-full md:w-auto">
                    SUBSCRIBE
                </Button>
            </form>
        </div>
    </HomeLayout>
}

export default Home;