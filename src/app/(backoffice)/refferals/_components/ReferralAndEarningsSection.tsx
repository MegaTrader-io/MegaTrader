import React from 'react';
import EarnWithMegatrader from "@/app/(backoffice)/refferals/_components/EarnWithMegatrader";
import InviteYourFriends from "@/app/(backoffice)/refferals/_components/InviteYourFriends";
import {IShowAlert} from "@/app/(backoffice)/refferals/page";

function ReferralAndEarningsSection({handleDisplayAlert}: { handleDisplayAlert: (payload: IShowAlert) => void }) {
    function displayMessage(response: { success: boolean, message: string }) {
        handleDisplayAlert({
            type: response.success ? 'success' : 'error',
            message: response.message
        })
    }

    return (
        <>
            <div className="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-12 lg:gap-4 w-full">
                <EarnWithMegatrader/>
                <InviteYourFriends
                    displayMessage={displayMessage}/>
            </div>
        </>
    );
}

export default ReferralAndEarningsSection;