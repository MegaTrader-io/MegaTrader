import {Account, INotification, IUser, Period, TooltipData} from "@/commons/interfaces";

export const accounts: Account[] = [
    {
        id: 1,
        name: 'S1SEP2586479132DSDS8',
        accountType: 'basic_plan',
        status: 'inactive',
        overallPerformance: {
            currentBalance: 47850.30,
            totalProfit: {
                value: -2149.70,
                percentage: -4.30
            },
            tradingDays: 4,
            dailyLossLimit: 2000,
            currentEquity: 47850.30,
            weeklyNetPnL: -1560.40,
        },
        objectives: {
            profit: {
                goal: 3000.00,
                current: 2149.70,
                percentage: 0,
                pass: false
            },
            tradingDays: {
                current: 1,
                total: 4,
                betweenPayouts: {
                    current: null,
                    total: null
                },
                daysWithMinProfit: {
                    current: null,
                    total: null,
                    minProfit: null
                }
            },
            consistency: {
                percentage: null,
                description: null
            },
            rule: {
                maximumLossLimit: {
                    pass: false,
                    threshold: 48000,
                    description: "Keep your Account Balance above $48,000"
                }
            },
        }
    },
    {
        id: 2,
        name: 'S1SEP2586479132DSDS9',
        accountType: 'pro_plan',
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
            profit: {
                goal: 6000,
                current: 3540.80,
                percentage: 0,
                pass: true
            },
            tradingDays: {
                pass: true,
                current: 1,
                total: 3,
                betweenPayouts: {
                    current: null,
                    total: null
                },
                daysWithMinProfit: {
                    current: null,
                    total: null,
                    minProfit: null
                }
            },
            consistency: {
                percentage: null,
                description: null
            },
            rule: {
                maximumLossLimit: {
                    pass: true,
                    threshold: 96500,
                    description: "Keep your Account Balance above $96,500"
                }
            },
        }
    },
    {
        id: 3,
        name: 'S1SEP2586479132DSD10',
        accountType: 'premium_plan',
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
            profit: {
                goal: 9000,
                current: 9420.75,
                percentage: 0,
                pass: true
            },
            tradingDays: {
                pass: true,
                current: 1,
                total: 12,
                betweenPayouts: {
                    current: null,
                    total: null
                },
                daysWithMinProfit: {
                    current: null,
                    total: null,
                    minProfit: null
                }
            },
            consistency: {
                percentage: null,
                description: null
            },
            rule: {
                maximumLossLimit: {
                    pass: true,
                    threshold: 145000,
                    description: "Keep your Account Balance above $145,000"
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