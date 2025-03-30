import React, {useState} from 'react';
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
import {MAX_WITHDRAWAL} from "@/commons/data";
import {calculateAmountToReceive} from "@/commons/utils";
import {IRequestPayoutCryptoBTC} from "@/commons/interfaces";

function CryptoBTCForm({showConfirmRequestDialog, onClose}: {
    showConfirmRequestDialog: (form: IRequestPayoutCryptoBTC) => void,
    onClose: () => void
}) {
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});

    const [form, setForm] = useState<IRequestPayoutCryptoBTC>({
        fullName: undefined,
        walletAddress: undefined,
        address: undefined,
        withdrawalAmount: undefined,
        netAmount: 0,
        transactionFee: 0,
    })

    function updateForm(name: string, value: string | number) {
        setForm(prev => {
            const newData = {...prev, [name]: value}
            if (name === 'withdrawalAmount') {
                const {transactionFee, netAmount} = calculateAmountToReceive(Number(value));
                newData.netAmount = netAmount;
                newData.transactionFee = transactionFee;
            }

            return newData
        })
    }

    function validateFields() {
        const newErrors: { [key: string]: string } = {};
        if (!form.withdrawalAmount || !!form.withdrawalAmount && form.withdrawalAmount.toString().trim() === '') newErrors.withdrawalAmount = "This field is required.";
        if (!form.walletAddress) newErrors.walletAddress = "This field is required.";
        if (!form.address) newErrors.address = "This field is required.";
        if (!form.fullName) newErrors.fullName = "This field is required.";

        if (form.withdrawalAmount && (form.withdrawalAmount <= 0 || form.withdrawalAmount > MAX_WITHDRAWAL)) newErrors.withdrawalAmount = "Invalid amount.";

        return newErrors;
    }

    function changeFields(ev: React.ChangeEvent<HTMLInputElement>) {
        const {name, value} = ev.target;
        updateForm(name, value);
    }

    function onSubmit(ev: React.FormEvent<HTMLFormElement>) {
        ev.preventDefault();

        const validationErrors = validateFields();

        if (Object.keys(validationErrors).length > 0) {
            setFieldErrors(validationErrors);
            return;
        }

        setFieldErrors({});

        showConfirmRequestDialog(form)
    }

    return (
        <form noValidate={true} onSubmit={onSubmit} className="text-white w-full space-y-4">
            <div className="space-y-8">
                <div>
                    <label className="text-stone-400 text-base font-bold leading-normal">
                        Enter the amount you wish to withdraw
                        <InputText type={"text"}
                                   name='withdrawalAmount'
                                   placeholder="100"
                                   defaultValue={form.withdrawalAmount}
                                   onChange={changeFields}
                                   errorMessage={fieldErrors.withdrawalAmount}/>
                    </label>
                    <span
                        className="self-stretch text-stone-400 justify-start text-sm font-medium leading-tight">Max
                        withdrawal: $150
                    </span>
                </div>
            </div>

            <div>
                <label className="text-stone-400 text-base font-bold leading-normal">
                    Wallet Address
                    <InputText
                        type={"text"}
                        name='walletAddress'
                        defaultValue={form.walletAddress}
                        onChange={changeFields}
                        errorMessage={fieldErrors.walletAddress}/>
                </label>
            </div>

            <div>
                <label className="text-stone-400 text-base font-bold leading-normal">
                    Full Name or Business Name
                    <InputText type={"text"}
                               name='fullName'
                               defaultValue={form.fullName}
                               onChange={changeFields}
                               errorMessage={fieldErrors.fullName}/>
                </label>
            </div>

            <div>
                <label className="text-stone-400 text-base font-bold leading-normal">
                    Address
                    <InputText type={"text"}
                               name='address'
                               defaultValue={form.address}
                               onChange={changeFields}
                               errorMessage={fieldErrors.address}/>
                </label>
            </div>

            <div
                className="flex flex-col !mt-4 space-y-4 sm:space-y-0 sm:flex sm:flex-row justify-center gap-2">
                <Button onClick={onClose}
                        className="w-full order-2 sm:order-1"
                        styleType={'text'}
                        variant={'light'}>
                    CANCEL
                </Button>
                <Button type='submit' className="w-full order-1 sm:order-2">
                    CONTINUE
                </Button>
            </div>
        </form>
    );
}

export default CryptoBTCForm;