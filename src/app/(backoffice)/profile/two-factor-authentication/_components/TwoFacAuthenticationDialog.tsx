import React, {useState} from 'react';
import Dialog from "@/components/Dialog";
import {Button} from "@/components/Button";
import InputText from "@/components/InputText";

function TwoFacAuthenticationDialog({open, onClose}: {
    open: boolean,
    onClose: () => void
}) {
    const [fieldErrors] = useState<Record<string, string>>({});
    const [form, setForm] = useState({
        code: '',
    });

    return (
        <Dialog showModal={open}
                childrenClassName={'max-h-dvh'}
                className="w-[calc(100vw-32px)] sm:w-[600px]"
                title={'ENABLE 2FA'}
                onClose={onClose}>
            <div className="flex items-center h-full sm:h-auto">
                <div className="space-y-8">
                    <div className="text-stone-400 text-base font-medium leading-normal">Scan the QR code below with an
                        authentication application, such as Google Authenticator, on your phone.
                    </div>

                    <div className="text-center text-stone-400 text-base font-medium  leading-normal">Or enter
                        the code below
                    </div>

                    <div className="text-center">
                        <div
                            className="mx-auto h-7 px-3 py-0.5 bg-neutral-200 rounded-2xl justify-center items-center gap-2.5 inline-flex">
                            <div
                                className="text-[#131210] text-sm font-bold uppercase leading-normal">PHDNUETGDHNYRASBDF
                            </div>
                        </div>
                    </div>

                    <div
                        className="h-7 text-center text-stone-400 text-base font-medium font-['Roboto'] leading-normal">Type
                        the code created by the authenticator app.
                    </div>

                    <form className="w-full sm:w-[300px] mx-auto space-y-4">
                        <div>
                            <InputText
                                type="text"
                                placeholder="XXX XXX"
                                name="code"
                                value={form.code}
                                onChange={(e) => {
                                    const value = e.target.value;
                                    setForm(prev => ({...prev, [e.target.name]: value}));
                                }}
                                errorMessage={fieldErrors.fullName}
                            />
                        </div>

                        <Button className="w-full">
                            CONTINUE
                        </Button>
                    </form>
                </div>
            </div>
        </Dialog>
    );
}

export default TwoFacAuthenticationDialog;