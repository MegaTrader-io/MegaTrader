import React, {useEffect, useState} from "react";

import Card from "@/components/card";
import {ArrowDown, ArrowUp} from "@/components/arrows";
import {SymbolMarketData} from "@/commons/interfaces";

const changeValue = (value: number) => {
    const symbol = value > 0 ? "+" : "-";

    return `${symbol} $ ${Math.abs(value)}`;
};

const MarketOverviewSection = () => {
    const [data, setData] = useState<SymbolMarketData[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch("/api/fetch-market-data");
                if (!response.ok) {
                    throw new Error("Error al obtener los datos");
                }
                const result = await response.json() as SymbolMarketData[];

                console.info('result', result);
                setData(result);
            } catch (err: unknown) {
                const error = err as { message: string };
                setError(error.message);
            } finally {
                setLoading(false);
            }
        };

        void fetchData();
    }, []);

    if (loading) return <p>Cargando datos...</p>;
    if (error) return null;

    return <>
        <section>
            <div className="flex gap-3 overflow-x-auto scrollbar-hide">
                {[
                    {name: "E-mini S&P 500 (ES)", price: 18680.12, change: -405.53},
                    {name: "E-mini NASDAQ 100 (NQ)", price: 20394.16, change: 502.41},
                    {name: "Mini-DOW (YM)", price: 2568.12, change: 46.78},
                    {name: "OMXH30", price: 2509.99, change: 21.40},
                    {name: "OMXH25", price: 4407.14, change: 12.23},
                    {name: "NQUS", price: 3066.24, change: 30.12},
                    {name: "NQUS500LC", price: 3066.24, change: 30.12},
                ].map((instrument, index) => (
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
        </section>
    </>
}

export default MarketOverviewSection;
