import React from 'react';
import {getInitials} from "@/commons/utils";
import {IUser} from "@/commons/interfaces";

function Avatar({user}: { user: IUser }) {
    return (
        <div className="w-24 h-24 bg-primary rounded-full relative">
            <div
                className="flex h-full items-center justify-center text-[40px] text-black leading-[48px] font-light uppercase">
                {getInitials(user.fullName)}
            </div>

            <div className="absolute bottom-0 right-0">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <mask id="path-1-inside-1_5483_938" fill="white">
                        <path
                            d="M0 14C0 6.26801 6.26801 0 14 0C21.732 0 28 6.26801 28 14C28 21.732 21.732 28 14 28C6.26801 28 0 21.732 0 14Z"/>
                    </mask>
                    <path
                        d="M0 14C0 6.26801 6.26801 0 14 0C21.732 0 28 6.26801 28 14C28 21.732 21.732 28 14 28C6.26801 28 0 21.732 0 14Z"
                        fill="white"/>
                    <path
                        d="M14 27C6.8203 27 1 21.1797 1 14H-1C-1 22.2843 5.71573 29 14 29V27ZM27 14C27 21.1797 21.1797 27 14 27V29C22.2843 29 29 22.2843 29 14H27ZM14 1C21.1797 1 27 6.8203 27 14H29C29 5.71573 22.2843 -1 14 -1V1ZM14 -1C5.71573 -1 -1 5.71573 -1 14H1C1 6.8203 6.8203 1 14 1V-1Z"
                        fill="white" mask="url(#path-1-inside-1_5483_938)"/>
                    <mask id="mask0_5483_938" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="4"
                          y="4" width="20" height="20">
                        <rect x="4" y="4" width="20" height="20" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_5483_938)">
                        <path
                            d="M6.5 21.5V17.9583L17.5 6.97917C17.6667 6.82639 17.8507 6.70833 18.0521 6.625C18.2535 6.54167 18.4653 6.5 18.6875 6.5C18.9097 6.5 19.125 6.54167 19.3333 6.625C19.5417 6.70833 19.7222 6.83333 19.875 7L21.0208 8.16667C21.1875 8.31944 21.309 8.5 21.3854 8.70833C21.4618 8.91667 21.5 9.125 21.5 9.33333C21.5 9.55556 21.4618 9.76736 21.3854 9.96875C21.309 10.1701 21.1875 10.3542 21.0208 10.5208L10.0417 21.5H6.5ZM18.6667 10.5L19.8333 9.33333L18.6667 8.16667L17.5 9.33333L18.6667 10.5Z"
                            fill="black"/>
                    </g>
                </svg>
            </div>
        </div>
    );
}

export default Avatar;