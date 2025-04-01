import React from "react";
import {MAX_WITHDRAWAL} from "@/commons/data";

function useValidateNumber({handlePasteBehavior, handleWithdrawalAmount}: {
    handlePasteBehavior: (field: string, sanitizedData: string) => void
    handleWithdrawalAmount: (value: string) => void
}) {
    function handleBeforeInput(event: React.FormEvent<HTMLInputElement>) {
        const input = event.nativeEvent as InputEvent;
        const newChar = input.data || "";
        const currentValue = event.currentTarget.value;

        if (!/^[0-9.]$/.test(newChar)) {
            event.preventDefault();
            return;
        }

        if (newChar === "." && currentValue.includes(".")) {
            event.preventDefault();
        }
    }


    function handlePaste(event: React.ClipboardEvent<HTMLInputElement>) {
        event.preventDefault();
        const pasteData = event.clipboardData.getData("text");

        const sanitizedData = pasteData.replace(/[^0-9.]/g, "");

        if ((sanitizedData.match(/\./g) || []).length > 1) {
            return;
        }

        event.currentTarget.value = sanitizedData;
        handlePasteBehavior(event.currentTarget.name, sanitizedData);
    }

    function verifyWithdrawalAmount(ev: React.ChangeEvent<HTMLInputElement>) {
        const value = ev.target.value.toString().trim();
        if (!value) {
            return;
        }

        const withdrawalAmount = Number(value);
        let newErrorsWithdrawalAmount: string = '';
        if (withdrawalAmount && (withdrawalAmount <= 0 || withdrawalAmount > MAX_WITHDRAWAL)) {
            newErrorsWithdrawalAmount = "Invalid amount."
        }

        handleWithdrawalAmount(newErrorsWithdrawalAmount)
    }

    return {handleBeforeInput, handlePaste, verifyWithdrawalAmount}
}

export default useValidateNumber;