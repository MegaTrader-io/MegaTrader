import React from "react";
import Image from "next/image";
import AccountCircleStatus from "@/app/(backoffice)/account-overview/_components/AccountCircleStatus";
import {AccountStatusType, TradingType} from "@/commons/interfaces";


const iconMap: Record<TradingType, string> = {
    megax: "/assets/images/trading-plans/md/projectX.svg",
    ninjatrader: "/assets/images/trading-plans/lg/ninjatraderIcon.svg",
    quantower: "/assets/images/trading-plans/lg/quantowerIcon.svg",
    tradovate: "/assets/images/trading-plans/lg/tradovateIcon.svg",
};

interface TradingPlanIconProps {
    tradingType: TradingType;
    status: AccountStatusType,
    alt?: string;
    size?: number;
}

const TradingPlanIcon: React.FC<TradingPlanIconProps> = ({
                                                             tradingType,
                                                             status,
                                                             alt = "Trading Platform Icon",
                                                             size = 40,
                                                         }) => {
    const iconSrc = iconMap[tradingType];

    if (!iconSrc) {
        console.error(`TradingPlanIcon: invalid "${tradingType}".`);
        return null;
    }

    return (<div className="relative">
        <Image src={iconSrc} alt={alt} width={size} height={size}/>
        <div className="absolute -bottom-2.5 right-0">
            <AccountCircleStatus status={status}/>
        </div>
    </div>)
};

export default TradingPlanIcon;
