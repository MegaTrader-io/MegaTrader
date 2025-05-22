import React from 'react';
import {AccountStatusType} from "@/commons/interfaces";
import clsx from "clsx";
import {BadgeSize} from "@/components/BaseBadge";

function AccountCircleStatus({status}: {
    className?: string,
    status: AccountStatusType,
    circleOnly?: boolean,
    size?: BadgeSize
}) {
    const variantColor: Record<AccountStatusType, "secondary" | "error" | "primary" | "info"> = {
        active: 'secondary',
        inactive: 'error',
    };

    const bgColor = variantColor[status] === 'error' ? 'bg-rose-500' : `bg-${variantColor[status]}`;


    return (
        <div className="relative flex justify-between items-center">
  <span
      className={clsx('absolute inline-flex rounded-full opacity-100', [bgColor])}></span>`
            <span className={clsx('relative inline-flex rounded-full size-3 border-2', [bgColor])}></span>
        </div>)
}

export default AccountCircleStatus;