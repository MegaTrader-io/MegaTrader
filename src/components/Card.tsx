import React, {PropsWithChildren} from "react";
import '@/app/corner-wrapper.css';
import clsx from "clsx";

interface PropsTitle extends PropsWithChildren {
    className?: string
}

export const CardTitle = ({children, className}: PropsTitle) => (
    <div className={clsx('text-white text-xl font-light uppercase leading-normal', className)}>
        {children}
    </div>)

export default function Card({children, className = ''}: {
    children?: React.ReactNode,
    className?: string
}) {
    return <div className={`p-4 bg-[#1e1e1e]/70 border border-transparent rounded-2xl  ${className}`}>
        {children || ''}
    </div>
}