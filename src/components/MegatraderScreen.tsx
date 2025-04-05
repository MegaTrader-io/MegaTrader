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
            console.info(handlerResize);
        }

        const observer = new ResizeObserver(handlerResize);

        return () => {
            observer.disconnect();
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
            <div className="h-full flex flex-col justify-center items-center w-full">
                <h3 className="flex items-center justify-center w-full h-auto">
                    <ScreenshotMg/>
                </h3>
            </div>
            <div className="flex flex-col justify-center items-center">
                <h3 className="flex flex-col items-center justify-center h-auto">
                    <Image src={'/assets/images/carousel/slide-2.svg'}
                           alt={'screenshot account overview'}
                           width={400}
                           height={424}
                           quality={100}
                    />
                </h3>
            </div>
            <div className="flex justify-center items-center">
                <h3 className="flex flex-col items-center justify-center h-auto">
                    <Image src={'/assets/images/carousel/slide-3.svg'}
                           alt={'screenshot account overview'}
                           width={506}
                           height={568}
                           quality={100}
                    />
                </h3>
            </div>
            <div className="flex justify-center items-center">
                <h3 className="flex flex-col items-center justify-center h-auto">
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