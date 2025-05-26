'use client';

import React, {ChangeEvent, useEffect, useRef, useState} from 'react';
import InputText from "@/components/InputText";
import clsx from "clsx";
import {Button} from "@/components/Button";
import {InputCheckbox} from "@/components/InputCheckbox";
import {IShowAlert} from "@/app/(backoffice)/refferals/page";
import {useLoading} from "@/context/LoadingContext";

const DefaultConsentMessage = () => (
    <>
        I consent to the use of my email address to
        receive<br className="hidden md:block"/> updates and launch announcements.
    </>
);

const DefaultCompactConsentMessage = () => (
    <>
        I consent to the use of my email address to receive news, updates, and important notifications.
    </>
);


function SubscribeForm({cbShowAlert, compact = false}: {
    cbShowAlert?: (payload: IShowAlert | null) => void,
    compact?: boolean
}) {
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

        if (cbShowAlert) {
            cbShowAlert(null);
        }

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

            if (cbShowAlert) {
                cbShowAlert({
                    type: 'success',
                    message: 'Congratulations! You have successfully subscribed.'
                });
            }
        } catch (e) {
            console.info(e);
        } finally {
            setLoading(false);

            setTimeout(() => {
                inputEmail.current?.focus();
            }, 100);
        }
    }

    return (
        <>
            <form noValidate={true} onSubmit={handlerSubmitForm}
                  className={clsx('grid grid-rows-2 items-start gap-2', [compact ? 'mb-4 lg:flex' : 'md:flex my-4'])}>
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
                        {!compact && <DefaultConsentMessage/>}
                        {compact && <DefaultCompactConsentMessage/>}
                    </p>
                </InputCheckbox>
            </div>
        </>
    );
}

export default SubscribeForm;