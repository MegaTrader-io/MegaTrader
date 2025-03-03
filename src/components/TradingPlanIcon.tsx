import React from "react";
import Image from "next/image";

const iconMap: Record<string, string> = {
    megax: "/assets/images/trading-plans/lg/megaxIcon.svg",
    ninjatrader: "/assets/images/trading-plans/lg/ninjatraderIcon.svg",
    quantower: "/assets/images/trading-plans/lg/quantowerIcon.svg",
    tradovate: "/assets/images/trading-plans/lg/tradovateIcon.svg",
};

interface TradingPlanIconProps {
    tradingType: "megax" | "ninjatrader" | "quantower" | "tradovate";
    alt?: string;
    size?: number;
}

const TradingPlanIcon: React.FC<TradingPlanIconProps> = ({
                                                             tradingType,
                                                             alt = "Trading Platform Icon",
                                                             size = 40,
                                                         }) => {
    const iconSrc = iconMap[tradingType];

    if (!iconSrc) {
        console.error(`TradingPlanIcon: invalid "${tradingType}".`);
        return null;
    }

    return <Image src={iconSrc} alt={alt} width={size} height={size}/>;
};

export default TradingPlanIcon;
