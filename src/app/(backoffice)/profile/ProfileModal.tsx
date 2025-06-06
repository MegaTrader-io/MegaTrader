'use client';

import React, {useEffect} from 'react';
import Dialog from "@/components/Dialog";
import ProfileForm from "@/app/(backoffice)/profile/ProfileForm";

function ProfileModal() {
    const showModal: boolean = true;

    useEffect(() => {
        console.info('ProfileModal');
    }, []);

    function handleCloseDialog() {
        console.info('handleCloseDialog');
    }

    return (
        <Dialog
            className="w-[calc(100vw-32px)] sm:max-w-[1024px] px-4 py-8"
            childrenClassName="px-0 pb-0"
            showModal={showModal}
            onClose={handleCloseDialog}
            title={'MY PROFILE'}>
            <ProfileForm/>
        </Dialog>
    );
}

export default ProfileModal;