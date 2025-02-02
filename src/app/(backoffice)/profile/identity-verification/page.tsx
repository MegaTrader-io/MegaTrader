'use client';

import React from 'react';
import Card from "@/components/Card";
import Image from "next/image";
import Link from "@/components/Link";

function Page() {
    const blocks = [
        {
            title: '100% SAFE',
            detail: 'Thanks to the most secure version of AES, your data is fully protected.',
            image: '/assets/images/government-secure.svg'
        },
        {
            title: 'FAST PROCESS',
            detail: 'Verification only takes a few minutes of process',
            image: '/assets/images/fast-process.svg'
        },
        {
            title: 'CONTRACT READY',
            detail: 'No more KYC on Funded Stage. Receive a pre-approved contract instantly.',
            image: '/assets/images/handshake-agreement.svg'
        },
    ]

    return (
        <>
            <Card className="w-full bg-primary text-white space-y-4">
                <div className="justify-start items-start gap-4 inline-flex">
                    {blocks.map(block => (
                        <div key={block.title}
                             className="text-[#131210] text-base font-normal uppercase leading-normal space-y-4">
                            <Image src={block.image} alt={block.title} width={64} height={64}></Image>
                            <h2 className="text-[#131210] text-xl font-medium">{block.title}</h2>
                            <p className="text-[#131210] text-base font-normal leading-normal">
                                {block.detail}
                            </p>
                        </div>
                    ))}
                </div>
                <div className="flex justify-between items-center">
                    <Link className="btn-dark-link" href={"#"}>
                        Get verified now
                    </Link>

                    <div>
                        <Image src={'/assets/images/veriff.svg'} alt="veriff" width={118} height={34}/>
                    </div>
                </div>
            </Card>
            <div className="mt-8 w-full">
                <div
                    className="text-white text-xl font-light uppercase leading-normal">Verification process
                </div>
            </div>
        </>
    );
}

export default Page;