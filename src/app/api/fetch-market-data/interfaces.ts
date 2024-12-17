export interface Meta {
    version: string;
    status: number;
    copywrite: string;
    symbol: string;
    processedTime: string;
}

export interface Quote {
    preMarketChange: number | null;
    preMarketChangePercent: number | null;
    preMarketPrice: number | null;
    preMarketTime: number | null;
    postMarketChange: number | null;
    postMarketChangePercent: number | null;
    postMarketPrice: number | null;
    postMarketTime: number | null;
    language: string;
    region: string;
    quoteType: string;
    typeDisp: string;
    quoteSourceName: string;
    triggerable: boolean;
    customPriceAlertConfidence: string;
    headSymbolAsString: string;
    contractSymbol: boolean;
    currency: string;
    exchange: string;
    exchangeTimezoneName: string;
    exchangeTimezoneShortName: string;
    underlyingSymbol: string;
    underlyingExchangeSymbol: string;
    gmtOffSetMilliseconds: number;
    market: string;
    esgPopulated: boolean;
    regularMarketChangePercent: number;
    regularMarketPrice: number;
    marketState: string;
    shortName: string;
    hasPrePostMarketData: boolean;
    firstTradeDateMilliseconds: number;
    priceHint: number;
    regularMarketChange: number;
    regularMarketTime: number;
    regularMarketDayHigh: number;
    regularMarketDayRange: string;
    regularMarketDayLow: number;
    regularMarketVolume: number;
    regularMarketPreviousClose: number;
    bid: number;
    ask: number;
    bidSize: number;
    askSize: number;
    fullExchangeName: string;
    regularMarketOpen: number;
    averageDailyVolume3Month: number;
    averageDailyVolume10Day: number;
    fiftyTwoWeekLowChange: number;
    fiftyTwoWeekLowChangePercent: number;
    fiftyTwoWeekRange: string;
    fiftyTwoWeekHighChange: number;
    fiftyTwoWeekHighChangePercent: number;
    fiftyTwoWeekLow: number;
    fiftyTwoWeekHigh: number;
    fiftyTwoWeekChangePercent: number;
    openInterest: number;
    expireDate: number;
    expireIsoDate: string;
    fiftyDayAverage: number;
    fiftyDayAverageChange: number;
    fiftyDayAverageChangePercent: number;
    twoHundredDayAverage: number;
    twoHundredDayAverageChange: number;
    twoHundredDayAverageChangePercent: number;
    sourceInterval: number;
    exchangeDataDelayedBy: number;
    tradeable: boolean;
    cryptoTradeable: boolean;
    symbol: string;
}

export interface ApiResponse {
    meta: Meta;
    body: Quote[];
}

export interface SymbolData {
    [key: string]: string;
}

export const SYMBOL_DATA: SymbolData = {
    "ES=F": "E-mini S&P 500",
    "MES=F": "Micro E-mini S&P 500",
    "NQ=F": "E-mini NASDAQ 100",
    "MNQ=F": "Micro E-mini NASDAQ 100",
    "RTY=F": "E-mini Russell 2000",
    "M2K=F": "Micro E-mini Russell 2000",
    "NKD=F": "Nikkei USD",
    "YM=F": "Mini-DOW",
    "MYM=F": "Micro Mini-DOW",
    "6A=F": "Australian Dollar",
    "M6A=F": "Micro AUD/USD",
    "6B=F": "British Pound",
    "M6B=F": "Micro GBP/USD",
    "6C=F": "Canadian Dollar",
    "6E=F": "Euro FX",
    "M6E=F": "Micro EUR/USD",
    "6J=F": "Japanese Yen",
    "6M=F": "Mexican Peso",
    "6N=F": "New Zealand Dollar",
    "6S=F": "Swiss Franc",
    "E7=F": "E-mini Euro FX",
    "CL=F": "Crude Oil",
    "QM=F": "E-mini Crude Oil",
    "MCL=F": "Micro Crude Oil",
    "NG=F": "Natural Gas",
    "QG=F": "E-mini Natural Gas",
    "MNG=F": "Micro Henry Hub Natural Gas",
    "HO=F": "Heating Oil",
    "RB=F": "RBOB Gasoline",
    "GC=F": "Gold",
    "MGC=F": "Micro Gold",
    "SI=F": "Silver",
    "SIL=F": "Micro Silver",
    "HG=F": "Copper",
    "MHG=F": "Micro Copper",
    "PL=F": "Platinum",
    "ZC=F": "Corn",
    "ZW=F": "Wheat",
    "ZS=F": "Soybeans",
    "ZL=F": "Soybean Oil",
    "ZM=F": "Soybean Meal",
    "HE=F": "Lean Hogs",
    "LE=F": "Live Cattle",
    "ZT=F": "2-Year Note",
    "ZF=F": "5-Year Note",
    "ZN=F": "10-Year Note",
    "TN=F": "10-Year Ultra-Note",
    "ZB=F": "30-Year Bond",
    "UB=F": "Ultra-Bond"
};