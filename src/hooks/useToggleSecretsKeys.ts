import {useCallback, useEffect, useState} from "react";

interface SecretElement {
    element: HTMLDivElement | null;
    value: string;
}

export default function useToggleSecretsKeys(elements: SecretElement[]) {
    const [isMasked, setIsMasked] = useState(true);
    const [currentMask, setCurrentMask] = useState<"password" | "text">("password");

    const toggleSecretValue = useCallback(
        (secretType: "password" | "text", element: HTMLDivElement, value: string) => {
            try {
                element.innerText = secretType === "text" ? value : "•".repeat(value.length);
            } catch (error) {
                console.error("Failed to toggle secret value:", error);
            }
        },
        []
    );

    const updateSecretsDisplay = useCallback(() => {
        const maskType = isMasked ? "password" : "text";
        elements.forEach(({element, value}) => {
            if (element) {
                toggleSecretValue(maskType, element, value);
            } else {
                console.warn("Element is null, skipping toggle");
            }
        });
        setCurrentMask(isMasked ? "password" : "text");
    }, [elements, isMasked, toggleSecretValue]);

    const toggleMask = useCallback(() => {
        setIsMasked((prev) => !prev);
    }, []);

    useEffect(() => {
        updateSecretsDisplay();
    }, [updateSecretsDisplay]);

    return {toggleMask, currentMask};
}
