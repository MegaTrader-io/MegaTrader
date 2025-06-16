import React, {useState} from 'react';
import QRcode from "@/components/QRcode";
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
import {useLoading} from "@/context/LoadingContext";
import Alert, {AlertType} from "@/components/Alert";
import {CopyButton} from "@/components/CopyButton";

const defaultQRCode = 'PHDNUETGDHNYRASBDF';

function TwoFactorAuthentication() {
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
    const [validCode, setValidCode] = useState<boolean | undefined>(undefined)
    const {isLoading, setLoading} = useLoading();

    const alertMessage: { type: AlertType, message: string } = {
        type: validCode
            ? 'success'
            : 'error',
        message: validCode
            ? 'Your account is now protected with two-factor authentication'
            : 'Your account is not protected with two-factor authentication'
    }

    const [form, setForm] = useState<{ code: string }>({
        code: '',
    });

    function handleSubmitCode(event: React.FormEvent<HTMLFormElement>) {
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

        setTimeout(() => {
            setLoading(false);
            setValidCode(form.code === '123456')
        }, 1200);
    }

    function handleDisable2FA() {
        setForm({
            code: ''
        })
        setValidCode(undefined);
    }

    if (validCode) {
        return <>
            <div
                className="self-stretch p-6 bg-[#1e1e1e] rounded-lg inline-flex flex-col justify-start items-center gap-8">
                <div
                    className="p-2 rounded-[64px] outline outline-2 outline-offset-[-2px] outline-teal-400 inline-flex justify-start items-center gap-2.5">
                    <div className="w-9 h-9 relative">
                        <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_10690_51140" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                                  y="0" width="36" height="36">
                                <rect width="36" height="36" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_10690_51140)">
                                <path
                                    d="M14.3254 27L5.77539 18.45L7.91289 16.3125L14.3254 22.725L28.0879 8.96252L30.2254 11.1L14.3254 27Z"
                                    fill="#2DD4BF"/>
                            </g>
                        </svg>

                    </div>
                </div>
                <div className="self-stretch flex flex-col justify-start items-start gap-4">
                    <div
                        className="self-stretch justify-start text-white text-xl font-medium font-['Roboto'] leading-loose">Two-Factor
                        Authentication is now enabled on your account
                    </div>
                    <div
                        className="self-stretch justify-start text-stone-400 text-base font-medium font-['Roboto'] leading-normal">For
                        added security, you’ll be prompted to enter a verification code from your authenticator app each
                        time you log in.
                    </div>
                </div>

                <Button onClick={handleDisable2FA} variant={'dark'} className="gap-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_6980_27154" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                              width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_6980_27154)">
                            <path
                                d="M6 22C5.45 22 4.97917 21.8042 4.5875 21.4125C4.19583 21.0208 4 20.55 4 20V10C4 9.45 4.19583 8.97917 4.5875 8.5875C4.97917 8.19583 5.45 8 6 8H7V6C7 4.61667 7.4875 3.4375 8.4625 2.4625C9.4375 1.4875 10.6167 1 12 1C13.3833 1 14.5625 1.4875 15.5375 2.4625C16.5125 3.4375 17 4.61667 17 6V8H18C18.55 8 19.0208 8.19583 19.4125 8.5875C19.8042 8.97917 20 9.45 20 10V20C20 20.55 19.8042 21.0208 19.4125 21.4125C19.0208 21.8042 18.55 22 18 22H6ZM12 17C12.55 17 13.0208 16.8042 13.4125 16.4125C13.8042 16.0208 14 15.55 14 15C14 14.45 13.8042 13.9792 13.4125 13.5875C13.0208 13.1958 12.55 13 12 13C11.45 13 10.9792 13.1958 10.5875 13.5875C10.1958 13.9792 10 14.45 10 15C10 15.55 10.1958 16.0208 10.5875 16.4125C10.9792 16.8042 11.45 17 12 17ZM9 8H15V6C15 5.16667 14.7083 4.45833 14.125 3.875C13.5417 3.29167 12.8333 3 12 3C11.1667 3 10.4583 3.29167 9.875 3.875C9.29167 4.45833 9 5.16667 9 6V8Z"
                                fill="white"/>
                        </g>
                    </svg>

                    <span className="hidden sm:block">
                        Disabled Two-Factor Authentication
                    </span>
                    <span className="block sm:hidden">
                        Disabled 2FA
                    </span>
                </Button>
            </div>
        </>
    }

    return (
        <div className="flex items-center h-full sm:h-auto">
            <div className="space-y-4 w-full">
                {validCode !== undefined && (
                    <Alert className="w-full text-mgt-dark"
                           type={alertMessage.type}
                           message={alertMessage.message}/>
                )}

                <div className="flex gap-2.5 text-white text-base font-medium leading-normal">
                    <div
                        className="px-3 py-0.5 bg-neutral-200 rounded-2xl inline-flex justify-center items-center gap-2.5">
                        <span
                            className="justify-start text-[#131210] text-sm font-bold font-['Roboto'] uppercase leading-normal">Step 1
                        </span>
                    </div>
                    <span className="text-xl">Scan QR code</span>
                </div>

                <div className="text-stone-400 text-base font-medium leading-normal">
                    Scan the QR code below or manually enter the secret key into your authentication app.
                </div>

                <div className="flex gap-4 p-2 bg-[#1e1e1e] rounded-lg">
                    <QRcode/>

                    <div className="space-y-4">
                        <div className="gap-y-1">
                            <div className="justify-start text-white text-xl font-medium leading-loose">
                                Can’t scan QR code?
                            </div>
                            <div
                                className="self-stretch justify-start text-stone-400 text-base font-medium font-['Roboto'] leading-normal">
                                Enter this secret instead:
                            </div>
                        </div>

                        <div
                            className="pl-3 pr-2 py-1 bg-white rounded outline outline-1 outline-offset-[-1px] outline-white inline-flex justify-center items-center gap-2">
                            <div
                                className="text-stone-800 text-sm font-medium uppercase items-center content-center flex">
                                <div className="mr-2">{defaultQRCode}</div>
                                <CopyButton defaultIcon={<>
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <mask id="mask0_10751_908" style={{maskType: 'alpha'}}
                                              maskUnits="userSpaceOnUse" x="0" y="0" width="20"
                                              height="20">
                                            <rect width="20" height="20" fill="#D9D9D9"/>
                                        </mask>
                                        <g mask="url(#mask0_10751_908)">
                                            <path
                                                d="M7.5 15C7.04167 15 6.64931 14.8368 6.32292 14.5104C5.99653 14.184 5.83333 13.7917 5.83333 13.3334V3.33335C5.83333 2.87502 5.99653 2.48266 6.32292 2.15627C6.64931 1.82988 7.04167 1.66669 7.5 1.66669H15C15.4583 1.66669 15.8507 1.82988 16.1771 2.15627C16.5035 2.48266 16.6667 2.87502 16.6667 3.33335V13.3334C16.6667 13.7917 16.5035 14.184 16.1771 14.5104C15.8507 14.8368 15.4583 15 15 15H7.5ZM4.16667 18.3334C3.70833 18.3334 3.31597 18.1702 2.98958 17.8438C2.66319 17.5174 2.5 17.125 2.5 16.6667V5.00002H4.16667V16.6667H13.3333V18.3334H4.16667Z"
                                                fill="black"/>
                                        </g>
                                    </svg>
                                </>} color={'black'} value={defaultQRCode}/>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="flex gap-2.5 text-white text-base font-medium leading-normal">
                    <div
                        className="px-3 py-0.5 bg-neutral-200 rounded-2xl inline-flex justify-center items-center gap-2.5">
                        <span
                            className="justify-start text-[#131210] text-sm font-bold font-['Roboto'] uppercase leading-normal">Step 2
                        </span>
                    </div>
                    <span className="text-xl">Get verification Code</span>
                </div>

                <div className="text-stone-400 text-base font-medium leading-normal">
                    Enter the 6-digit code you see in your authentication app.
                </div>

                <form onSubmit={handleSubmitCode} noValidate={true}
                      className="w-full mx-auto space-y-4">
                    <div>
                        <InputText
                            type="text"
                            placeholder="XXX XXX"
                            required={true}
                            disabled={isLoading}
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
                    </div>

                    <Button disabled={isLoading} type={'submit'} className="w-full">
                        SETUP 2FA
                    </Button>
                </form>
            </div>
        </div>
    );
}

export default TwoFactorAuthentication;