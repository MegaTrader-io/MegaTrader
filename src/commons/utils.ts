import dayjs from "dayjs";

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