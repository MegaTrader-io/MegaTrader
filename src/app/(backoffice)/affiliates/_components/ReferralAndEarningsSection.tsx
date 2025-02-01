import React from 'react';
import EarnWithMegatrader from "@/app/(backoffice)/affiliates/_components/EarnWithMegatrader";
import InviteYourFriends from "@/app/(backoffice)/affiliates/_components/InviteYourFriends";

function ReferralAndEarningsSection() {
    return (
        <div className="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-12 lg:gap-4 w-full">
            <EarnWithMegatrader/>
            <InviteYourFriends/>
        </div>
    );
}

export default ReferralAndEarningsSection;