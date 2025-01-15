import React from "react";
import BaseBadge, {BadgeProps} from "@/components/BaseBadge";

const Badge = ({children, className = '', size = 'md', shape = "rounded", variant = "secondary"}: BadgeProps) => {
    return <BaseBadge shape={shape}
                      className={className}
                      variant={variant}
                      size={size}>
        {children}
    </BaseBadge>
}

export default Badge;