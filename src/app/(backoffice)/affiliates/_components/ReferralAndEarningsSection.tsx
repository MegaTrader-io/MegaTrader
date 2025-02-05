import React, {useState} from 'react';
import EarnWithMegatrader from "@/app/(backoffice)/affiliates/_components/EarnWithMegatrader";
import InviteYourFriends from "@/app/(backoffice)/affiliates/_components/InviteYourFriends";
import Alert from "@/components/Alert";

function ReferralAndEarningsSection() {
    const [response, setResponse] = useState<{ success: boolean; message: string } | null>(null);

    function displayMessage(response: { success: boolean, message: string }) {
        setResponse(response);
    }

    return (
        <>
            {response &&
                (<Alert type={response.success ? 'success' : 'error'}
                        message={response.success ? 'Your invitation has been sent successfully!' : 'An error occurred while sending your invitation. Please try again later.'}
                        className="w-full"/>)}

            <div className="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-12 lg:gap-4 w-full">
                <EarnWithMegatrader/>
                <InviteYourFriends
                    displayMessage={displayMessage}/>
            </div>
        </>
    );
}

export default ReferralAndEarningsSection;