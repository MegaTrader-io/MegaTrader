'use client'
import React, {useEffect, useState} from 'react';
import {Button} from "@/components/Button";
import clsx from "clsx";

function ToTopButton() {
    const [visible, setVisible] = useState(false)

    useEffect(() => {
        window.addEventListener('scroll', handleVisibilityChange);
        handleVisibilityChange()
    }, []);

    const handleVisibilityChange = () => {
        const scrolled = document.documentElement.scrollTop;
        console.info('handleVisibilityChange', scrolled > 160)
        setVisible(scrolled > 400)
    }

    function scrollToTop() {
        try {
            if (typeof window !== 'undefined') {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        } catch (error) {
            console.error('unable to scroll to the top:', error);
        }
    }

    return (
        <div className={clsx("sticky bottom-[5px] right-[5px] w-[300px] h-[300px]", {'block': visible, 'hidden': !visible})}>
            <Button
                onClick={scrollToTop} styleType={'filled'} variant={'light'}>
                <div className="flex gap-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_11468_2063" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                              y="0"
                              width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_11468_2063)">
                            <path d="M11 18V8.8L7.4 12.4L6 11L12 5L18 11L16.6 12.4L13 8.8V18H11Z" fill="black"/>
                        </g>
                    </svg>
                    <div>
                        BACK TO TOP
                    </div>
                </div>
            </Button>
        </div>
    );
}

export default ToTopButton;