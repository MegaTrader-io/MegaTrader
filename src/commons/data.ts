import {Account, Period, TooltipData} from "@/commons/interfaces";

export const accounts: Account[] = [
    {
        id: 1,
        name: 'S1SEP2586479132',
        status: 'active',
        accountBalance: {
            currentBalance: "$145,166.78",
            currentEquity: "$145,166.78",
            high: "$150,000",
            low: "$145,166.78",
            weeklyNetPnL: "$0",
            bestDayPercentage: "-",
            bestDay: "-$73.40",
            worstDay: "-$4,524.54",
            avgWinningDay: "-",
            avgLosingDay: "-$1,610.74"
        }
    },
    {
        id: 2,
        name: 'SHYE36496NCHG33',
        status: 'inactive',
        accountBalance: {
            currentBalance: "$143,166.78",
            currentEquity: "$143,166.78",
            high: "$150,000",
            low: "$145,166.78",
            weeklyNetPnL: "$0",
            bestDayPercentage: "-",
            bestDay: "-$73.40",
            worstDay: "-$4,524.54",
            avgWinningDay: "-",
            avgLosingDay: "-$2,610.74"
        }
    },
    {
        id: 3,
        name: 'NJHA810003BGW33',
        status: 'breach',
        accountBalance: {
            currentBalance: "$143,166.78",
            currentEquity: "$143,166.78",
            high: "$150,000",
            low: "$145,166.78",
            weeklyNetPnL: "$0",
            bestDayPercentage: "-",
            bestDay: "-$73.40",
            worstDay: "-$4,524.54",
            avgWinningDay: "-",
            avgLosingDay: "-$2,610.74"
        }
    },
];

export const credentials = {
    login: 'pGd031d@hkh&Z~r1',
    password: 'pGd031d@hkh&Z~r1'
}

export const periods: Period[] = [
    {id: 'last_10_days', text: 'LAST 10 DAYS'},
    {id: 'last_30_days', text: 'LAST 30 DAYS'},
    {id: 'last_60_days', text: 'LAST 60 DAYS'},
]

export const tooltipData: TooltipData = {
    parameters: {
        startingBalance: "$50,000",
        maxPositionSize: "5",
        maxDrawdown: "$2,000",
    },
    accountDetails: {
        accountNumber: "9008713",
        platform: "Megatrader",
        username: "johndoe",
        password: "Same as your Megatrader account password",
    },
};
