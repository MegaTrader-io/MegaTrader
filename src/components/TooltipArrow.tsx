import React from 'react';
import * as BaseTooltip from "@radix-ui/react-tooltip";

function TooltipArrow() {
    return (
        <BaseTooltip.Arrow asChild>
            <svg width="16" height="10" viewBox="0 2 16 10"
                 className="absolute -top-[1px] -translate-x-1/2" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <g filter="url(#filter0_d_5037_5816)">
                    <path d="M8.5 8L16.5 0H0.5L8.5 8Z" fill="black"/>
                </g>
                <defs>
                    <filter id="filter0_d_5037_5816" x="0.5" y="0" width="16" height="10"
                            filterUnits="userSpaceOnUse" colorInterpolationFilters="sRGB">
                        <feFlood floodOpacity="0" result="BackgroundImageFix"/>
                        <feColorMatrix in="SourceAlpha" type="matrix"
                                       values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                       result="hardAlpha"/>
                        <feOffset dy="2"/>
                        <feComposite in2="hardAlpha" operator="out"/>
                        <feColorMatrix type="matrix"
                                       values="0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 1 0"/>
                        <feBlend mode="normal" in2="BackgroundImageFix"
                                 result="effect1_dropShadow_5037_5816"/>
                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_5037_5816"
                                 result="shape"/>
                    </filter>
                </defs>
            </svg>
        </BaseTooltip.Arrow>
    );
}

export default TooltipArrow;