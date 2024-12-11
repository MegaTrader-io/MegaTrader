import React from "react";
import '@/app/corner-wrapper.css';

export default function Card({children}: {
    children: React.ReactNode,
    className: string,
    withCornerRightTop: boolean
}) {
    return <div className={`single-checkout-widget`}>
        {children}
    </div>
}