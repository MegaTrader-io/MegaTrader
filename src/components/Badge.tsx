import React from "react";
import {MgProps} from "@/commons/interfaces";


interface BadgeProps extends MgProps {
    size?: 'md' | 'sm'
}

const Badge = ({size = 'md', className = '', children, ...props}: BadgeProps) => {
    if (size === 'md') {
        return (
            <div  {...props}
                  className={`min-h-7 px-3 py-0.5 bg-teal-500 rounded-lg justify-center items-center gap-2.5 inline-flex ${className}`}>
                <div
                    className="text-[#131210] text-xs font-medium uppercase leading-normal">{children}</div>
            </div>
        )
    }

    return (
        <div  {...props}
              className={`h-4 px-2 bg-teal-500 rounded-xl justify-center items-center gap-2.5 inline-flex ${className}`}>
            <div
                className="text-[#131210] text-[10px] font-medium uppercase leading-none">{children}</div>
        </div>
    )
}

export default Badge;