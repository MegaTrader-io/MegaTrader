import {Account, INotification, IUser, Period, TooltipData} from "@/commons/interfaces";

export const accounts: Account[] = [
    {
        id: 1,
        name: 'S1SEP2586479132DSDS8',
        accountType: 'basic_plan',
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
        },
        objectives: {
            profit: {
                goal: 9000,
                current: 2900,
                percentage: 37.5
            },
            consistency: {
                percentage: null,
                description: null
            },
            rule: {
                maximumLossLimit: {
                    threshold: 145500,
                    description: "Do not let your account balance hit or go below $145,500."
                }
            },
            tradingDays: {
                current: 1,
                total: 5,
                betweenPayouts: {
                    current: null,
                    total: null
                },
                daysWithMinProfit: {
                    current: null,
                    total: null,
                    minProfit: null
                }
            }
        }
    },
    {
        id: 2,
        name: 'S1SEP2586479132DSDS9',
        status: 'unpaid',
        accountType: 'basic_plan',
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
        },
        objectives: {
            profit: {
                goal: 9000,
                current: -1600,
                percentage: 0
            },
            consistency: {
                percentage: null,
                description: null
            },
            rule: {
                maximumLossLimit: {
                    threshold: 145500,
                    description: "Do not let your account balance hit or go below $145,500."
                }
            },
            tradingDays: {
                current: 1,
                total: 5,
                betweenPayouts: {
                    current: null,
                    total: null
                },
                daysWithMinProfit: {
                    current: null,
                    total: null,
                    minProfit: null
                }
            }
        }
    },
    {
        id: 3,
        name: 'S1SEP2586479132DSD10',
        accountType: 'basic_plan',
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
        },
        objectives: {
            profit: {
                goal: 9000,
                current: 3200,
                percentage: 0
            },
            consistency: {
                percentage: null,
                description: null
            },
            rule: {
                maximumLossLimit: {
                    threshold: 145500,
                    description: "Do not let your account balance hit or go below $145,500."
                }
            },
            tradingDays: {
                current: 1,
                total: 5,
                betweenPayouts: {
                    current: null,
                    total: null
                },
                daysWithMinProfit: {
                    current: null,
                    total: null,
                    minProfit: null
                }
            }
        }
    },
    {
        id: 4,
        name: 'S1SEP2586479132DSD11',
        status: 'funded',
        accountType: 'basic_plan',
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
        },
        objectives: {
            profit: {
                goal: null,
                current: 5000.00,
                percentage: 22.00
            },
            consistency: {
                percentage: 100,
                description: null
            },
            rule: {
                maximumLossLimit: {
                    threshold: 145500,
                    description: "Do not let your account balance hit or go below $145,500."
                }
            },
            tradingDays: {
                current: null,
                total: null,
                betweenPayouts: {
                    current: 2,
                    total: 10
                },
                daysWithMinProfit: {
                    current: 1,
                    total: 5,
                    minProfit: 150
                }
            }
        }
    }
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