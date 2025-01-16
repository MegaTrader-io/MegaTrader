
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
    currentBalance: number,
    currentEquity: number,
    high: number,
    low: number,
    weeklyNetPnL: number,
    bestDayPercentage: string,
    bestDay: number,
    worstDay: number,
    avgWinningDay: string,
    avgLosingDay: number
}

export interface Account {
    id: number
    name: string
    active: boolean,
    accountBalance: AccountBalance
}