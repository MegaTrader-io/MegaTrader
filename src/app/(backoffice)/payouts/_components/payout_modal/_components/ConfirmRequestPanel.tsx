import React from 'react';
import {Button} from "@/components/Button";
import {RequestPayoutsType} from "@/app/(backoffice)/payouts/_components/payout_modal/RequestPayoutsModal";
import Alert from "@/components/Alert";

function ConfirmRequestPanel({confirmRequest, goBack}: {goBack: () => void, confirmRequest: RequestPayoutsType }) {
    return (
        <form className="text-white w-full">
            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Full
                    Name
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">John
                    Doe
                </div>
            </div>

            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Email
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">john@doe.com
                </div>
            </div>


            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Address
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">Dayne Port, 890 Franecki
                    Motorway Suite 297
                </div>
            </div>


            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Amount to
                    Withdraw
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">$150
                </div>
            </div>


            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Transaction Fee
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">$12
                </div>
            </div>

            <div
                className="w-full py-4 border-b border-Colors-Gray-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Amount to
                    Receive
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">$138
                </div>
            </div>

            <Alert type={'info'} className='my-4'
                   message={'Please verify your details before confirming. Incorrect information may cause payment delays.'}/>

            <div
                className="flex flex-col !mt-4 space-y-4 sm:space-y-0 sm:flex sm:flex-row justify-center gap-2">
                <Button onClick={goBack} className="w-full order-2 sm:order-1"
                        styleType={'text'}
                        variant={'light'}>
                    GO BACK
                </Button>
                <Button className="w-full order-1 sm:order-2">
                    CONFIRM REQUEST
                </Button>
            </div>
        </form>
    );
}

export default ConfirmRequestPanel;