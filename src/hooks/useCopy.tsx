import {useEffect, useRef, useState} from "react";
import ClipboardJS from "clipboard";

export function useCopy(value: string) {
    const buttonRef = useRef<HTMLButtonElement | null>(null);
    const [copySuccess, setCopySuccess] = useState(false);

    useEffect(() => {
        if (!value) {
            return;
        }

        const clipboard = new ClipboardJS(buttonRef.current as HTMLButtonElement, {
            text: () => value,
        });

        clipboard.on("success", (e) => {
            setCopySuccess(true);
            console.log("copied:", e.text);
            e.clearSelection();
            setTimeout(() => setCopySuccess(false), 800);
        });

        clipboard.on("error", (e) => {
            console.error("unable to copy:", e.action, e.trigger);
        });

        return () => {
            clipboard.destroy();
        };
    }, [value]);

    return { buttonRef, copySuccess };
}
