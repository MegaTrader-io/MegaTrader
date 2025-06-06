'use client';

import React from 'react';
import Card from "@/components/Card";
import Image from "next/image";
import Link from "@/components/Link";

const blocks = [
    {
        svg: function () {
            return <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_9280_47500" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                      width="24" height="24">
                    <rect width="24" height="24" fill="#D9D9D9"/>
                </mask>
                <g mask="url(#mask0_9280_47500)">
                    <path
                        d="M10.95 15.55L16.6 9.9L15.175 8.475L10.95 12.7L8.85 10.6L7.425 12.025L10.95 15.55ZM12 22C9.68333 21.4167 7.77083 20.0875 6.2625 18.0125C4.75417 15.9375 4 13.6333 4 11.1V5L12 2L20 5V11.1C20 13.6333 19.2458 15.9375 17.7375 18.0125C16.2292 20.0875 14.3167 21.4167 12 22Z"
                        fill="#FFB34A"/>
                </g>
            </svg>
        },
        title: '100% SAFE',
        detail: 'Your data is secured by AES-grade encryption.',
        image: 'government-secure.svg'
    },
    {
        svg: function () {
            return <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_9280_47506" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                      width="25" height="24">
                    <rect x="0.333252" width="24" height="24" fill="#D9D9D9"/>
                </mask>
                <g mask="url(#mask0_9280_47506)">
                    <path d="M8.33325 22L9.33325 15H4.33325L13.3333 2H15.3333L14.3333 10H20.3333L10.3333 22H8.33325Z"
                          fill="#FFB34A"/>
                </g>
            </svg>
        },
        title: 'Fast Process',
        detail: 'Identity check takes only a couple of minutes.',
        image: 'fast-process.svg'
    },
    {
        svg: function () {
            return <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <mask id="mask0_9280_47512" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                      width="25"
                      height="24">
                    <rect x="0.666504" width="24" height="24" fill="#D9D9D9"/>
                </mask>
                <g mask="url(#mask0_9280_47512)">
                    <path
                        d="M6.6665 20C5.5665 20 4.62484 19.6083 3.8415 18.825C3.05817 18.0417 2.6665 17.1 2.6665 16V8C2.6665 6.9 3.05817 5.95833 3.8415 5.175C4.62484 4.39167 5.5665 4 6.6665 4H18.6665C19.7665 4 20.7082 4.39167 21.4915 5.175C22.2748 5.95833 22.6665 6.9 22.6665 8V16C22.6665 17.1 22.2748 18.0417 21.4915 18.825C20.7082 19.6083 19.7665 20 18.6665 20H6.6665ZM6.6665 8H18.6665C19.0332 8 19.3832 8.04167 19.7165 8.125C20.0498 8.20833 20.3665 8.34167 20.6665 8.525V8C20.6665 7.45 20.4707 6.97917 20.079 6.5875C19.6873 6.19583 19.2165 6 18.6665 6H6.6665C6.1165 6 5.64567 6.19583 5.254 6.5875C4.86234 6.97917 4.6665 7.45 4.6665 8V8.525C4.9665 8.34167 5.28317 8.20833 5.6165 8.125C5.94984 8.04167 6.29984 8 6.6665 8ZM4.8165 11.25L15.9415 13.95C16.0915 13.9833 16.2415 13.9833 16.3915 13.95C16.5415 13.9167 16.6832 13.85 16.8165 13.75L20.2915 10.85C20.1082 10.6 19.8748 10.3958 19.5915 10.2375C19.3082 10.0792 18.9998 10 18.6665 10H6.6665C6.23317 10 5.854 10.1125 5.529 10.3375C5.204 10.5625 4.9665 10.8667 4.8165 11.25Z"
                        fill="#FFB34A"/>
                </g>
            </svg>
        },
        title: 'Contract Ready',
        detail: 'Skip KYC steps. Get your contract right away',
        image: 'handshake-agreement.svg'
    },
]

const verificationProcesses = [
    {
        title: 'CHECK YOUR ACCOUNT INFORMATION',
        detail: 'Please check that your account information matches with your government-issued ID to avoid any inconveniences during verification. You will not be able to change this information afterward.',
        image: 'user-verified.svg'
    },
    {
        title: 'PREPARE YOUR PHYSICAL ID CARDS',
        detail: 'You will be asked to take a photo of either your ID card, driving license, or other government-issued cards. Make sure you take a photo of your physical ID. Copies, screenshots, or other forms will be declined.',
        image: 'credit-card.svg'
    },
    {
        title: 'BEGIN THE VERIFICATION PROCESS',
        detail: 'Click the button bellow to start the verification process. You will be asked to take a photo of your ID and yourself.',
        image: 'camera.svg'
    },
    {
        title: 'WAIT FOR CONFIRMATION',
        detail: 'You will be notified that your account has been verified. Usually it takes under a minute. All accounts passwords waiting for verification will be released immediately.',
        image: 'trophy.svg'
    },

];

function Page() {
    return (
        <>
            <Card className="w-full bg-mgt-dark text-white space-y-4">
                <div
                    className="justify-start items-start space-y-4 sm:space-y-0 md:gap-4 md:grid-cols-3 md:grid lg:grid-cols-3">
                    {blocks.map(block => (
                        <div key={block.title}
                             className="space-y-4">
                            <div className="flex gap-1">
                                {block.svg()}
                                <span className="text-white text-base font-bold">{block.title}</span>
                            </div>
                            <p className="text-stone-400 text-base font-medium leading-normal tracking-tight">
                                {block.detail}
                            </p>
                        </div>
                    ))}
                </div>
                <div className="space-y-3 md:space-y-0 md:flex justify-between items-center">
                    <Link className="btn-dark-link" href={"#"}>
                        GET VERIFIED NOW
                    </Link>

                    <Image className="mx-auto md:mx-0 fill-stone-400" src={'/assets/images/veriff.svg'} alt="veriff"
                           width={118}
                           height={33}/>
                </div>
            </Card>
            <div className="mt-8 w-full space-y-6">
                <div
                    className="justify-start text-white text-xl font-light font-['Roboto'] uppercase leading-normal">Verification
                    process
                </div>

                <div className="space-y-6 relative wrapper-vertical-line">
                    {verificationProcesses.map(vp => (
                        <div key={vp.title} className="flex gap-4 vertical-line">
                            <div>
                                <div
                                    className="rounded-full w-10 h-10 bg-[#1e1e1e] outline outline-4 outline-neutral-700 flex justify-center items-center">
                                    <Image src={`/assets/images/${vp.image}`} alt="veriff" width={24} height={24}/>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <div className="text-white text-xl font-medium uppercase leading-normal">
                                    {vp.title}
                                </div>
                                <p
                                    className="text-stone-400 text-base font-medium leading-normal">
                                    {vp.detail}
                                </p>
                            </div>
                        </div>
                    ))}
                </div>


            </div>
        </>
    );
}

export default Page;