import dayjs from "dayjs";
import {PlanDetail, PlanLevel, PlanType} from "@/commons/interfaces";
import {TRANSACTION_PERCENTAGE} from "@/commons/data";

function valueBySize(size: PlanLevel) {
    if (size === '50K') {
        return 50000
    } else if (size === '100K') {
        return 100000;
    } else if (size === '150K') {
        return 150000;
    }

    return 0
}

export function getPlanDetail(planType: PlanType, size: PlanLevel): PlanDetail {

    const plans = {
        'elite': {
            level: size,
            value: valueBySize(size),
            planType: 'elite'
        },
        'growth': {
            level: size,
            value: valueBySize(size),
            planType: 'growth'
        },
        'funded': {
            level: size,
            value: valueBySize(size),
            planType: 'funded'
        },
    };

    return plans[planType] as PlanDetail;
}

export function formatCurrency(value: number, decimal: number = 2) {
    try {
        const hasDecimals = value % 1 !== 0;

        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",
            minimumFractionDigits: hasDecimals ? decimal : 0,
            maximumFractionDigits: 2,
        }).format(value);
    } catch (error) {
        console.error("Error with formatCurrency:", error);
        return value.toString();
    }
}

export function sleep(miliseconds = 1800) {
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve(1);
        }, miliseconds)
    })
}

export function getInitials(fullName: string) {
    if (fullName.trim() === '') {
        return fullName;
    }

    return fullName
        .trim()
        .split(/\s+/)
        .map(word => word[0]?.toUpperCase())
        .join('');
}

export function formatDateTime(
    datetime: string,
    format: string = 'MMM DD, YYYY hh:mm:ss A',
): string {
    if (!datetime) {
        return '---'
    }

    return dayjs(datetime).format(format)
}

export function capitalizeWords(value: string) {
    return value.toString().split(' ').map(word => {
        return word.charAt(0).toUpperCase() + +word.slice(1).toLowerCase();
    }).join(' ');
}

export function calculateAmountToReceive(amount: number) {
    const transactionFee = amount * TRANSACTION_PERCENTAGE;
    return {transactionFee, netAmount: Math.max(0, amount - transactionFee)};
}

export function debounce<T extends (...args: any[]) => void>(fn: T, delay: number) {
    let timeoutId: NodeJS.Timeout;
    return (...args: Parameters<T>): void => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
}