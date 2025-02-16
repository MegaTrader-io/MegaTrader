import React from 'react';
import Badge from "@/components/Badge";
import {AccountStatusType} from "@/commons/interfaces";
import clsx from "clsx";

function AccountStatus({status, circleOnly = false}: { status: AccountStatusType, circleOnly?: boolean }) {
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
        <Badge shape="pill" variant={variantColor[status]}>{status}</Badge>
    );
}

export default AccountStatus;