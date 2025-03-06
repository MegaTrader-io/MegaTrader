import {Account, IUser, Metrics, Period, TooltipData} from "@/commons/interfaces";
import {formatCurrency, getPlanDetail} from "@/commons/utils";
import {ApexOptions} from "apexcharts";

export const accounts: Account[] = [
    {
        id: 1,
        name: 'S1SEP2586479132DSDS8',
        planDetail: getPlanDetail('elite', '50K'),
        tradingType: 'quantower',
        status: 'inactive',
        overallPerformance: {
            currentBalance: 47850.30,
            totalProfit: {
                value: -1149.70,
                percentage: -4.30
            },
            tradingDays: 4,
            dailyLossLimit: 2000,
            currentEquity: 47850.30,
            weeklyNetPnL: -1560.40,
        },
        objectives: {
            profitTarget: {
                target: -2149.70,
                value: 3000,
                percentage: 0,
                pass: false
            },
            daysTraded: {
                target: 1,
                value: 4,
                percentage: 0,
                pass: false
            },
            rule: {
                maximumLossLimit: {
                    pass: false,
                    description: "Keep your Account Balance above $48,000"
                }
            },
        }
    },
    {
        id: 2,
        name: 'S1SEP2586479132DSDS9',
        planDetail: getPlanDetail('growth', '100K'),
        status: 'active',
        tradingType: 'ninjatrader',
        overallPerformance: {
            currentBalance: 103540.80,
            totalProfit: {
                value: 3549.80,
                percentage: 3.54
            },
            tradingDays: 3,
            dailyLossLimit: 2500,
            currentEquity: 103540.80,
            weeklyNetPnL: 1720.30
        },
        objectives: {
            profitTarget: {
                target: 3540.80,
                value: 6000,
                percentage: 0,
            },
            daysTraded: {
                target: 1,
                value: 3,
                percentage: 0,
                pass: true
            },
            rule: {
                maximumLossLimit: {
                    pass: true,
                    description: "Keep your Account Balance above $96,500"
                }
            },
        },
    },
    {
        id: 3,
        name: 'S1SEP2586479132DSD10',
        planDetail: getPlanDetail('growth', '150K'),
        status: 'active',
        tradingType: 'megax',
        overallPerformance: {
            currentBalance: 159420.75,
            totalProfit: {
                value: 9420.75,
                percentage: 6.28
            },
            tradingDays: 12,
            dailyLossLimit: 3750,
            currentEquity: 159420.75,
            weeklyNetPnL: 2740.60
        },
        objectives: {
            profitTarget: {
                target: 9420.75,
                value: 9000,
                percentage: 0,
                pass: true
            },
            daysTraded: {
                target: 1,
                value: 12,
                percentage: 0,
                pass: true
            },
            rule: {
                maximumLossLimit: {
                    pass: true,
                    description: "Keep your Account Balance above $145,000"
                }
            },
        }
    },
    {
        id: 4,
        name: 'S1SEP2586479132DSD11',
        planDetail: getPlanDetail('funded', '50K'),
        status: 'inactive',
        tradingType: 'megax',
        overallPerformance: {
            currentBalance: 47650.30,
            totalProfit: {
                value: -2349.70,
                percentage: -4.70
            },
            tradingDays: 6,
            dailyLossLimit: 1250,
            currentEquity: 47650.30,
            weeklyNetPnL: -1130.50,
        },
        objectives: {
            profit: {
                target: -2349.70,
                value: 2500,
                percentage: 0,
                pass: false
            },
            tradingDayBetweenPayouts: {
                target: 10,
                value: 6,
                percentage: 0,
                pass: false
            },
            tradingDayWithProfit: {
                target: 5,
                value: 1,
                percentage: 0,
                pass: false
            },
            consistency: {
                percentage: 45,
                minPercentage: 35
            },
            highestProfitDaySinceLastPayout: 0,
            rule: {
                maximumLossLimit: {
                    pass: false,
                    description: "Keep your Account Balance above $48,000"
                }
            },
        }
    },
    {
        id: 5,
        name: 'S1SEP2586479132DSD10',
        planDetail: getPlanDetail('funded', '100K'),
        status: 'active',
        tradingType: 'tradovate',
        overallPerformance: {
            currentBalance: 104280.60,
            totalProfit: {
                value: 4280.60,
                percentage: 4.28
            },
            tradingDays: 7,
            dailyLossLimit: 2500,
            currentEquity: 104280.60,
            weeklyNetPnL: 1930.75
        },
        objectives: {
            profitTarget: {
                target: 4280.60,
                value: 6000,
                percentage: 0,
                pass: false
            },
            daysTraded: {
                target: 10,
                value: 7,
                percentage: 0,
            },
            tradingDayWithProfit: {
                target: 5,
                value: 4,
                percentage: 0,
                pass: false
            },
            consistency: {
                percentage: 30,
                minPercentage: 35
            },
            highestProfitDaySinceLastPayout: 0,
            rule: {
                maximumLossLimit: {
                    pass: true,
                    description: "Keep your Account Balance above $96,000"
                }
            },
        },
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


export const defaultUser: IUser = {
    fullName: 'JOHN DOE',
    firstName: 'Jane',
    lastName: 'Doe',
    email: 'janedoe@gmail.com',
    zipCode: '',
    verified: false,
    memberSince: '21-12-2023',
    address: '',
    state: '',
    city: '',
    phone: '',
    country: '',
    language: '',
}

export const languages = [
    {id: 'ar', description: 'Arabic'},
    {id: 'zh', description: 'Chinese'},
    {id: 'en', description: 'English'},
    {id: 'fr', description: 'French'},
    {id: 'de', description: 'German'},
    {id: 'it', description: 'Italian'},
    {id: 'ja', description: 'Japanese'},
    {id: 'pt', description: 'Portuguese'},
    {id: 'ru', description: 'Russian'},
    {id: 'es', description: 'Spanish'}
];

export const countries = [
    {id: 'BR', description: 'Brazil'},
    {id: 'CN', description: 'China'},
    {id: 'FR', description: 'France'},
    {id: 'DE', description: 'Germany'},
    {id: 'IT', description: 'Italy'},
    {id: 'JP', description: 'Japan'},
    {id: 'PT', description: 'Portugal'},
    {id: 'RU', description: 'Russia'},
    {id: 'ES', description: 'Spain'},
    {id: 'US', description: 'United States'}
];

export const PayoutMetrics: Metrics[] = [
    {
        title: 'Available Amount',
        subtitle: 'Withdrable profit available',
        value: formatCurrency(4895)
    },
    {
        title: 'Available Profit',
        subtitle: 'Your total account profit',
        value: formatCurrency(9000)
    },
    {
        title: 'Profit Share %',
        subtitle: 'The amount of the profit you keep',
        value: '80%'
    },
    {
        title: 'Next Withdraw Date',
        subtitle: 'Next date you can withdraw profits',
        value: '06/03/2025'
    },
]

export const AffiliatesMetrics: Metrics[] = [
    {
        title: 'Available Amount',
        subtitle: 'Withdrable profit available',
        value: formatCurrency(4895)
    },
    {
        title: 'Total Earnings',
        subtitle: 'Total profit earned',
        value: formatCurrency(5471)
    },
    {
        title: 'Active Referrals',
        subtitle: 'Subscribed user count',
        value: 12
    },
    {
        title: 'Total sold',
        subtitle: 'Purchased plan count',
        value: 7
    },
]

export const chartConfig = {
    type: "line" as const,
    height: '100%',
    series: [
        {
            name: "Pro Plan Revenue",
            data: [24850, 24600, 24300, 23950, 23500, 23250, 23000, 23250, 23500, 24000, 24300, 24550],
        },
        {
            name: "Upper Bound",
            data: [24250, 24250, 24250, 24250, 24250, 24250, 24250, 24250, 24250, 24250, 24250, 24250],
        },
        {
            name: "Lower Bound",
            data: [23250, 23250, 23250, 23250, 23250, 23250, 23250, 23250, 23250, 23250, 23250, 23250],
        },
    ],
    options: {
        chart: {
            toolbar: {
                show: false,
            },
        },
        title: {
            show: false,
        },
        dataLabels: {
            enabled: false,
        },
        colors: ["#FFE7B8", "#24b8a6", "#FF4D4D"],
        stroke: {
            lineCap: "round",
            curve: "smooth",
            width: [2, 2, 2],
        },
        markers: {
            size: [0, 5, 5],
            colors: ["#FF4D4D", "#24b8a6"],
            strokeColors: 'transparent',
            strokeWidth: 0
        },
        legend: {
            show: false
        },
        xaxis: {
            axisTicks: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
            labels: {
                style: {
                    colors: "#A8A29E",
                    fontSize: "12px",
                    fontFamily: "inherit",
                    fontWeight: 400,
                },
            },
            categories: [0, 2, 4, 6, 8, 10, 12, 14, 16, 18],
        },
        yaxis: {
            labels: {
                formatter: (value: number) => `$${value}`,
                style: {
                    colors: "#A8A29E",
                    fontSize: "12px",
                    fontFamily: "inherit",
                    fontWeight: 400,
                },
            },
        },
        grid: {
            show: true,
            borderColor: "#374151",
            strokeDashArray: 5,
        },
        fill: {
            opacity: 0.8,
        },
        tooltip: {
            theme: "dark",
            x: {
                show: true,
            },
            y: {
                formatter: (value: number) => `$ ${value.toFixed(2)}`,
            },
        },
    } as ApexOptions,
};

export const chartAffiliatesConfig = {
    type: "line" as const,
    height: '100%',
    series: [
        {
            name: "Earnings over time",
            data: [400, 300, 200, 100, 0],
        },
    ],
    options: {
        chart: {
            toolbar: {
                show: false,
            },
        },
        title: {
            show: false,
        },
        dataLabels: {
            enabled: false,
        },
        colors: ["#FFE7B8"],
        stroke: {
            lineCap: "round",
            curve: "smooth",
            width: 2,
        },
        markers: {
            size: 0,
        },
        xaxis: {
            axisTicks: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
            labels: {
                style: {
                    colors: "#A8A29E",
                    fontSize: "12px",
                    fontFamily: "inherit",
                    fontWeight: 400,
                },
            },
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        },
        yaxis: {
            labels: {
                formatter: (value: number) => `${value}`,
                style: {
                    colors: "#A8A29E",
                    fontSize: "12px",
                    fontFamily: "inherit",
                    fontWeight: 400,
                },
            },
        },
        grid: {
            show: true,
            borderColor: "#374151",
            strokeDashArray: 5,
        },
        fill: {
            opacity: 0.8,
        },
        tooltip: {
            theme: "dark",
            x: {
                show: true,
            },
            y: {
                formatter: (value: number) => `$ ${value.toFixed(2)}`,
            },
        },
    } as ApexOptions,
};

export const featureContentOptions: { id: string, label: string }[] = [
    {id: 'overview', label: 'Overview'},
    {id: 'e_mini_sp_500', label: 'E-mini S&P 500'},
    {id: 'micro_e_mini_sp', label: 'Micro E-mini S&P'},
    {id: 'e_mini_nasdaq_100', label: 'E-mini NASDAQ 100'},
    {id: 'micro_e_mini_nasdaq_100', label: 'Micro E-mini NASDAQ 100'},
    {id: 'e_mini_russell_2000', label: 'E-mini Russell 2000'},
    {id: 'micro_e_mini_russell_2000', label: 'Micro E-mini Russell 2000'},
    {id: 'nikkei_nkd', label: 'Nikkei NKD'},
    {id: 'micro_bitcoin', label: 'Micro Bitcoin'},
    {id: 'micro_ether', label: 'Micro Ether'},
    {id: 'australian_dollar', label: 'Australian Dollar'},
    {id: 'british_pound', label: 'British Pound'},
    {id: 'canadian_dollar', label: 'Canadian Dollar'},
    {id: 'euro_fx', label: 'Euro FX'},
    {id: 'japanese_yen', label: 'Japanese Yen'},
    {id: 'swiss_franc', label: 'Swiss Franc'},
    {id: 'e_mini_euro_fx', label: 'E-mini Euro FX'},
    {id: 'micro_euro_fx', label: 'Micro Euro FX'},
    {id: 'micro_aud_usd', label: 'Micro AUD/USD'},
    {id: 'mexican_peso', label: 'Mexican Peso'},
    {id: 'new_zealand_dollar', label: 'New Zealand Dollar'},
    {id: 'micro_gbp_usd', label: 'Micro GBP/USD'},
    {id: 'lean_hogs', label: 'Lean Hogs'},
    {id: 'live_cattle', label: 'Live Cattle'},
    {id: 'crude_oil', label: 'Crude Oil'},
    {id: 'e_mini_crude_oil', label: 'E-mini Crude Oil'},
    {id: 'natural_gas', label: 'Natural Gas'},
    {id: 'e_mini_natural_gas', label: 'E-mini Natural Gas'},
    {id: 'micro_crude_oil', label: 'Micro Crude Oil'},
    {id: 'rbob_gasoline', label: 'RBOB Gasoline'},
    {id: 'heating_oil', label: 'Heating Oil'},
    {id: 'platinum', label: 'Platinum'},
    {id: 'micro_henry_hub_natural_gas', label: 'Micro Henry Hub Natural Gas'},
    {id: 'corn', label: 'Corn'},
    {id: 'wheat', label: 'Wheat'},
    {id: 'soybeans', label: 'Soybeans'},
    {id: 'soybean_meal', label: 'Soybean Meal'},
    {id: 'soybean_oil', label: 'Soybean Oil'},
    {id: 'mini_dow', label: 'Mini-DOW'},
    {id: 'micro_mini_dow', label: 'Micro Mini-DOW'},
    {id: '2_year_note', label: '2-Year Note'},
    {id: '5_year_note', label: '5-Year Note'},
    {id: '10_year_note', label: '10-Year Note'},
    {id: '10_year_ultra_note', label: '10-Year Ultra-Note'},
    {id: '30_year_bond', label: '30-Year Bond'},
    {id: 'ultra_bond', label: 'Ultra-Bond'},
    {id: 'gold', label: 'Gold'},
    {id: 'silver', label: 'Silver'},
    {id: 'copper', label: 'Copper'},
    {id: 'micro_gold', label: 'Micro Gold'},
    {id: 'micro_silver', label: 'Micro Silver'},
    {id: 'micro_copper', label: 'Micro Copper'},
];