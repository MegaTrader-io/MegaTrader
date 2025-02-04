'use client';

import React from 'react';
import Card from "@/components/Card";
import Image from "next/image";
import Link from "@/components/Link";

const blocks = [
    {
        title: '100% SAFE',
        detail: 'Thanks to the most secure version of AES, your data is fully protected.',
        image: 'government-secure.svg'
    },
    {
        title: 'FAST PROCESS',
        detail: 'Verification only takes a few minutes of process',
        image: 'fast-process.svg'
    },
    {
        title: 'CONTRACT READY',
        detail: 'No more KYC on Funded Stage. Receive a pre-approved contract instantly.',
        image: 'handshake-agreement.svg'
    },
]

const verificationProcesses = [
    {
        title: 'Check your account information',
        detail: 'Please check that your account information matches with your government-issued ID to avoid any inconveniences during verification. You will not be able to change this information afterward.',
        image: 'user-verified.svg'
    },
    {
        title: 'Prepare your physical ID cards',
        detail: 'You will be asked to take a photo of either your ID card, driving license, or other government-issued cards. Make sure you take a photo of your physical ID. Copies, screenshots, or other forms will be declined.',
        image: 'credit-card.svg'
    },
    {
        title: 'Begin the verification process',
        detail: 'Click the button bellow to start the verification process. You will be asked to take a photo of your ID and yourself.',
        image: 'camera.svg'
    },
    {
        title: 'Wait for confirmation',
        detail: 'You will be notified that your account has been verified. Usually it takes under a minute. All accounts passwords waiting for verification will be released immediately.',
        image: 'trophy.svg'
    },

];

function Page() {
    return (
        <>
            <Card className="w-full bg-primary text-white space-y-4">
                <div className="justify-start items-start space-y-4 md:space-y-0 md:gap-4 md:grid-cols-3 lg:grid-cols-1 md:grid xl:grid-cols-3">
                    {blocks.map(block => (
                        <div key={block.title}
                             className="space-y-4">
                            <Image src={`/assets/images/${block.image}`}
                                   alt={block.title}
                                   width={64}
                                   height={64}
                            />
                            <h2 className="text-[#131210] text-xl font-medium">{block.title}</h2>
                            <p className="text-[#131210] text-base font-normal leading-normal">
                                {block.detail}
                            </p>
                        </div>
                    ))}
                </div>
                <div className="space-y-3 md:space-y-0 md:flex justify-between items-center">
                    <Link className="btn-dark-link" href={"#"}>
                        Get verified now
                    </Link>

                    <Image className="mx-auto md:mx-0" src={'/assets/images/veriff.svg'} alt="veriff" width={118} height={34}/>
                </div>
            </Card>
            <div className="mt-8 w-full space-y-4">
                <div
                    className="text-white text-xl font-light uppercase leading-normal">Verification process
                </div>

                {verificationProcesses.map(vp => (
                    <div key={vp.title} className="flex gap-4">
                        <div>
                            <div className="rounded-full w-10 h-10 bg-[#1e1e1e] flex justify-center items-center">
                                <Image src={`/assets/images/${vp.image}`} alt="veriff" width={24} height={24}/>
                            </div>
                        </div>
                        <div>
                            <div className="text-white text-xl font-light leading-loose">
                                {vp.title}
                            </div>
                            <p
                                className="text-stone-400 text-base font-normal leading-normal">
                                {vp.detail}
                            </p>
                        </div>
                    </div>
                ))}

            </div>
        </>
    );
}

export default Page;