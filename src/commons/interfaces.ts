import React from "react";

export interface PlanInterface {
    id: number
    level: string
    total_peer_year: string
    total_peer_month: string
    max_loss_limit: string
    max_position_size: string
    profit_target: string
    color: string
    colorItem: string
}

export interface SymbolMarketData {
    name: string,
    price: number,
    change: number
}

export type MgProps = React.HTMLAttributes<HTMLDivElement> & {
    children: React.ReactNode;
};

export interface AccountBalance {
    currentBalance: string,
    currentEquity: string,
    high: string,
    low: string,
    weeklyNetPnL: string,
    bestDayPercentage: string,
    bestDay: string,
    worstDay: string,
    avgWinningDay: string,
    avgLosingDay: string
}

export type AccountStatusType = 'active' | 'unpaid' | 'breach' | 'funded'

interface Profit {
    goal: number | null;
    current: number | null;
    percentage: number | null;
}

interface Consistency {
    percentage: number | null;
    description: string | null;
}

interface MaximumLossLimit {
    threshold: number;
    description: string;
}

interface Rule {
    maximumLossLimit: MaximumLossLimit;
}

interface TradingDays {
    current: number | null;
    total: number | null;
    betweenPayouts: {
        current: number | null;
        total: number | null;
    };
    daysWithMinProfit: {
        current: number | null;
        total: number | null;
        minProfit: number | null;
    };
}

interface Objectives {
    profit: Profit;
    consistency: Consistency;
    rule: Rule;
    tradingDays: TradingDays;
}

export interface Account {
    id: number
    name: string
    status: AccountStatusType,
    accountBalance: AccountBalance,
    objectives: Objectives
}

export interface Period {
    id: string
    text: string
}

export interface TooltipData {
    parameters: {
        startingBalance: string;
        maxPositionSize: string;
        maxDrawdown: string;
    };
    accountDetails: {
        accountNumber: string;
        platform: string;
        username: string;
        password: string;
    };
}

export interface SurveyState {
    id?: number | undefined,
    emojiId?: number | undefined
    simpleQuestion?: boolean | undefined
    note?: string | null
}

export interface JournalEntry {
    survey: SurveyState | null;
    date: string;
    netPnl: string;
    pnlHigh: string;
    pnlLow: string;
    totalContracts: number;
    totalFeesComm: string;
    totalTrades: number;
    avgWinningTrades: string;
    avgLosingTrades: string;
    winningTradePercentage: number;
    maxConsecutiveWLTrades: string;
    avgWLDuration: string;
    id: number;
}

export interface PayoutsEntry {
    id: number,
    month: string,
    sold: number,
    total_profit: number,
    status: 'paid' | 'pending'
}

export interface IncomeEntry {
    id: number;
    orderNumber: string;
    product: string;
    created: string;
    paymentDate: string;
    originalPrice: number;
    profit: number;
}


export interface Emoji {
    id: number;
    name: string;
    description: string;
    icon: React.ReactElement;
}

export interface VisitDataEntry {
    id: number;
    url: string;
    referrer: string;
    converted: boolean;
}

export interface OptionInterface {
    id: string;
    label: string
}

export interface IOption {
    url: string,
    label: string
}

export interface IUser {
    fullName: string,
    firstName: string,
    lastName: string,
    email: string,
    verified: boolean,
    zipCode: string,
    memberSince: string,
    address: string,
    state: string,
    city: string,
    phone: string,
    country: string,
    language: string,
}

interface NotificationAction {
    label: string;
    read: boolean;
}

export type NotificationStatus = "success" | "error" | "warning";

export interface INotification {
    id: string;
    status: NotificationStatus;
    title: string;
    message: string;
    action: NotificationAction;
}