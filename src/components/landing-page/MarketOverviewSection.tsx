import React, {useEffect, useRef, useState} from "react";

import Card from "@/components/card";
import {ArrowDown, ArrowUp} from "@/components/arrows";
import {SymbolMarketData} from "@/commons/interfaces";

const changeValue = (value: number) => {
    const symbol = value > 0 ? "+" : "-";
    return `${symbol} $ ${Math.abs(value).toFixed(2)}`;
};

const SkeletonCards = () => {
    return <section>
        <div className="flex gap-3 overflow-x-auto scrollbar-hide">
            {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10].map(item => (
                <Card
                    key={item}
                    className="animate-pulse p-3 bg-[#1e1e1e]/70 rounded-2xl border border-transparent inline-table"
                >
                    <div className="grid grid-cols-[1fr_auto] gap-4 w-[278px] h-[48px]">
                        <div>
                            <h3 className="h-6  bg-slate-800/70 text-white text-base font-bold text-nowrap"></h3>
                            <p className="h-6  bg-slate-800/30 text-stone-400 font-normal"></p>
                        </div>

                        <div className="flex justify-center items-center text-nowrap">
                            <p
                                className={`flex gap-2 text-base font-bold`}
                            >
                                <span className="bg-slate-800/70 w-[75px] h-6">

                                </span>
                                <span className="bg-slate-800/70 rounded-full w-6 h-6">
                                </span>
                            </p>
                        </div>
                    </div>
                </Card>
            ))}
        </div>
    </section>
}

const MarketOverviewSection = () => {
    const carouselRef = useRef<HTMLDivElement>(null);
    const [data, setData] = useState<SymbolMarketData[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch("/api/fetch-market-data");
                if (!response.ok) {
                    throw new Error("error getting market data");
                }
                const result = await response.json() as SymbolMarketData[];
                setData(result.reverse());
            } catch (err: unknown) {
                const error = err as { message: string };
                setError(error.message);
            } finally {
                setLoading(false);
            }
        };

        void fetchData();

        if (carouselRef.current) {
            const carousel = carouselRef.current;
            const clone = carousel.innerHTML;
            carousel.innerHTML += clone;

            const totalWidth = Array.from(carousel.children).reduce((acc, el) => acc + el.clientWidth, 0) / 2;
            carousel.style.setProperty('--total-width', `${totalWidth}px`);
        }
    }, []);


    useEffect(() => {
        if (carouselRef.current && data.length > 0) {
            const carousel = carouselRef.current;
            const itemWidth = carousel.children[0].clientWidth;
            const totalWidth = itemWidth * data.length;

            carousel.style.setProperty('--item-width', `${itemWidth}px`);
            carousel.style.setProperty('--total-width', `${totalWidth}px`);

            // Clone items for infinite effect
            const clonedItemsBefore = Array.from(carousel.children).map(child => child.cloneNode(true));
            const clonedItemsAfter = Array.from(carousel.children).map(child => child.cloneNode(true));
            clonedItemsBefore.forEach(item => carousel.insertBefore(item, carousel.firstChild));
            clonedItemsAfter.forEach(item => carousel.appendChild(item));

            // Set initial scroll position
            carousel.scrollLeft = totalWidth;

            // Add scroll event listener
            const handleScroll = () => {
                if (carousel.scrollLeft <= totalWidth) {
                    carousel.scrollLeft = 2 * totalWidth;
                } else if (carousel.scrollLeft >= 3 * totalWidth) {
                    carousel.scrollLeft = 2 * totalWidth;
                }
            };

            carousel.addEventListener('scroll', handleScroll);

            return () => {
                carousel.removeEventListener('scroll', handleScroll);
            };
        }
    }, [data]);

    // useEffect(() => {
    //     if (carouselRef.current && data.length > 0) {
    //         const carousel = carouselRef.current;
    //         const itemWidth = carousel.children[0].clientWidth;
    //         const totalWidth = itemWidth * data.length;
    //
    //         carousel.style.setProperty('--item-width', `${itemWidth}px`);
    //         carousel.style.setProperty('--total-width', `${totalWidth}px`);
    //
    //         const clonedItems = Array.from(carousel.children).map(child => child.cloneNode(true));
    //         clonedItems.forEach(item => carousel.appendChild(item));
    //
    //         carousel.scrollLeft = totalWidth;
    //
    //         const handleScroll = () => {
    //             if (carousel.scrollLeft === 0) {
    //                 carousel.scrollLeft = totalWidth;
    //             } else if (carousel.scrollLeft === carousel.scrollWidth - carousel.clientWidth) {
    //                 carousel.scrollLeft = totalWidth;
    //             }
    //         };
    //
    //         carousel.addEventListener('scroll', handleScroll);
    //
    //         return () => {
    //             carousel.removeEventListener('scroll', handleScroll);
    //         };
    //     }
    // }, [data]);


    if (loading) return <>
        <SkeletonCards></SkeletonCards>
    </>;
    if (error) return null;

    return <>
        <section>
            <div className="flex gap-3 overflow-x-auto scrollbar-hide">
                <div ref={carouselRef} className="flex gap-4 animate-carousel">
                    {data.map((instrument, index) => (
                        <Card
                            key={index}
                            className=" p-3 bg-[#1e1e1e]/70 rounded-2xl border border-transparent inline-table"
                        >
                            <div className="grid grid-cols-[1fr_auto] gap-4">
                                <div>
                                    <h3 className="text-white text-base font-bold text-nowrap">{instrument.name}</h3>
                                    <p className="text-stone-400 font-normal">{instrument.price.toLocaleString()}</p>
                                </div>

                                <div className="flex justify-center items-center text-nowrap">
                                    <p
                                        className={`flex gap-2 text-base font-bold ${
                                            instrument.change > 0 ? "text-teal-400" : "text-rose-500"
                                        }`}
                                    >
                                        {changeValue(instrument.change)}
                                        {instrument.change > 0 && <ArrowUp/>}
                                        {instrument.change < 0 && <ArrowDown/>}
                                    </p>
                                </div>
                            </div>


                        </Card>
                    ))}
                </div>
            </div>
        </section>
    </>
}

export default MarketOverviewSection;
