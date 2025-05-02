'use client'
import {Swiper, SwiperSlide} from 'swiper/react';
import {Pagination, Autoplay} from 'swiper/modules';
import 'swiper/swiper-bundle.css';
import 'swiper/css/pagination';
import '../app/swiper-pagination.css'

import React, {useRef} from "react";
import Image from "next/image";
import clsx from "clsx";


const sliders = [
    {
        imageUrl: '/assets/images/screenshots/ac2.svg',
        title: 'Power up your trading with full control.',
        subtitle: 'Log in to manage your accounts, track performance, and unlock the full potential of your MegaTrader journey.'
    },
    {
        imageUrl: '/assets/images/screenshots/affiliates2.svg',
        title: 'Track, Grow, and Earn with Confidence',
        subtitle: 'Monitor your referrals, commissions, and performance using powerful tools designed to maximize your affiliate success.'
    },
    {
        imageUrl: '/assets/images/screenshots/payouts2.svg',
        title: 'Stay Fully in Control of Every Payout',
        subtitle: 'View your earnings, check payment status, and stay in control of your withdrawals with clear, real-time updates.'
    },
]

const MegatraderScreen = () => {
    const windowExampleRef = useRef<HTMLInputElement | null>(null);

    return <div ref={windowExampleRef} className="windowExampleRef h-full">
        <Swiper autoplay={{
            delay: 3000,
            disableOnInteraction: false
        }}
                pagination={true}
                modules={[Pagination]}
                className="h-full pb-[32px]">
            {sliders.map(({imageUrl, title, subtitle}, index) => (
                <SwiperSlide key={index} className={'h-full'}>
                    <div className={clsx("text-white w-full h-full")}>
                        <div
                            className="flex-col w-full h-full xl:items-center 2xl:items-start xl:grid xl:grid-rows-[auto_128px]">
                            <Image
                                src={imageUrl}
                                className="size-3/4 md:max-h-[640px] xl:max-h-[calc(100dvh-60px-85px-32px-128px)]"
                                alt="Account overview"
                                width={684}
                                height={659}
                                style={{width: '100%', height: 'auto'}}
                                quality={100}
                            />
                            <div className="space-y-2 text-center relative md:top-[44px] xl:-top-[44px]">
                                <h3 className="h-[29px] text-center text-white text-xl font-medium uppercase leading-normal">
                                    {title}
                                </h3>
                                <p className="mx-auto text-stone-400 text-base font-medium leading-normal max-w-[500px] text-center">
                                    {subtitle}
                                </p>
                            </div>
                        </div>
                    </div>
                </SwiperSlide>
            ))}
        </Swiper>
    </div>
}

export default MegatraderScreen;