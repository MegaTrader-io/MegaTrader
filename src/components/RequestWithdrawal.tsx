import React, {useState} from 'react';
import Image from "next/image";
import {Button} from "@/components/Button";
import Card from "@/components/Card";
import {useLoading} from "@/context/LoadingContext";
import {IShowAlert} from "@/app/(backoffice)/refferals/page";
import RequestPayoutsModal from "@/app/(backoffice)/payouts/_components/payout_modal/RequestPayoutsModal";
import {IRequestPayoutForm, IRequestPayoutTransfer} from "@/commons/interfaces";

function RequestWithdrawal({handleDisplayAlert}: { handleDisplayAlert: (payload: IShowAlert) => void }) {
    const {setLoading} = useLoading();
    const [openRequestModal, setOpenRequestModal] = useState<boolean>(false);

    function toggleRequestModal() {
        setOpenRequestModal(prev => !prev);
    }

    function submitRequest(form: IRequestPayoutForm | IRequestPayoutTransfer) {
        setOpenRequestModal(false)
        setLoading(true);

        console.info('form', form);

        setTimeout(function () {
            const success = Number(form.withdrawalAmount) > 0 && Number(form.withdrawalAmount) <= 150;
            const message = success
                ? 'Your request has been submitted successfully. You\'ll be notified once it\'s approved.'
                : 'Something went wrong. Check your internet connection and try again later.';

            handleDisplayAlert({
                type: success ? 'success' : 'error',
                message: message
            })

            setLoading(false);
        }, 1200);
    }

    return (
        <>
            {openRequestModal &&
                <RequestPayoutsModal open={openRequestModal}
                                     onClose={toggleRequestModal}
                                     submitRequest={submitRequest}/>}

            <Card
                className="space-y-4 md:space-y-0 md:justify-start w-full md:items-center md:gap-4 md:inline-flex md:w-full">
                <div
                    className="space-y-4 md:space-y-0 md:grow md:shrink md:basis-0 md:h-6 md:justify-start md:items-center md:gap-4 md:flex md:w-full">
                    <div id="available-payment-methods"
                         className="text-white text-base font-medium leading-normal">Available Payment
                        Methods
                    </div>
                    <Image src='/assets/images/crypto-icons.svg' alt='icons' quality={100} width={88} height={24}/>
                </div>

                <Button id="request-withdrawal-button" onClick={toggleRequestModal} className="w-full md:w-auto">
                    REQUEST WITHDRAWAL
                </Button>
            </Card>
        </>
    );
}

export default RequestWithdrawal;