'use client'

import React, {useEffect, useState} from 'react';
import clsx from "clsx";

export type directionType = 'asc' | 'desc'

function ArrowDown({direction = 'desc'}: { direction: directionType }) {
    const [rotate, setRotate] = useState('desc');

    useEffect(() => {
        setRotate(direction === 'asc' ? 'rotate-180' : '')
    }, [direction])

    return (
        <svg className={clsx(rotate)} width="24" height="24" viewBox="0 0 24 24" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_5635_21685" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect y="24" width="24" height="24" transform="rotate(-90 0 24)" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_5635_21685)">
                <path d="M18 10L12 16L6 10L7.4 8.6L12 13.2L16.6 8.6L18 10Z" fill="white"/>
            </g>
        </svg>
    );
}

export default ArrowDown;