'use client';

import React, {useState} from 'react';
import Alert from "@/components/Alert";
import {Button} from "@/components/Button";
import TwoFacAuthenticationDialog
    from "@/app/(backoffice)/profile/two-factor-authentication/_components/TwoFacAuthenticationDialog";

function Page() {
    const [openRequestModal, setOpenRequestModal] = useState<boolean>(false);

    function toggleRequestModal() {
        setOpenRequestModal(prev => !prev);
    }

    return (
        <div className="space-y-8">
            <Alert className="w-full"
                   type={'error'}
                   message={'Your account is not protected with two-factor authentication'}/>

            {openRequestModal && (
                <TwoFacAuthenticationDialog open={openRequestModal} onClose={toggleRequestModal}/>
            )}

            <Button onClick={toggleRequestModal}>
                SET 2FA
            </Button>
        </div>
    );
}

export default Page;