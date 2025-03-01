import React from 'react';
import Image from "next/image";
import {PaymentMethod} from "@/commons/interfaces";

function PaymentMethodImage({paymentMethod}: { paymentMethod: PaymentMethod }) {
    if (!paymentMethod) {
        return null;
    }

    const url = `/assets/images/payment-method/${paymentMethod}.svg`;

    return <Image src={url} alt={paymentMethod} width={24} height={24}></Image>
}

export default PaymentMethodImage;