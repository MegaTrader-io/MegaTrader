'use client'
import {Swiper, SwiperSlide} from 'swiper/react';
import {Pagination, Autoplay} from 'swiper/modules';
import 'swiper/swiper-bundle.css';
import 'swiper/css/pagination';
import '../app/swiper-pagination.css'

import React from "react";
import Image from "next/image";

const sliders = [
    {
        imageUrl: '/assets/images/screenshots/slider1.svg',
        imageUrlMobile: '/assets/images/screenshots/ac2-mobile.svg',
        title: 'Power up your trading with full control.',
        subtitle: 'Log in to manage your accounts, track performance, and unlock the full potential of your MegaTrader journey.'
    },
    {
        imageUrl: '/assets/images/screenshots/affiliates3.svg',
        imageUrlMobile: '/assets/images/screenshots/affiliates3-mobile.png',
        title: 'Track, Grow, and Earn with Confidence',
        subtitle: 'Monitor your referrals, commissions, and performance using powerful tools designed to maximize your affiliate success.'
    },
    {
        imageUrl: '/assets/images/screenshots/payouts3.svg',
        imageUrlMobile: '/assets/images/screenshots/payouts3-mobile.svg',
        title: 'Stay Fully in Control of Every Payout',
        subtitle: 'View your earnings, check payment status, and stay in control of your withdrawals with clear, real-time updates.'
    },
]

const MegatraderScreen = () => <Swiper autoplay={{
    delay: 3000,
    disableOnInteraction: false
}}
                                       loop={true}
                                       pagination={true}
                                       modules={[Pagination, Autoplay]}>
    {sliders.map(({imageUrl, imageUrlMobile, title, subtitle}, index) => (
        <SwiperSlide key={index}>
            <div
                className="min-h-[668px] flex-col w-full h-full xl:items-center 2xl:items-start xl:grid xl:grid-rows-[auto_128px]">

                <Image
                    src={imageUrl}
                    className="hidden md:block size-3/4 md:max-h-[640px] xl:max-h-[calc(100dvh-60px-85px-32px-128px-32px)]"
                    alt="Account overview"
                    width={684}
                    height={659}
                    style={{objectFit: 'fill', width: '100%', height: '100%'}}
                    quality={100}
                />

                <Image
                    src={imageUrlMobile}
                    className="md:hidden w-full px-4"
                    alt="Account overview"
                    width={296}
                    height={420}
                    quality={100}
                />

                <div className="space-y-2 text-center mx-4 sm:mx-0 relative top-[44px]">
                    <h3 className="min-h-[29px] text-center text-white text-xl font-medium uppercase leading-normal">
                        {title}
                    </h3>
                    <p className="mx-auto text-stone-400 text-base font-medium leading-normal max-w-[500px] text-center">
                        {subtitle}
                    </p>
                </div>
            </div>
        </SwiperSlide>
    ))}
</Swiper>

export default MegatraderScreen;
