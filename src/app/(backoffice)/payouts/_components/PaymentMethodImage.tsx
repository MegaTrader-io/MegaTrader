import React from 'react';
import Image from "next/image";
import {PaymentMethod} from "@/commons/interfaces";

function PaymentMethodImage({paymentMethod}: { paymentMethod: PaymentMethod }) {
    if (!paymentMethod) {
        return null;
    }

    const url = `/assets/images/payment-method/${paymentMethod}.svg`;

    const imageSize = {
        width: paymentMethod === 'paypal' ? 91 : 24,
        height: 24
    }

    return <Image src={url} alt={paymentMethod} width={imageSize.width} height={imageSize.height}></Image>
}

export default PaymentMethodImage;