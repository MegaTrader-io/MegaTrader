'use client'
import Slider from "react-slick";
import "slick-carousel/slick/slick-theme.css";
import "../app/slick-theme.css";

import React, {useEffect, useRef} from "react";
import ScreenshotMg from "@/components/ScreenshotMg";
import Image from "next/image";

const MegatraderScreen = () => {
    const windowExampleRef = useRef<HTMLInputElement | null>(null);

    useEffect(() => {
        const handlerResize = () => {
            if (!windowExampleRef.current) {
                return;
            }

            const slickSlider = windowExampleRef.current.querySelector('.slick-slider') as HTMLDivElement;

            if (!slickSlider) {
                return;
            }

            windowExampleRef.current.style.setProperty('--height-slider', `${slickSlider.offsetHeight}px`);
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

    const settings = {
        dots: true,
        infinite: true,
        speed: 500,
        autoplay: false,
        autoplaySpeed: 3000,
        slidesToShow: 1,
        slidesToScroll: 1,
    };

    return <div ref={windowExampleRef}
                className="mx-auto relative xl:w-full overflow-hidden h-[calc(100%)]">
        <Slider {...settings}>
            <div className="text-white h-[var(--height-slider)] flex flex-col justify-center items-center w-full">
                <div className="flex flex-col justify-center w-full h-full">
                    <div className="block relative mx-auto w-[804px] h-[715px]">
                        <ScreenshotMg>
                            <Image
                                src="/assets/images/screenshots/account-overview.svg"
                                alt="Account overview"
                                width={684}
                                height={659}
                                style={{width: '100%', height: 'auto'}}
                                quality={100}
                            />
                        </ScreenshotMg>
                        <div className="absolute bottom-0 left-0">
                            <Image
                                src="/assets/images/screenshots/account-overview-small.svg"
                                alt="Account overview"
                                width={296}
                                height={420}
                                style={{width: '100%', height: 'auto'}}
                                quality={100}
                            />
                        </div>
                    </div>
                    <div className="space-y-2 mt-[44px] text-center">
                        <h3 className="h-[29px] text-center text-white text-xl font-medium uppercase leading-normal">
                            Power up your trading with full control.
                        </h3>
                        <p className="mx-auto text-stone-400 text-base font-medium leading-normal max-w-[500px] text-center">
                            Log in to manage your accounts, track performance, and unlock the full potential of your
                            MegaTrader journey.
                        </p>
                    </div>
                </div>
            </div>
            <div className="text-white h-[var(--height-slider)] flex flex-col justify-center items-center w-full">
                <div className="flex flex-col justify-center w-full h-full">
                    <div className="block relative mx-auto w-[804px] h-[715px]">
                        <ScreenshotMg>
                            <Image
                                src="/assets/images/screenshots/affiliates.svg"
                                alt="Affiliates"
                                width={684}
                                height={659}
                                style={{width: '100%', height: 'auto'}}
                                quality={100}
                            />
                        </ScreenshotMg>

                        <div className="absolute bottom-0  right-0">
                            <Image
                                src="/assets/images/screenshots/affilates-small.svg"
                                alt="Affiliates"
                                width={296}
                                height={420}
                                style={{width: '100%', height: 'auto'}}
                                quality={100}
                            />
                        </div>
                    </div>
                    <div className="space-y-2 mt-[44px] text-center">
                        <h3 className="h-[29px] text-center text-white text-xl font-medium uppercase leading-normal">
                            Track, Grow, and Earn with Confidence
                        </h3>
                        <p className="mx-auto text-stone-400 text-base font-medium leading-normal max-w-[500px] text-center">
                            Monitor your referrals, commissions, and performance using powerful tools designed to
                            maximize your affiliate success.
                        </p>
                    </div>
                </div>
            </div>
            <div className="text-white h-[var(--height-slider)] flex flex-col justify-center items-center w-full">
                <div className="flex flex-col justify-center w-full h-full">
                    <div className="block relative mx-auto w-[804px] h-[715px]">
                        <ScreenshotMg>
                            <Image
                                src="/assets/images/screenshots/payouts.svg"
                                alt="Affiliates"
                                width={684}
                                height={659}
                                style={{width: '100%', height: 'auto'}}
                                quality={100}
                            />
                        </ScreenshotMg>

                        <div className="absolute bottom-0">
                            <Image
                                src="/assets/images/screenshots/payouts-small.svg"
                                alt="Affiliates"
                                width={296}
                                height={420}
                                style={{width: '100%', height: 'auto'}}
                                quality={100}
                            />
                        </div>
                        <div className="absolute -bottom-2 right-[52px]">
                            <Image
                                src="/assets/images/screenshots/payouts-icons.svg"
                                alt="Affiliates"
                                width={160}
                                height={48}
                                style={{width: '100%', height: 'auto'}}
                                quality={100}
                            />
                        </div>
                    </div>
                    <div className="space-y-2 mt-[44px] text-center">
                        <h3 className="h-[29px] text-center text-white text-xl font-medium uppercase leading-normal">
                            Stay Fully in Control of Every Payout
                        </h3>
                        <p className="mx-auto text-stone-400 text-base font-medium leading-normal max-w-[500px] text-center">
                            View your earnings, check payment status, and stay in control of your withdrawals with
                            clear, real-time updates.
                        </p>
                    </div>
                </div>
            </div>
        </Slider>
    </div>
}

export default MegatraderScreen;