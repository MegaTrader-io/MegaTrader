'use client'
import Slider from "react-slick";
import "slick-carousel/slick/slick-theme.css";
import "../app/slick-theme.css";

import React, {useEffect, useRef} from "react";
import Image from "next/image";
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
            <div className="h-0 flex justify-center items-center">
                <h3 className="flex flex-col items-center justify-center">
                    slider 2
                </h3>
            </div>
            <div className="h-0 flex justify-center items-center">
                <h3 className="flex flex-col items-center justify-center">
                    slider 3
                </h3>
            </div>
            <div className="h-0 flex justify-center items-center">
                <h3 className="flex flex-col items-center justify-center">
                    slider 4
                </h3>
            </div>
        </Slider>
    </div>
}

export default MegatraderScreen;