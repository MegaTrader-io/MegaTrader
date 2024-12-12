import React from "react";
import '@/app/corner-wrapper.css';

export default function Card({children, className = ''}: {
    children: React.ReactNode,
    className?: string
}) {
    return <div className={`p-4  bg-[#1e1e1e]/70 border border-transparent rounded-2xl  ${className}`}>
        {children || ''}
    </div>
}