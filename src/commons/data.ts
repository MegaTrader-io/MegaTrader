import {Account, INotification, IPayoutRequest, IUser, Period, TooltipData} from "@/commons/interfaces";
import {formatCurrency, getPlanDetail} from "@/commons/utils";


export const accounts: Account[] = [
    {
        id: 1,
        name: 'S1SEP2586479132DSDS8',
        planDetail: getPlanDetail('elite'),
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
        planDetail: getPlanDetail('growth'),
        status: 'active',
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
        }
    },
    {
        id: 3,
        name: 'S1SEP2586479132DSD10',
        planDetail: getPlanDetail('funded'),
        status: 'active',
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
        planDetail: getPlanDetail('elite'),
        status: 'inactive',
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
                target: 6,
                value: 10,
                percentage: 0,
                pass: false
            },
            tradingDayWithProfit: {
                target: 1,
                value: 5,
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
        name: 'S1SEP2586479132DSD12',
        planDetail: getPlanDetail('growth'),
        status: 'active',
        overallPerformance: {
            currentBalance: 104280.60,
            totalProfit: {
                value: 4280.60,
                percentage: 4.28
            },
            tradingDays: 7,
            dailyLossLimit: 2500,
            currentEquity: 104280.60,
            weeklyNetPnL: 1930.75,
        },
        objectives: {
            profit: {
                target: 4280.60,
                value: 6000,
                percentage: 0,
                pass: false
            },
            tradingDayBetweenPayouts: {
                target: 7,
                value: 10,
                percentage: 0,
                pass: false
            },
            tradingDayWithProfit: {
                target: 1,
                value: 5,
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
        id: 6,
        name: 'S1SEP2586479132DSD13',
        planDetail: getPlanDetail('funded'),
        status: 'active',
        overallPerformance: {
            currentBalance: 159870.20,
            totalProfit: {
                value: 9870.20,
                percentage: 6.28
            },
            tradingDays: 12,
            dailyLossLimit: 3750,
            currentEquity: 159870.20,
            weeklyNetPnL: 2960.40,
        },
        objectives: {
            profit: {
                target: 9870.20,
                value: 9000,
                percentage: 0,
                pass: false
            },
            tradingDayBetweenPayouts: {
                target: 10,
                value: 12,
                percentage: 0,
                pass: false
            },
            tradingDayWithProfit: {
                target: 5,
                value: 6,
                percentage: 0,
                pass: false
            },
            consistency: {
                percentage: 25,
                minPercentage: 10
            },
            highestProfitDaySinceLastPayout: 534.15,
            rule: {
                maximumLossLimit: {
                    pass: true,
                    description: "Keep your Account Balance above $144,000"
                }
            },
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

export const notificationsData: INotification[] = [
    {
        id: "99966584551",
        status: "success",
        title: "Congratulations! You've Passed!",
        message: "Your dedication and skills have led to success. Welcome to the next level!",
        action: {
            label: "MARK READ",
            read: false
        }
    },
    {
        id: "99966584552",
        status: "error",
        title: "Evaluation Failed",
        message: "Unfortunately, you did not meet the evaluation criteria.",
        action: {
            label: "MARK READ",
            read: false
        }
    },
    {
        id: "99966584553",
        status: "success",
        title: "Congratulations! You've Passed!",
        message: "Your dedication and skills have led to success. Welcome to the next level!",
        action: {
            label: "MARK READ",
            read: false
        }
    },
    {
        id: "99966584554",
        status: "warning",
        title: "Account Breach Alert",
        message: "A rule violation has been detected in your account.",
        action: {
            label: "MARK READ",
            read: false
        }
    },
    {
        id: "99966584555",
        status: "success",
        title: "Congratulations! You've Passed!",
        message: "Your dedication and skills have led to success. Welcome to the next level!",
        action: {
            label: "MARK READ",
            read: false
        }
    },
    {
        id: "99966584556",
        status: "error",
        title: "Evaluation Failed",
        message: "Unfortunately, you did not meet the evaluation criteria.",
        action: {
            label: "MARK READ",
            read: false
        }
    },
    {
        id: "99966584557",
        status: "success",
        title: "Congratulations! You've Passed!",
        message: "Your dedication and skills have led to success. Welcome to the next level!",
        action: {
            label: "MARK READ",
            read: false
        }
    },
    {
        id: "99966584558",
        status: "warning",
        title: "Account Breach Alert",
        message: "A rule violation has been detected in your account.",
        action: {
            label: "MARK READ",
            read: false
        }
    },
    {
        id: "99966584559",
        status: "success",
        title: "Congratulations! You've Passed!",
        message: "Your dedication and skills have led to success. Welcome to the next level!",
        action: {
            label: "MARK READ",
            read: false
        }
    },
    {
        id: "99966584560",
        status: "warning",
        title: "Account Breach Alert",
        message: "A rule violation has been detected in your account.",
        action: {
            label: "MARK READ",
            read: false
        }
    },
    {
        id: "99966584561",
        status: "warning",
        title: "Account Breach Alert",
        message: "A rule violation has been detected in your account.",
        action: {
            label: "MARK READ",
            read: false
        }
    }
];

export const payoutRequests: IPayoutRequest[] = [
    {
        id: 1,
        dateOfRequest: "01-01-2023",
        mtAmount: 910.00,
        profitShare: 60,
        traderShare: 546.00,
        status: "APPROVED"
    },
    {
        id: 2,
        dateOfRequest: "01-01-2023",
        mtAmount: 1810.00,
        profitShare: 50,
        traderShare: 450.00,
        status: "APPROVED"
    },
    {
        id: 3,
        dateOfRequest: "01-01-2023",
        mtAmount: 900.00,
        profitShare: 18,
        traderShare: 80.00,
        status: "APPROVED"
    },
    {
        id: 4,
        dateOfRequest: "01-01-2023",
        mtAmount: 900.00,
        profitShare: 18,
        traderShare: 80.00,
        status: "APPROVED"
    },
    {
        id: 5,
        dateOfRequest: "01-01-2023",
        mtAmount: 900.00,
        profitShare: 18,
        traderShare: 80.00,
        status: "APPROVED"
    },
    {
        id: 6,
        dateOfRequest: "01-01-2023",
        mtAmount: 910.00,
        profitShare: 60,
        traderShare: 546.00,
        status: "PENDING"
    },
    {
        id: 7,
        dateOfRequest: "01-01-2023",
        mtAmount: 1810.00,
        profitShare: 50,
        traderShare: 450.00,
        status: "PENDING"
    },
    {
        id: 8,
        dateOfRequest: "01-01-2023",
        mtAmount: 900.00,
        profitShare: 18,
        traderShare: 80.00,
        status: "PENDING"
    },
    {
        id: 9,
        dateOfRequest: "01-01-2023",
        mtAmount: 910.00,
        profitShare: 60,
        traderShare: 546.00,
        status: "REJECTED"
    },
    {
        id: 10,
        dateOfRequest: "01-01-2023",
        mtAmount: 1810.00,
        profitShare: 50,
        traderShare: 450.00,
        status: "REJECTED"
    }
]

export const METRICS = [
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