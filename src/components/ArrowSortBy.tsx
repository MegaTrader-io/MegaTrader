'use client'

import React, {useEffect, useState} from 'react';
import clsx from "clsx";

export type directionType = 'asc' | 'desc'

function ArrowSortBy({direction = 'desc'}: { direction: directionType, className?: string }) {
    const [rotate, setRotate] = useState('desc');

    useEffect(() => {
        setRotate(direction === 'asc' ? 'rotate-180' : '')
    }, [direction])

    return (
        <svg className={clsx(rotate)} width="25" height="24" viewBox="0 0 25 24" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_6399_21931" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                  width="25" height="24">
                <rect x="0.399902" width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_6399_21931)">
                <path d="M12.3999 15L7.3999 10H17.3999L12.3999 15Z" fill="white"/>
            </g>
        </svg>
    );
}

export default ArrowSortBy;