import React from 'react';
import {AccountType as accountTypeBase} from "@/commons/interfaces";

function AccountPlanType({accountType}: { accountType: accountTypeBase }) {
    const description = {
        'basic_plan': '50K Elite Plan',
        'pro_plan': '100K Pro Plan',
        'premium_plan': '150K Premium Plan',
    }[accountType];

    return <div className="flex items-center gap-1">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_5990_5590" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_5990_5590)">
                <path
                    d="M9.2 8.25L11.85 3H12.15L14.8 8.25H9.2ZM11.25 20.1L2.625 9.75H11.25V20.1ZM12.75 20.1V9.75H21.375L12.75 20.1ZM16.45 8.25L13.85 3H19L21.625 8.25H16.45ZM2.375 8.25L5 3H10.15L7.55 8.25H2.375Z"
                    fill="white"/>
            </g>
        </svg>
        <div className="text-white text-base font-medium font-['Roboto'] leading-normal">{description}</div>
    </div>
}

export default AccountPlanType;