'use client'
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

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
                className="mx-auto relative xl:w-full h-full overflow-hidden">
        <div
            className="relative"
        >
            <Slider {...settings}>
                <div>
                    <h3>
                        <Image src={'/assets/images/carousel/slide-1.svg'}
                               alt={'screenshot account overview'}
                               width={800}
                               height={766}
                               style={{width: '100%', height: 'auto'}}
                               quality={100}
                        />
                    </h3>
                </div>
                <div className="h-full flex justify-center items-center">
                    <h3 className="flex  items-center justify-center h-full">
                        <Image src={'/assets/images/carousel/slide-2.svg'}
                               alt={'screenshot account overview'}
                               width={400}
                               height={424}
                               quality={100}
                        />
                    </h3>
                </div>
                <div className="h-full flex justify-center items-center">
                    <h3 className="flex  items-center justify-center h-full">
                        <Image src={'/assets/images/carousel/slide-3.svg'}
                               alt={'screenshot account overview'}
                               width={506}
                               height={568}
                               quality={100}
                        />
                    </h3>
                </div>
                <div className="h-full flex justify-center items-center">
                    <h3 className="flex  items-center justify-center h-full">
                        <Image src={'/assets/images/carousel/slide-4.svg'}
                               alt={'screenshot account overview'}
                               width={600}
                               height={520}
                               style={{width: '100%', height: 'auto'}}
                               quality={100}
                        />
                    </h3>
                </div>
            </Slider>
        </div>
    </div>
}

export default MegatraderScreen;