import React from "react";
import Image from "next/image";

const iconMap: Record<string, string> = {
    megax: "/assets/images/trading-plans/logo/megaxIcon.svg",
    ninjatrader: "/assets/images/trading-plans/logo/ninjatraderIcon.svg",
    quantower: "/assets/images/trading-plans/logo/quantowerIcon.svg",
    tradovate: "/assets/images/trading-plans/logo/tradovateIcon.svg",
};

const sizeMap: Record<string, { width: number, height: number }> = {
    megax: {
        width: 120,
        height: 36
    },
    ninjatrader: {
        width: 137,
        height: 20
    },
    quantower: {
        width: 170,
        height: 36
    },
    tradovate: {
        width: 133,
        height: 40
    },
};

interface TradingPlanIconProps {
    tradingType: "megax" | "ninjatrader" | "quantower" | "tradovate";
    className?: string,
    alt?: string;
    size?: number;
}

const TradingPlanIcon: React.FC<TradingPlanIconProps> = ({
                                                             tradingType,
                                                             className,
                                                             alt = "Trading Platform Logo"
                                                         }) => {
    const iconSrc = iconMap[tradingType];
    const size = sizeMap[tradingType];

    if (!iconSrc || !size) {
        console.error(`TradingPlanIcon: invalid "${tradingType}".`);
        return null;
    }

    return <Image className={className} src={iconSrc} alt={alt} width={size.width} height={size.height}/>;
};

export default TradingPlanIcon;
