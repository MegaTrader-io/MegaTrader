import React from "react";
import {MgProps} from "@/commons/interfaces";

const Badge = ({className = '', children, ...props}: MgProps) => {
    return (
        <div  {...props}
              className={`min-h-7 px-3 py-0.5 bg-teal-500 rounded-lg justify-center items-center gap-2.5 inline-flex ${className}`}>
            <div
                className="text-[#131210] text-xs font-medium font-['Space Grotesk'] uppercase leading-normal">{children}</div>
        </div>
    )
}

export default Badge;