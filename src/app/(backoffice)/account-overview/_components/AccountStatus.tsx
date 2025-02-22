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
        inactive: 'error',
    };

    if (circleOnly) {
        const bgColor = variantColor[status] === 'error' ? 'bg-rose-500' : `bg-${variantColor[status]}`;
        return <span className="relative flex justify-between items-center w-2 h-2">
  <span
      className={clsx('animate-ping absolute inline-flex h-full w-full rounded-full opacity-100', [bgColor])}></span>
  <span className={clsx('relative inline-flex rounded-full w-2 h-2', [bgColor])}></span>
</span>
    }

    return (
        <Badge shape="pill" className={className} size={size} variant={variantColor[status]}>{status}</Badge>
    );
}

export default AccountStatus;