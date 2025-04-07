'use client'
import Slider from "react-slick";
import "slick-carousel/slick/slick-theme.css";
import Image from 'next/image';
import "../app/slick-theme.css";

import React, {useEffect, useRef} from "react";
import ScreenshotMg from "@/components/ScreenshotMg";

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
        slidesToShow: 1,
        slidesToScroll: 1,
    };

    return <div ref={windowExampleRef}
                className="mx-auto relative xl:w-full overflow-hidden pb-[64px]">
        <Slider {...settings}>
            <div className="h-[var(--height-slider)] flex flex-col justify-center items-center w-full">
                <h3 className="flex items-center justify-center w-full h-full">
                    <ScreenshotMg/>
                </h3>
            </div>
            <div className="h-[var(--height-slider)] flex  flex-col justify-center items-center">
                <h3 className="flex flex-col items-center justify-center h-full">
                    <Image src={'/assets/images/carousel/slide-2.svg'}
                           alt={'screenshot account overview'}
                           width={400}
                           height={424}
                           quality={100}
                    />
                </h3>
            </div>
            <div className="h-[var(--height-slider)] flex justify-center items-center">
                <h3 className="flex flex-col items-center justify-center h-full">
                    <Image src={'/assets/images/carousel/slide-3.svg'}
                           alt={'screenshot account overview'}
                           width={506}
                           height={568}
                           quality={100}
                    />
                </h3>
            </div>
            <div className="h-[var(--height-slider)] flex justify-center items-center">
                <h3 className="flex flex-col items-center justify-center h-full">
                    <Image src={'/assets/images/carousel/slide-4.svg'}
                           alt={'screenshot account overview'}
                           width={600}
                           height={520}
                           quality={100}
                    />
                </h3>
            </div>
        </Slider>
    </div>
}

export default MegatraderScreen;