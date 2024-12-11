import React from "react";
import '@/app/corner-wrapper.css';

export default function Card({children, className = ''}: {
    children: React.ReactNode,
    className?: string
}) {
    return <div className={`single-checkout-widget ${className}`}>
        {children || ''}
    </div>
}