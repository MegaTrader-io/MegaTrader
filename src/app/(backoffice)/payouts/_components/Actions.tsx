import React from 'react';
import {Button} from "@/components/Button";

function Actions() {
    return (
        <div className="flex w-full">
            <div className="flex gap-2 flex-1">
                <Button iconPosition={'left'} icon={<>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_5397_375" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                              width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_5397_375)">
                            <path
                                d="M6 22C5.45 22 4.97917 21.8042 4.5875 21.4125C4.19583 21.0208 4 20.55 4 20V4C4 3.45 4.19583 2.97917 4.5875 2.5875C4.97917 2.19583 5.45 2 6 2H14L20 8V20C20 20.55 19.8042 21.0208 19.4125 21.4125C19.0208 21.8042 18.55 22 18 22H6ZM13 9H18L13 4V9Z"
                                fill="white"/>
                        </g>
                    </svg>
                </>} variant={'dark'}>
                    DOCUMENTS
                </Button>
                <Button iconPosition={'left'} icon={<>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="mask0_5617_431" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                              width="24" height="24">
                            <rect width="24" height="24" fill="#D9D9D9"/>
                        </mask>
                        <g mask="url(#mask0_5617_431)">
                            <path
                                d="M5.0499 22.375L3.6499 20.95L6.5999 18H4.3499V16H9.9999V21.65H7.9999V19.425L5.0499 22.375ZM11.9999 22V14H3.9999V4C3.9999 3.45 4.19574 2.97917 4.5874 2.5875C4.97907 2.19583 5.4499 2 5.9999 2H13.9999L19.9999 8V20C19.9999 20.55 19.8041 21.0208 19.4124 21.4125C19.0207 21.8042 18.5499 22 17.9999 22H11.9999ZM12.9999 9H17.9999L12.9999 4V9Z"
                                fill="white"/>
                        </g>
                    </svg>
                </>} variant={'dark'}>
                    EXPORT
                </Button>
            </div>
            <Button className="items-end" iconPosition={'left'} icon={<>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_5617_785" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                          width="24" height="24">
                        <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_5617_785)">
                        <path
                            d="M5.1 16.05C4.73333 15.4167 4.45833 14.7667 4.275 14.1C4.09167 13.4333 4 12.75 4 12.05C4 9.81667 4.775 7.91667 6.325 6.35C7.875 4.78333 9.76667 4 12 4H12.175L10.575 2.4L11.975 1L15.975 5L11.975 9L10.575 7.6L12.175 6H12C10.3333 6 8.91667 6.5875 7.75 7.7625C6.58333 8.9375 6 10.3667 6 12.05C6 12.4833 6.05 12.9083 6.15 13.325C6.25 13.7417 6.4 14.15 6.6 14.55L5.1 16.05ZM12.025 23L8.025 19L12.025 15L13.425 16.4L11.825 18H12C13.6667 18 15.0833 17.4125 16.25 16.2375C17.4167 15.0625 18 13.6333 18 11.95C18 11.5167 17.95 11.0917 17.85 10.675C17.75 10.2583 17.6 9.85 17.4 9.45L18.9 7.95C19.2667 8.58333 19.5417 9.23333 19.725 9.9C19.9083 10.5667 20 11.25 20 11.95C20 14.1833 19.225 16.0833 17.675 17.65C16.125 19.2167 14.2333 20 12 20H11.825L13.425 21.6L12.025 23Z"
                            fill="black"/>
                    </g>
                </svg>
            </>} variant={'primary'}>
                TRANSACTIONS
            </Button>
        </div>
    );
}

export default Actions;