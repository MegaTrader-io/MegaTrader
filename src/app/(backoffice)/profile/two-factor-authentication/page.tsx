'use client';

import React, {useState} from 'react';
import Alert, {AlertType} from "@/components/Alert";
import {Button} from "@/components/Button";
import TwoFacAuthenticationDialog
    from "@/app/(backoffice)/profile/two-factor-authentication/_components/TwoFacAuthenticationDialog";
import {useLoading} from "@/context/LoadingContext";

function Page() {
    const [openRequestModal, setOpenRequestModal] = useState<boolean>(false);
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

    function toggleRequestModal() {
        setOpenRequestModal(prev => !prev);
    }

    function submitCode(code: string) {
        setOpenRequestModal(false)
        setLoading(true);

        setTimeout(() => {
            setLoading(false);
            setValidCode(code === '123456')
        }, 1200);
    }

    return (
        <div className="space-y-8">
            {validCode !== undefined && (
                <Alert className="w-full"
                       type={alertMessage.type}
                       message={alertMessage.message}/>
            )}

            {openRequestModal && (
                <TwoFacAuthenticationDialog
                    open={openRequestModal}
                    onClose={toggleRequestModal}
                    submitCode={submitCode}/>
            )}

            <Button onClick={toggleRequestModal}>
                SET 2FA
            </Button>
        </div>
    );
}

export default Page;