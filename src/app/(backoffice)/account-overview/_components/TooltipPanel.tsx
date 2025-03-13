import React from "react";

export default function TooltipPanel({children, id}: { children: React.ReactNode, id?: string }) {
    return <div id={id} className="relative mt-[22px] group bg-card-onboarding">
        <div>
            {children}
        </div>
        <span className="text-[#0A0A0A] group-[.bg-card-onboarding]:text-[#1E1E1E]/80">
            <svg className="absolute w-[90px] h-3.5 -top-[13px] z-1" width="90" height="14" viewBox="0 0 90 14"
                 fill="none" xmlns="http://www.w3.org/2000/svg">
    <g filter="url(#filter0_i_4397_10335)">
        <path d="M77 0L90 14H64L77 0Z" fill="#0A0A0A"/>
    </g>
    <g filter="url(#filter1_i_4397_10335)">
        <path d="M77 0L90 14H64L77 0Z" fill="currentColor"/>
    </g>
    <defs>
        <filter id="filter0_i_4397_10335" x="64" y="0" width="26" height="14" filterUnits="userSpaceOnUse"
                colorInterpolationFilters="sRGB">
            <feFlood floodOpacity="0" result="BackgroundImageFix"/>
            <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                           result="hardAlpha"/>
            <feOffset dy="1"/>
            <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
            <feColorMatrix type="matrix" values="0 0 0 0 0.1176 0 0 0 0 0.1176 0 0 0 0 0.1176 0 0 0 1 0"/>
            <feBlend mode="normal" in2="shape" result="effect1_innerShadow_4397_10335"/>
        </filter>
        <filter id="filter1_i_4397_10335" x="64" y="0" width="26" height="14" filterUnits="userSpaceOnUse"
                colorInterpolationFilters="sRGB">
            <feFlood floodOpacity="0" result="BackgroundImageFix"/>
            <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                           result="hardAlpha"/>
            <feOffset dy="1"/>
            <feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/>
            <feColorMatrix type="matrix" values="0 0 0 0 0.1176 0 0 0 0 0.1176 0 0 0 0 0.1176 0 0 0 1 0"/>
            <feBlend mode="normal" in2="shape" result="effect1_innerShadow_4397_10335"/>
        </filter>
    </defs>
</svg>

        </span>

    </div>
}