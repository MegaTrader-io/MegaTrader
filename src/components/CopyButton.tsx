import React, {useState} from "react";
import clsx from "clsx";
import ClipboardJS from "clipboard";

interface CopyButtonProps {
    value: string;
    className?: string;
    color?: string;
    disabled?: boolean;
}

export function CopyButton({
                               value,
                               className,
                               color = "#A8A29E",
                               disabled = false,
                           }: CopyButtonProps) {
    const [copySuccess, setCopySuccess] = useState(false);
    const [errorMsg, setErrorMsg] = useState<string | null>(null);

    const handleCopy = async () => {
        if (disabled) return;

        try {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                await navigator.clipboard.writeText(value);
            } else {
                const tempBtn = document.createElement("button");
                document.body.appendChild(tempBtn);

                const clipboard = new ClipboardJS(tempBtn, {
                    text: () => value,
                });

                tempBtn.click();
                clipboard.destroy();
                tempBtn.remove();
            }

            setCopySuccess(true);
            setErrorMsg(null);
            setTimeout(() => setCopySuccess(false), 800);
        } catch (err: any) {
            console.error("Error copying to clipboard:", err);
            setErrorMsg("Copy failed");
            setTimeout(() => setErrorMsg(null), 2000);
        }
    };

    return (
        <button
            type="button"
            onClick={handleCopy}
            disabled={disabled}
            className={clsx(
                "relative disabled:text-stone-600 disabled:cursor-not-allowed",
                className,
                `text-[${color}]`
            )}
        >
            <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <mask
                    id="clipboard-mask"
                    style={{maskType: "alpha"}}
                    maskUnits="userSpaceOnUse"
                    x="0"
                    y="0"
                    width="24"
                    height="24"
                >
                    <rect width="24" height="24" fill="#D9D9D9"/>
                </mask>
                <g mask="url(#clipboard-mask)">
                    <path
                        d="M9 18C8.45 18 7.97917 17.8042 7.5875 17.4125C7.19583 17.0208 7 16.55 7 16V4C7 3.45 7.19583 2.97917 7.5875 2.5875C7.97917 2.19583 8.45 2 9 2H18C18.55 2 19.0208 2.19583 19.4125 2.5875C19.8042 2.97917 20 3.45 20 4V16C20 16.55 19.8042 17.0208 19.4125 17.4125C19.0208 17.8042 18.55 18 18 18H9ZM5 22C4.45 22 3.97917 21.8042 3.5875 21.4125C3.19583 21.0208 3 20.55 3 20V6H5V20H16V22H5Z"
                        fill="currentColor"
                    />
                </g>
            </svg>

            {copySuccess && (
                <span className="absolute top-[-20px] right-[-9px] text-xs text-gray-400">
          copied
        </span>
            )}
            {errorMsg && (
                <span className="absolute top-[-20px] right-[-9px] text-xs text-red-400">
          {errorMsg}
        </span>
            )}
        </button>
    );
}
