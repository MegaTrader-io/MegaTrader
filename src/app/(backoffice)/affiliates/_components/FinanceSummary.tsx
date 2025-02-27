import React, {useState} from 'react';
import Card, {CardTitle} from "@/components/Card";
import {Button} from "@/components/Button";
import {useLoading} from "@/context/LoadingContext";
import RequestPayoutsModal, {IRequestPayoutForm} from "@/app/(backoffice)/payouts/_components/RequestPayoutsModal";
import {IShowAlert} from "@/app/(backoffice)/affiliates/page";

function FinanceSummary({handleDisplayAlert}: { handleDisplayAlert: (payload: IShowAlert) => void }) {
    const {setLoading} = useLoading();

    const [openRequestModal, setOpenRequestModal] = useState<boolean>(false);

    function toggleRequestModal() {
        setOpenRequestModal(prev => !prev);
    }

    function submitRequest(form: IRequestPayoutForm) {
        setOpenRequestModal(false)
        setLoading(true);

        console.info('form', form);

        setTimeout(function () {
            const fakeResponse = Number(form.amount) >= 100;

            const alertType = fakeResponse ? 'success' : 'error';
            const alertMessage = fakeResponse
                ? 'Your request has been submitted successfully. You\'ll be notified once it\'s approved.'
                : 'Something went wrong. Check your internet connection and try again later.';

            handleDisplayAlert({
                type: alertType,
                message: alertMessage
            })

            setLoading(false);
        }, 1200);
    }

    return (
        <>
            {openRequestModal &&
                <RequestPayoutsModal open={openRequestModal} onClose={toggleRequestModal}
                                     submitRequest={submitRequest}/>}
            <div className="w-full space-y-8">
                <div className="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-12 gap-4 w-full">
                    <Card className="col-span-1 md:col-span-4 lg:col-span-4 w-full bg-primary p-4 space-y-2">
                        <div className="flex justify-between">
                            <div className="text-[#131210] text-base font-bold leading-normal">
                                Total sold
                            </div>
                            <div className="text-[#131210] text-base font-bold leading-normal">$</div>
                        </div>
                        <div className="sm:flex sm:justify-between">
                            <div className="text-[#131210] text-[40px] font-lightuppercase leading-[48px]">
                                $5072,00
                            </div>
                            <Button onClick={toggleRequestModal} variant={'dark'}
                                    className="text-base w-full sm:w-auto">
                                REQUEST PAYOUT
                            </Button>
                        </div>
                    </Card>
                    <Card className="col-span-1 md:col-span-2 lg:col-span-4 w-full p-4 text-white">
                        <div className="text-white text-base font-bold leading-normal">
                            Total profit
                        </div>
                        <div className="text-white text-[40px] font-light uppercase leading-[48px]">
                            $399,00
                        </div>
                        <div className="text-stone-400 text-base font-normal leading-normal">
                            +$120 from last month
                        </div>
                    </Card>
                    <Card className="col-span-1 md:col-span-2 lg:col-span-4 w-full p-4 text-white">
                        <div className="text-white text-base font-bold leading-normal">
                            Total sold
                        </div>
                        <div className="text-white text-[40px] font-light uppercase leading-[48px]">
                            7
                        </div>
                        <div className="text-stone-400 text-base font-normal leading-normal">
                            +2 from last month
                        </div>
                    </Card>
                </div>
                <Card className="w-full p-4 text-white">
                    <CardTitle className="mb-4">
                        SUMMARY
                    </CardTitle>

                    <div className="space-y-4 md:space-y-0 md:grid md:grid-cols-3">
                        <div>
                            <h2 className="text-white text-base font-bold leading-normal">
                                Total Earnings
                            </h2>
                            <p
                                className="text-white text-[40px] font-light uppercase leading-[48px]">$5,471.00
                            </p>
                            <p className="text-teal-400 text-base font-normal leading-normal">
                                +12% from last month
                            </p>
                        </div>
                        <div>
                            <h2 className="text-white text-base font-bold leading-normal">
                                Conversion Rate
                            </h2>
                            <p
                                className="text-white text-[40px] font-light uppercase leading-[48px]">3.6%
                            </p>
                            <p className="text-rose-400 text-base font-normal leading-normal">
                                -0.8% from last month
                            </p>
                        </div>
                        <div>
                            <h2 className="text-white text-base font-bold leading-normal">
                                Active Referrals
                            </h2>
                            <p
                                className="text-white text-[40px] font-light uppercase leading-[48px]">3.6%
                            </p>
                            <p className="text-teal-400 text-base font-normal leading-normal">
                                +23 from last month
                            </p>
                        </div>
                    </div>
                </Card>
            </div>
        </>
    );
}

export default FinanceSummary;