import React, {useState} from 'react';
import QRcode from "@/components/QRcode";
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
import {useLoading} from "@/context/LoadingContext";
import Alert, {AlertType} from "@/components/Alert";

function TwoFactorAuthentication() {
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
    const [validCode, setValidCode] = useState<boolean | undefined>(undefined)
    const {setLoading} = useLoading();

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

    return (
        <div className="flex items-center h-full sm:h-auto">
            <div className="space-y-4">
                {validCode !== undefined && (
                    <Alert className="w-full text-mgt-dark"
                           type={alertMessage.type}
                           message={alertMessage.message}/>
                )}

                <div className="text-stone-400 text-base font-medium leading-normal">Scan the QR code below with an
                    authentication application, such as Google Authenticator, on your phone.
                </div>

                <div className="flex justify-center">
                    <QRcode/>
                </div>

                <div className="text-center space-y-4">
                    <div className="text-center text-stone-400 text-base font-medium  leading-normal">Or enter
                        the code below
                    </div>
                    <div
                        className="mx-auto h-7 px-3 bg-neutral-200 rounded-2xl justify-center items-center gap-2.5 inline-flex">
                        <div
                            className="text-[#131210] text-sm font-bold uppercase leading-normal">PHDNUETGDHNYRASBDF
                        </div>
                    </div>
                </div>

                <div
                    className="h-7 text-center text-stone-400 text-base font-medium font-['Roboto'] leading-normal">Type
                    the code created by the authenticator app.
                </div>

                <form onSubmit={handleSubmitCode} noValidate={true}
                      className="w-full mx-auto space-y-4">
                    <div>
                        <InputText
                            type="text"
                            placeholder="XXX XXX"
                            required={true}
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

                    <Button type={'submit'} className="w-full">
                        Set Up 2FA
                    </Button>
                </form>
            </div>
        </div>
    );
}

export default TwoFactorAuthentication;