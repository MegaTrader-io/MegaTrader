import React, {useState} from 'react';
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
import {MAX_WITHDRAWAL} from "@/commons/data";
import {calculateAmountToReceive} from "@/commons/utils";
import {IRequestPayoutTransfer} from "@/commons/interfaces";
import Select from "@/components/Select";

function TransferForm({showConfirmRequestDialog, onClose}: {
    showConfirmRequestDialog: (form: IRequestPayoutTransfer) => void,
    onClose: () => void
}) {
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
    const [form, setForm] = useState<IRequestPayoutTransfer>({
        transferType: 'ACH',
        fullName: '',
        fedwireRoutingNumber: '',
        bankName: '',
        routingNumber: '',
        accountNumber: '',
        accountType: 'checking',
        country: 'US',
        city: '',
        recipientAddress: '',
        state: '',
        zipCode: '',
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
        if (!form.fullName) newErrors.fullName = "This field is required.";
        if (!form.accountNumber) newErrors.accountNumber = "This field is required.";
        if (!form.accountType) newErrors.accountType = "This field is required.";
        if (!form.country) newErrors.country = "This field is required.";
        if (!form.city) newErrors.city = "This field is required.";
        if (!form.recipientAddress) newErrors.recipientAddress = "This field is required.";
        if (!form.state) newErrors.state = "This field is required.";
        if (!form.zipCode) newErrors.zipCode = "This field is required.";
        if (form.transferType === 'ACH' && !form.routingNumber) newErrors.routingNumber = "This field is required.";
        if (form.transferType === 'WIRE' && !form.fedwireRoutingNumber) newErrors.fedwireRoutingNumber = "This field is required.";

        if (form.withdrawalAmount && (form.withdrawalAmount <= 0 || form.withdrawalAmount > MAX_WITHDRAWAL)) newErrors.withdrawalAmount = "Invalid amount.";

        console.info('newErrors', newErrors);
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

    function changeOption(ev: React.ChangeEvent<HTMLSelectElement>) {
        const {name, value} = ev.target;
        updateForm(name, value);
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
                <Select onChange={changeOption} name="transferType">
                    {['ACH', 'WIRE'].map(option => (
                        <option key={option} value={option}>{option}</option>
                    ))}
                </Select>
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

            {form.transferType === 'ACH' && (
                <div>
                    <label className="text-stone-400 text-base font-bold leading-normal">
                        Routing numbers
                        <InputText type={"text"}
                                   name='routingNumber'
                                   defaultValue={form.routingNumber}
                                   onChange={changeFields}
                                   errorMessage={fieldErrors.routingNumber}/>
                    </label>
                </div>
            )}

            {form.transferType === 'WIRE' && (
                <div>
                    <label className="text-stone-400 text-base font-bold leading-normal">
                        Fedwire Routing number
                        <InputText type={"text"}
                                   name='fedwireRoutingNumber'
                                   defaultValue={form.fedwireRoutingNumber}
                                   onChange={changeFields}
                                   errorMessage={fieldErrors.fedwireRoutingNumber}/>
                    </label>
                </div>
            )}

            <div>
                <label className="text-stone-400 text-base font-bold leading-normal">
                    Account Number
                    <InputText type={"text"}
                               name='accountNumber'
                               defaultValue={form.accountNumber}
                               onChange={changeFields}
                               errorMessage={fieldErrors.accountNumber}/>
                </label>
            </div>

            <div>
                <label className="text-stone-400 text-base font-bold leading-normal">
                    Account Type
                    <Select onChange={changeOption} name="accountType" defaultValue={form.accountType}>
                        <option value="checking">Checking</option>
                    </Select>
                </label>
            </div>

            <div>
                <label className="text-stone-400 text-base font-bold leading-normal">
                    Country
                    <Select onChange={changeOption} name="country" defaultValue={form.country}>
                        <option value="US">United State</option>
                    </Select>
                </label>
            </div>

            <div>
                <label className="text-stone-400 text-base font-bold leading-normal">
                    City
                    <InputText type={"text"}
                               name='city'
                               defaultValue={form.city}
                               onChange={changeFields}
                               errorMessage={fieldErrors.city}/>
                </label>
            </div>

            <div>
                <label className="text-stone-400 text-base font-bold leading-normal">
                    Recipient Address
                    <InputText type={"text"}
                               name='recipientAddress'
                               defaultValue={form.recipientAddress}
                               onChange={changeFields}
                               errorMessage={fieldErrors.recipientAddress}/>
                </label>
            </div>

            <div>
                <label className="text-stone-400 text-base font-bold leading-normal">
                    State
                    <InputText type={"text"}
                               name='state'
                               defaultValue={form.state}
                               onChange={changeFields}
                               errorMessage={fieldErrors.state}/>
                </label>
            </div>

            <div>
                <label className="text-stone-400 text-base font-bold leading-normal">
                    ZIP Code
                    <InputText type={"text"}
                               name='zipCode'
                               defaultValue={form.zipCode}
                               onChange={changeFields}
                               errorMessage={fieldErrors.zipCode}/>
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

export default TransferForm;