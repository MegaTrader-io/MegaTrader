import React from 'react';
import Card, {CardTitle} from "@/components/Card";
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
import ShareReferralLink from "@/app/(backoffice)/affiliates/_components/ShareReferralLink";

function InviteYourFriends() {
    return (
        <Card className="w-full lg:col-span-5 p-4 text-white">
            <CardTitle className="mb-2">
                INVITE YOUR FRIENDS
            </CardTitle>
            <p className="text-stone-400 text-base font-normal  leading-normal">
                Add your friends email addresses and sent them invitations to join!
            </p>

            <div className="grid grid-rows-2 md:flex gap-2 my-4">
                <InputText
                    value={''}
                    placeholder={'Email addresses...'}
                    name={'email_referral'}/>
                <Button className="w-full md:w-auto">
                    SEND
                </Button>
            </div>

            <ShareReferralLink/>
        </Card>
    );
}

export default InviteYourFriends;