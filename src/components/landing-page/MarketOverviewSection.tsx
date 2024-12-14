import Card from "@/components/card";
import {ArrowDown, ArrowUp} from "@/components/arrows";

export async function generateStaticParams() {
    try {
        const response = await fetch("http://localhost:3000/api/fetch-market-data", {
            cache: "no-store",
        });

        if (!response.ok) {
            throw new Error("Error fetching market data");
        }

        const data = await response.json();

        return {props: {data}};
    } catch (error) {
        console.error("Error fetching data:", error);
        return {props: {data: null, error: (error as Error).message}};
    }
}


const MarketOverviewSection = (props) => {

    console.info('props ?>> ', props);

    return <>
        <section className="my-10">
            <div className="flex gap-3 overflow-x-auto scrollbar-hide">
                {[
                    {name: "E-mini S&P 500 (ES)", value: 18680.12, change: "+ $405.53", positive: true},
                    {name: "E-mini NASDAQ 100 (NQ)", value: 20394.16, change: "+ $502.41", positive: true},
                    {name: "Mini-DOW (YM)", value: 2568.12, change: "- $46.78", positive: false},
                    {name: "OMXH30", value: 2509.99, change: "+ $21.40", positive: true},
                    {name: "OMXH25", value: 4407.14, change: "- $12.23", positive: false},
                    {name: "NQUS", value: 3066.24, change: "+ $30.12", positive: true},
                    {name: "NQUS500LC", value: 3066.24, change: "+ $30.12", positive: true},
                ].map((instrument, index) => (
                    <Card
                        key={index}
                        className=" p-3 bg-[#1e1e1e]/70 rounded-2xl border border-transparent inline-table"
                    >
                        <div className="grid grid-cols-[1fr_auto] gap-4">
                            <div>
                                <h3 className="text-white text-base font-bold text-nowrap">{instrument.name}</h3>
                                <p className="text-stone-400 font-normal">{instrument.value.toLocaleString()}</p>
                            </div>

                            <div className="flex justify-center items-center text-nowrap">
                                <p
                                    className={`flex gap-2 text-base font-bold ${
                                        instrument.positive ? "text-green-400" : "text-red-500"
                                    }`}
                                >
                                    {instrument.change}
                                    {instrument.positive && <ArrowUp/>}
                                    {!instrument.positive && <ArrowDown/>}
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
