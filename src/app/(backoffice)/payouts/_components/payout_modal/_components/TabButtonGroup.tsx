import {Button} from "@/components/Button";
import clsx from "clsx";
import IconPaymentMethod from "@/app/(backoffice)/payouts/_components/payout_modal/_components/IconPaymentMethod";
import React from "react";
import {PaymentMethodList} from "@/app/(backoffice)/payouts/_components/payout_modal/RequestPayoutsModal";

export default function TabButtonGroup({onClick, selection}: { onClick: (key: string) => void, selection: string }) {
    return <div className="grid grid-cols-2 sm:flex justify-around items-center gap-2">
        {PaymentMethodList.map(option => (
            <Button key={option.id}
                    className="w-full !pl-3 flex justify-center gap-2 text-nowrap"
                    variant={option.id === selection ? "primary" : 'dark'}
                    onClick={() => {
                        onClick(option.id)
                    }}>
                <div className={clsx('w-6 h-6', {'text-black': option.id === selection})}>
                    <IconPaymentMethod
                        value={option.id}/>
                </div>
                {option.name}
            </Button>
        ))}
    </div>
}
