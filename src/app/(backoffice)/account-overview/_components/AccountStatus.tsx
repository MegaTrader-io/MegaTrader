import React from 'react';
import Badge from "@/components/Badge";
import {AccountStatusType} from "@/commons/interfaces";

function AccountStatus({status}: { status: AccountStatusType }) {
    const variantColor: Record<AccountStatusType, "secondary" | "error" | "primary" | "info"> = {
        active: 'secondary',
        inactive: 'error',
        breach: 'primary',
    };

    return (
        <Badge shape="pill" variant={variantColor[status]}>{status}</Badge>
    );
}

export default AccountStatus;