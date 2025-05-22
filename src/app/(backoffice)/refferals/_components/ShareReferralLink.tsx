'use client'

import React from 'react';
import InputText from "@/components/InputText";
import {CopyButton} from "@/components/CopyButton";

function ShareReferralLink() {
    const url = 'https://app.axcera.io/share';

    return (
        <div className="space-y-2">
            <h2
                className="text-white text-base font-bold leading-normal">
                Share the referral link
            </h2>
            <p className="text-stone-400 text-base font-normal  leading-normal">
                You can also share your referral link by copying and sending it to your friends or sharing it on social
                media.
            </p>

            <div className="flex gap-2 items-center">
                <InputText
                    readOnly={true}
                    value={url}
                    name={'share_link'}/>

                <CopyButton className="btn-dark-link rounded-xl p-3 w-12 h-12" value={url}/>
            </div>
        </div>
    );
}

export default ShareReferralLink;