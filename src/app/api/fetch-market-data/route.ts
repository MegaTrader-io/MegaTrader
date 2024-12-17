import NodeCache from "node-cache";
import {ApiResponse, SYMBOL_DATA} from "@/app/api/fetch-market-data/interfaces";

const cache = new NodeCache({stdTTL: 300});

interface SymbolMarketData {
    name: string,
    price: number,
    change: number
};

export async function GET() {
    const cacheKey = "marketData";

    let body: SymbolMarketData[] = [];
    const data: SymbolMarketData[] = cache.get(cacheKey) as SymbolMarketData[];

    if (!data) {
        try {
            const response = await fetch(
                "https://yahoo-finance15.p.rapidapi.com/api/v1/markets/stock/quotes?ticker=ES=F,MES=F,NQ=F,MNQ=F,RTY=F,M2K=F,NKD=F,YM=F,MYM=F,6A=F,M6A=F,6B=F,M6B=F,6C=F,6E=F,M6E=F,6J=F,6M=F,6N=F,6S=F,E7=F,CL=F,QM=F,MCL=F,NG=F,QG=F,MNG=F,HO=F,RB=F,GC=F,MGC=F,SI=F,SIL=F,HG=F,MHG=F,PL=F,ZC=F,ZW=F,ZS=F,ZL=F,ZM=F,HE=F,LE=F,ZT=F,ZF=F,ZN=F,TN=F,ZB=F,UB=F",
                {
                    method: "GET",
                    headers: {
                        "X-Rapidapi-Key": "da16701ec3msh8a73fda62b6dc70p1645d7jsn0d0e0d8bdca0",
                        "X-Rapidapi-Host": "yahoo-finance15.p.rapidapi.com",
                        Cookie: "__cflb=02DiuDPiBzc7r3SEPt3hbb7G3S2VSWX6BEN69eKLuY3He",
                    },
                }
            );

            if (!response.ok) {
                throw new Error(`Error al obtener datos: ${response.statusText}`);
            }

            const {body: bodyData} = await response.json() as ApiResponse || {body: []};

            body = bodyData.map(({regularMarketChange, regularMarketPrice, symbol}: {
                regularMarketChange: number,
                regularMarketPrice: number,
                symbol: string
            }) => {
                const label = SYMBOL_DATA[symbol] || 'unknown';
                const symbolModified = symbol.split('=').at(0);

                return {
                    name: `${label} (${symbolModified})`,
                    change: regularMarketChange,
                    price: regularMarketPrice
                };
            }) as SymbolMarketData[];

            cache.set(cacheKey, body);
        } catch (error) {
            return new Response(
                JSON.stringify({error: (error as Error).message}),
                {status: 500}
            );
        }
    } else {
        body = data;
    }

    return new Response(JSON.stringify(body), {status: 200, headers: {"Content-Type": "application/json"}});
}
