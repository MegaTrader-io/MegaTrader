'use client'

import React from 'react';
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
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

            <div className="space-y-2 md:space-y-0 md:flex md:gap-2 my-4 items-center">
                <InputText
                    readOnly={true}
                    value={url}
                    name={'share_link'}/>

                <div className="flex gap-2">
                    <CopyButton className="btn-dark-link rounded-xl p-3 w-12 h-12" value={url}/>

                    <Button variant={'dark'} className="!p-3 w-12 h-12">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 2.25C6.61575 2.25 2.25 6.61575 2.25 12C2.25 16.8881 5.85113 20.9246 10.5424 21.6296V14.5837H8.13V12.021H10.5424V10.3155C10.5424 7.49213 11.9179 6.25312 14.2646 6.25312C15.3881 6.25312 15.9832 6.33675 16.2641 6.37425V8.61112H14.6636C13.6676 8.61112 13.3196 9.55612 13.3196 10.6204V12.021H16.239L15.8434 14.5837H13.3196V21.6499C18.0784 21.0049 21.75 16.9361 21.75 12C21.75 6.61575 17.3846 2.25 12 2.25Z"
                                fill="white"/>
                        </svg>
                    </Button>

                    <Button variant={'dark'} className="!p-3 w-12 h-12">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13.2465 10.4709L18.1423 4.78H16.9822L12.7312 9.72135L9.33594 4.78H5.41992L10.5542 12.2522L5.41992 18.22H6.58012L11.0693 13.0018L14.6549 18.22H18.5709L13.2465 10.4709ZM11.6575 12.318L11.1373 11.574L6.99816 5.65338H8.78017L12.1205 10.4315L12.6407 11.1755L16.9827 17.3863H15.2007L11.6575 12.318Z"
                                fill="white"/>
                        </svg>
                    </Button>
                </div>
            </div>
        </div>
    );
}

export default ShareReferralLink;