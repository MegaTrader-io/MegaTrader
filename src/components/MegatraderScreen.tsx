'use client'
import Slider from "react-slick";
import "slick-carousel/slick/slick-theme.css";
import "../app/slick-theme.css";

import React, {useEffect, useRef} from "react";
import Image from "next/image";

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
                className="mx-auto relative xl:w-full overflow-hidden  max-w-[800px] lg:min-h-[869px] pt-[37px] pb-[64px]">
        <Slider {...settings}>
            <div className="h-full flex flex-col justify-center items-center">
                <h3 className="flex items-center justify-center md:h-[650px] lg:h-[766px]">
                    <Image src={'/assets/images/carousel/slide-1-tablet.svg'}
                           className="mx-[62px] h-full w-full block lg:hidden"
                           alt={'screenshot account overview'}
                           width={700}
                           height={650}
                           quality={100}
                    />

                    <Image src={'/assets/images/carousel/slide-1.svg'}
                           className="mx-[62px] h-full hidden lg:block"
                           alt={'screenshot account overview'}
                           width={800}
                           height={766}
                           quality={100}
                    />
                </h3>
            </div>
            <div className="h-full flex flex-col justify-center items-center">
                <h3 className="flex items-center justify-center md:h-[650px] lg:h-[766px]">
                    <Image src={'/assets/images/carousel/slide-2.svg'}
                           alt={'screenshot account overview'}
                           width={400}
                           height={424}
                           quality={100}
                    />
                </h3>
            </div>
            <div className="h-full flex justify-center items-center">
                <h3 className="flex items-center justify-center md:h-[650px] lg:h-[766px]">
                    <Image src={'/assets/images/carousel/slide-3.svg'}
                           alt={'screenshot account overview'}
                           width={506}
                           height={568}
                           quality={100}
                    />
                </h3>
            </div>
            <div className="h-full flex justify-center items-center">
                <h3 className="flex items-center justify-center md:h-[650px] lg:h-[766px]">
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