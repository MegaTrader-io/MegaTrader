import React from "react";

function CornerRightTop() {
    return <div className="absolute -top-[1px] -right-[1px]">
        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_i_3205_449)">
                <path
                    d="M5.64741 0H0V48H48V42.3526C48 39.7911 46.9814 37.338 45.1733 35.5299L12.4701 2.82672C10.662 1.01858 8.20894 0 5.64741 0Z"
                    fill="#1E1E1E" fillOpacity={0.7}/>
            </g>
            <defs>
                <filter id="filter0_i_3205_449" x="0" y="0" width="48" height="48" filterUnits="userSpaceOnUse"
                        colorInterpolationFilters={"sRGB"}>
                    <feFlood floodOpacity={0} result="BackgroundImageFix"/>
                    <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                   result="hardAlpha"/>
                    <feOffset dx="-1" dy="1"/>
                    <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
                    <feColorMatrix type="matrix" values="0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 1 0"/>
                    <feBlend mode="normal" in2="shape" result="effect1_innerShadow_3205_449"/>
                </filter>
            </defs>
        </svg>
    </div>
}

export default function Card({children, className = '', withCornerRightTop = false}: { children: React.ReactNode, className: string, withCornerRightTop: boolean }) {
    return <div className={`px-4 py-3 ${withCornerRightTop ?  'clip-custom-card' : ''} bg-[#1e1e1eb3] rounded-xl border border-neutral-700 ${className} relative`}>
        {withCornerRightTop && <CornerRightTop/>}
        {children}
    </div>
}