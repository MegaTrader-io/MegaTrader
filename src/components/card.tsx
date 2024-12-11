import React from "react";
import '@/app/corner-wrapper.css';

export default function Card({children, className = 'single-checkout-widget'}: {
    children: React.ReactNode,
    className?: string
}) {
    return <div className={` ${className}`}>
        {children || ''}
    </div>
}