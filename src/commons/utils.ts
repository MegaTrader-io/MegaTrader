export function formatCurrency(value: number) {
    try {
        return new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",
            minimumFractionDigits: 2,
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