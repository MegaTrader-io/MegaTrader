import React from 'react';
import Badge from "@/components/Badge";
import {AccountStatusType} from "@/commons/interfaces";
import clsx from "clsx";
import {BadgeSize} from "@/components/BaseBadge";

function AccountStatus({status, className = '', circleOnly = false, size = 'md'}: {
    className?: string,
    status: AccountStatusType,
    circleOnly?: boolean,
    size?: BadgeSize
}) {
    const variantColor: Record<AccountStatusType, "secondary" | "error" | "primary" | "info"> = {
        active: 'secondary',
        unpaid: 'error',
        breach: 'primary',
        funded: 'secondary',
    };

    if (circleOnly) {
        const bgColor = variantColor[status] === 'error' ? 'bg-rose-500' : `bg-${variantColor[status]}`;
        return <div className={clsx('w-2 h-2 rounded-full', [bgColor])}></div>
    }

    return (
        <Badge shape="pill" className={className} size={size} variant={variantColor[status]}>{status}</Badge>
    );
}

export default AccountStatus;