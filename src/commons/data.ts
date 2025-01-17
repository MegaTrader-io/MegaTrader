import {Account, Period} from "@/commons/interfaces";

export const accounts: Account[] = [
    {
        id: 1,
        name: 'S1SEP2586479132',
        active: true,
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
        name: 'S1SEP2586479133',
        active: false,
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