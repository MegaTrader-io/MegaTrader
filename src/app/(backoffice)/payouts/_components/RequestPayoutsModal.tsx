import React, {useState} from 'react';
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
import Dialog from "@/components/Dialog";
import Alert from "@/components/Alert";
import clsx from "clsx";

export interface IRequestPayoutForm {
    amount: number | undefined,
    paymentMethodType: string,
    email: string | undefined,
    address: string | undefined,
    fullName: string | undefined,
}

interface IPaymentMethod {
    id: string,
    name: string
}

const PaymentMethodList: IPaymentMethod[] = [
    {id: 'riseworks', name: 'Riseworks'},
    {id: 'crypto_btc', name: 'Crypto - BTC'},
    {id: 'crypto_eth', name: 'Crypto - ETH'},
    {id: 'wire_ach', name: 'Wire / ACH'},
];

function IconPaymentMethod({value}: { value: string }) {
    return {
        'riseworks': <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_7736_28510" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_7736_28510)">
                <path
                    d="M16.1807 18L16.1325 10.512L8.66265 18H5L14.3976 8.64H10.9759C9.91566 8.64 9.04819 9.504 9.04819 10.56C9.04819 10.992 9.19277 11.424 9.43373 11.712L7.60241 13.536C6.87952 12.72 6.44578 11.664 6.44578 10.512C6.44578 8.016 8.46988 6 10.9759 6H18.7831V17.952L16.1807 18Z"
                    fill="currentColor"/>
            </g>
        </svg>,
        'crypto_btc': <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_7736_27298" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect x="0.5" width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_7736_27298)">
                <path
                    d="M17.9506 10.1091C18.1891 8.51238 16.9734 7.654 15.311 7.08138L15.8502 4.91838L14.5332 4.59025L14.0082 6.69625C13.6625 6.61 13.307 6.52863 12.9537 6.448L13.4825 4.32812L12.1666 4L11.627 6.16225C11.3405 6.097 11.0592 6.0325 10.7862 5.96463L10.7878 5.95788L8.972 5.5045L8.62175 6.91075C8.62175 6.91075 9.59863 7.13462 9.578 7.1485C10.1113 7.28163 10.208 7.6345 10.1915 7.91425L9.57725 10.3784C9.614 10.3878 9.66162 10.4012 9.71412 10.4222L9.57537 10.3878L8.714 13.8396C8.64875 14.0016 8.48338 14.2446 8.11063 14.1524C8.12375 14.1715 7.15363 13.9135 7.15363 13.9135L6.5 15.421L8.21375 15.8481C8.5325 15.928 8.84487 16.0116 9.152 16.0904L8.60713 18.2785L9.92225 18.6066L10.4622 16.4421C10.8211 16.5396 11.1699 16.6296 11.5111 16.7144L10.9734 18.8688L12.29 19.1969L12.8349 17.0133C15.08 17.4381 16.7686 17.2667 17.4785 15.2365C18.0511 13.6015 17.4504 12.6584 16.2691 12.043C17.1294 11.8439 17.7777 11.278 17.9506 10.1091ZM14.942 14.3275C14.5347 15.9625 11.7822 15.079 10.8894 14.857L11.6124 11.9586C12.5049 12.1814 15.3661 12.6224 14.942 14.3275ZM15.3489 10.0855C14.9776 11.5728 12.6864 10.8171 11.9427 10.6319L12.5982 8.00313C13.3419 8.18838 15.7359 8.53413 15.3489 10.0855Z"
                    fill="currentColor"/>
            </g>
        </svg>,
        'crypto_eth': <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_7736_27780" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_7736_27780)">
                <path fillRule="evenodd" clipRule="evenodd"
                      d="M11.9118 15.0515L7 12.1496L11.9118 4V15.0515ZM11.9118 15.0515V4L16.8217 12.1496L11.9118 15.0515ZM7 13.0802L11.9118 20V15.9821L7 13.0802ZM11.9118 15.9821L16.8255 13.0802L11.9118 20V15.9821Z"
                      fill="currentColor"/>
            </g>
        </svg>,
        'wire_ach': <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_7736_27984" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_7736_27984)">
                <path d="M5 17V10H7V17H5ZM11 17V10H13V17H11ZM2 21V19H22V21H2ZM17 17V10H19V17H17ZM2 8V6L12 1L22 6V8H2Z"
                      fill="currentColor"/>
            </g>
        </svg>
    }[value] || '---'
}

function TabButtonGroup({onClick, selection}: { onClick: (key: string) => void, selection: string }) {
    return <div className="flex justify-around items-center gap-2">
        {PaymentMethodList.map(option => (
            <Button key={option.id}
                    className="w-full !pl-3 flex justify-center gap-2 text-nowrap"
                    variant={option.id === selection ? "primary" : 'dark'}
                    onClick={() => {
                        onClick(option.id)
                    }}>
                <div className={clsx('w-6 h-6', {'text-black': option.id === selection})}>
                    <IconPaymentMethod value={option.id}/>
                </div>
                {option.name}
            </Button>
        ))}
    </div>
}

function RequestPayoutsModal({open, onClose, submitRequest}: {
    open: boolean,
    onClose: () => void,
    submitRequest: (form: IRequestPayoutForm) => void
}) {
    const [form, setForm] = useState<IRequestPayoutForm>({
        amount: undefined,
        email: undefined,
        address: undefined,
        fullName: undefined,
        paymentMethodType: 'riseworks'
    })
    const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});

    function changeFields(ev: React.ChangeEvent<HTMLInputElement>) {
        const {name, value} = ev.target;
        updateForm(name, value);
    }

    function updateForm(name: string, value: string) {
        setForm(prev => ({...prev, [name]: value}))
    }

    function validateFields() {
        const newErrors: { [key: string]: string } = {};
        if (!form.amount) newErrors.amount = "This field is required.";
        return newErrors;
    }

    function onSubmit(ev: React.FormEvent<HTMLFormElement>) {
        ev.preventDefault();

        const validationErrors = validateFields();

        if (Object.keys(validationErrors).length > 0) {
            setFieldErrors(validationErrors);
            return;
        }

        setFieldErrors({});
        submitRequest(form)
    }

    return (
        <Dialog showModal={open}
                childrenClassName={'max-h-dvh'}
                classNameOverlay={'bg-[#131210]'}
                className="w-[calc(100vw-32px)] sm:w-[700px]"
                title={'REQUEST PAYOUTS'}
                onClose={onClose}>
            {Object.keys(fieldErrors).length > 0 && (
                <Alert className={'text-black w-full mb-4'}
                       type={'error'}
                       message={'You must select, at lease, one of the options in Payment method'}/>
            )}

            <form onSubmit={onSubmit} className="text-white w-full space-y-4">
                <div className="space-y-8">
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Payment method
                            <TabButtonGroup selection={form.paymentMethodType}
                                            onClick={(value: string) => updateForm('paymentMethodType', value)}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal">
                            Enter the amount you wish to withdraw
                            <InputText type={"text"}
                                       name='amount'
                                       placeholder="100"
                                       defaultValue={form.amount}
                                       onChange={changeFields}
                                       errorMessage={fieldErrors.amount}/>
                        </label>
                        <span
                            className="self-stretch text-stone-400 justify-start text-sm font-medium leading-tight">Max
                        withdrawal: $150
                    </span>
                    </div>
                </div>


                <div>
                    <label className="text-stone-400 text-base font-bold leading-normal">
                        Email
                        <InputText type={"email"}
                                   name='email'
                                   defaultValue={form.email}
                                   onChange={changeFields}
                                   errorMessage={fieldErrors.email}/>
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

                <div className="!mt-4 space-y-4 sm:space-y-0 sm:flex justify-center gap-2">
                    <Button onClick={onClose}
                            className="w-full"
                            styleType={'text'}
                            variant={'light'}>
                        CANCEL
                    </Button>
                    <Button type='submit' className="w-full">
                        CONTINUE
                    </Button>
                </div>
            </form>
        </Dialog>
    );
}

export default RequestPayoutsModal;