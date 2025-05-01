'use client'
import Slider from "react-slick";
import "slick-carousel/slick/slick-theme.css";
import "../app/slick-theme.css";

import React, {useEffect, useRef} from "react";
import Image from "next/image";
import clsx from "clsx";

const settings = {
    dots: true,
    infinite: true,
    speed: 500,
    autoplay: false,
    autoplaySpeed: 3000,
    slidesToShow: 1,
    slidesToScroll: 1,
};

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

const SliderItem = ({imageUrl, className, title, subtitle}: {
    imageUrl: string,
    className: string,
    title: string,
    subtitle: string
}) => {
    return <div className={clsx("text-white w-full", className)}>
        <div className="flex flex-col items-center justify-center w-full h-full">
            <Image
                src={imageUrl}
                alt="Account overview"
                width={684}
                height={659}
                style={{width: '100%', height: 'auto'}}
                quality={100}
            />
            <div className="space-y-2 text-center  mt-[44px]">
                <h3 className="h-[29px] text-center text-white text-xl font-medium uppercase leading-normal">
                    {title}
                </h3>
                <p className="mx-auto text-stone-400 text-base font-medium leading-normal max-w-[500px] text-center">
                    {subtitle}
                </p>
            </div>
        </div>
    </div>
}

const MegatraderScreen = () => {
    const windowExampleRef = useRef<HTMLInputElement | null>(null);
    const sliderRef = useRef<Slider | null>(null);

    useEffect(() => {
        const handlerResize = () => {
            if (!windowExampleRef.current) {
                return;
            }

            const slickSlider = windowExampleRef.current.querySelector('.slick-slider') as HTMLDivElement;

            if (!slickSlider) {
                return;
            }

            const height = slickSlider.offsetHeight;

            console.info('height', height);
            // windowExampleRef.current.style.setProperty('--height-slider', `${height}px`);
        }

        const observer = new ResizeObserver(handlerResize);

        if (typeof window !== "undefined") {
            handlerResize();
            observer.observe(document.body);
            window.addEventListener("resize", handlerResize);
        }

        return () => {
            observer.disconnect();
            window.removeEventListener("resize", handlerResize);
        }
    }, []);

    useEffect(() => {
        if (!sliderRef.current) {
            return;
        }

        const observer = new IntersectionObserver(
            ([entry]) => {
                console.info('entry.isIntersecting', entry.isIntersecting);
            },
            {threshold: 0.1, root: windowExampleRef.current}
        );

        return () => {
            observer.disconnect();
        }
    }, [sliderRef.current, windowExampleRef.current]);


    return <div ref={windowExampleRef} className="windowExampleRef mx-auto relative xl:w-full">
        <Slider ref={sliderRef} {...settings}>
            {sliders.map((sliderItem, index) => (
                <SliderItem
                    key={index}
                    className={`slider-${index + 1}`}
                    imageUrl={sliderItem.imageUrl}
                    title={sliderItem.title}
                    subtitle={sliderItem.subtitle}
                />
            ))}
        </Slider>
    </div>
}

export default MegatraderScreen;