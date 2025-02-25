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

export interface TotalProfit {
    value: number,
    percentage: number
}

export interface OverallPerformance {
    currentBalance: number,
    totalProfit: TotalProfit,
    tradingDays: number,
    dailyLossLimit: number,
    currentEquity: number,
    weeklyNetPnL: number,
}

export type AccountStatusType = 'active' | 'inactive'

export interface ObjectiveType {
    target: number;
    value: number;
    percentage: number;
    pass?: boolean
}

interface Consistency {
    minPercentage: number;
    percentage: number;
}

interface MaximumLossLimit {
    description: string;
    pass: boolean;
}

interface Rule {
    maximumLossLimit: MaximumLossLimit;
}

interface Objectives {
    profit?: ObjectiveType;
    profitTarget?: ObjectiveType;
    daysTraded?: ObjectiveType;
    tradingDayBetweenPayouts?: ObjectiveType;
    tradingDayWithProfit?: ObjectiveType;
    consistency?: Consistency;
    highestProfitDaySinceLastPayout?: number;
    rule: Rule;
}

export type PlanLevel = '50K' | '100K' | '150K'
export type PlanType = 'elite' | 'growth' | 'funded'

export type PlanDetail = {
    level: PlanLevel,
    value: number,
    planType: PlanType,
}

export interface Account {
    id: number
    name: string
    planDetail: PlanDetail,
    status: AccountStatusType,
    overallPerformance: OverallPerformance,
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

export type RequestStatusType = 'APPROVED' | 'PENDING' | 'REJECTED'

export interface IPayoutRequest {
    id: number
    dateOfRequest: string
    mtAmount: number
    profitShare: number
    traderShare: number
    status: RequestStatusType
}