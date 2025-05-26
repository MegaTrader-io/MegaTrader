import React from 'react';
import {
    IRequestPayoutCrypto,
    PayoutSummary,
    IRequestPayoutRiseWorks, IRequestPayoutTransfer,
    PaymentMethodType
} from "@/commons/interfaces";
import {PayoutMethod} from "@/commons/data";
import PayoutTransaction from "@/app/(backoffice)/payouts/_components/payout_modal/_components/PayoutTransaction";

function FormRequest({methodTypeSelected, payload}: {
    methodTypeSelected: PaymentMethodType,
    payload: PayoutSummary | IRequestPayoutTransfer,

}) {
    if (methodTypeSelected === PayoutMethod.RISEWORKS) {
        const _payload = payload as IRequestPayoutRiseWorks;
        return <>
            <div
                className="w-full py-4 border-b border-neutral-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Email
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">{_payload.email}
                </div>
            </div>
            <PayoutTransaction
                withdrawalAmount={_payload.withdrawalAmount}
                transactionFee={_payload.transactionFee}
                netAmount={_payload.netAmount}
            />
        </>
    }

    if (methodTypeSelected === PayoutMethod.CRYPTO_BTC || methodTypeSelected === PayoutMethod.CRYPTO_ETH) {
        const _payload = payload as IRequestPayoutCrypto;

        return <>
            <div
                className="grid grid-cols-2 w-full py-4 border-b border-neutral-700 justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Wallet Address
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal truncate">{_payload.walletAddress}
                </div>
            </div>
            <PayoutTransaction
                withdrawalAmount={_payload.withdrawalAmount}
                transactionFee={_payload.transactionFee}
                netAmount={_payload.netAmount}
            />
        </>
    }

    if (methodTypeSelected === PayoutMethod.WIRE_ACH) {
        const _payload = payload as IRequestPayoutTransfer;
        return <>
            <div
                className="w-full py-4 border-b border-neutral-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Full
                    Name
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal capitalize">{_payload.fullName}
                </div>
            </div>


            {_payload.transferType === 'ACH' && (
                <div
                    className="w-full py-4 border-b border-neutral-700 inline-flex justify-between items-center">
                    <div
                        className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Routing
                        Number
                    </div>
                    <div
                        className="text-right justify-start text-base font-medium leading-normal">{_payload.routingNumber}
                    </div>
                </div>
            )}

            {_payload.transferType === 'WIRE' && (
                <div
                    className="w-full py-4 border-b border-neutral-700 inline-flex justify-between items-center">
                    <div
                        className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Fedwire
                        Routing number
                    </div>
                    <div
                        className="text-right justify-start text-base font-medium leading-normal">{_payload.fedwireRoutingNumber}
                    </div>
                </div>
            )}

            <div
                className="w-full py-4 border-b border-neutral-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Bank Name
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal capitalize">{_payload.bankName}
                </div>
            </div>

            <PayoutTransaction
                withdrawalAmount={_payload.withdrawalAmount}
                transactionFee={_payload.transactionFee}
                netAmount={_payload.netAmount}
            />

            <div
                className="w-full py-4 border-b border-neutral-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">Country
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">{_payload.country}
                </div>
            </div>

            <div
                className="grid grid-cols-2 w-full py-4 border-b border-neutral-700 justify-between items-center">
                <div
                    className="justify-start text-stone-400 text-base font-medium leading-normal">Address
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal capitalize">{_payload.recipientAddress}
                </div>
            </div>

            <div
                className="w-full py-4 border-b border-neutral-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">State
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">{_payload.state}
                </div>
            </div>

            <div
                className="w-full py-4 border-b border-neutral-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">City
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">{_payload.city}
                </div>
            </div>

            <div
                className="w-full py-4 border-b border-neutral-700 inline-flex justify-between items-center">
                <div
                    className="flex-1 justify-start text-stone-400 text-base font-medium leading-normal">ZIP Code
                </div>
                <div
                    className="text-right justify-start text-base font-medium leading-normal">${_payload.zipCode}
                </div>
            </div>
        </>
    }

    return null;
}

export default FormRequest;