import React, {useRef, useState} from 'react';
import Card, {CardTitle} from "@/components/Card";
import {Button} from "@/components/Button";
import ShareReferralLink from "@/app/(backoffice)/affiliates/_components/ShareReferralLink";
import InputText from "@/components/InputText";
import {sleep} from "@/commons/utils";
import {TARGET_EMAIL} from "@/commons/credentials";
import clsx from "clsx";
import {useLoading} from "@/context/LoadingContext";

function InviteYourFriends({displayMessage}: {
    displayMessage: ({success, message}: { success: boolean, message: string }) => void
}) {
    const {setLoading, isLoading: sendingEmail} = useLoading();
    const inputEmail = useRef<HTMLInputElement | null>(null);
    const [email, setEmail] = useState('');
    const [errorMessage, setErrorMessage] = useState<string | null>(null);

    const validateEmail = () => {
        if (inputEmail.current) {
            const value = inputEmail.current.value.trim();

            if (!value) {
                setErrorMessage("Email is required");
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

    const handlerInvitation = async (ev: React.ChangeEvent<HTMLFormElement>) => {
        ev.preventDefault();

        if (!validateEmail()) {
            return;
        }

        inputEmail.current?.blur();

        setLoading(true);
        await sleep(900);

        let result = {
            success: false,
            message: 'An error occurred while sending your invitation. Please try again later.'
        }

        if (email === TARGET_EMAIL) {
            result = {
                success: true,
                message: 'Your invitation has been sent successfully!'
            };
        }

        displayMessage(result);
        setLoading(false);
        setEmail('')
    }

    return (
        <Card id="invite-your-friends" className="w-full lg:col-span-5 p-4 text-white">
            <CardTitle className="mb-2">
                INVITE YOUR FRIENDS
            </CardTitle>
            <p className="text-stone-400 text-base font-normal  leading-normal">
                Add your friends email addresses and sent them invitations to join!
            </p>

            <form noValidate={true} onSubmit={handlerInvitation}
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
                    placeholder={'Email addresses...'}
                    name={'email_referral'}/>
                <Button disabled={sendingEmail} type={'submit'} className="w-full md:w-auto">
                    SEND
                </Button>
            </form>

            <ShareReferralLink/>
        </Card>
    );
}

export default InviteYourFriends;