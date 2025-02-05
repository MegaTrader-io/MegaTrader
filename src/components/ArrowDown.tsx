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
            <mask id="mask0_4450_6166"
                  maskUnits="userSpaceOnUse" x="0" y="0" width="24" height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_4450_6166)">
                <path d="M12 15L7 10H17L12 15Z" fill="white"/>
            </g>
        </svg>
    );
}

export default ArrowDown;