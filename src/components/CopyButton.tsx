import React from "react";
import Image from "next/image";
import {useCopy} from "@/hooks/useCopy";

export function CopyButton({value}: { value: string }) {
    const {buttonRef, copySuccess} = useCopy(value);

    return (
        <button ref={buttonRef} className="relative">
            <Image
                className="w-6 h-6"
                src="/assets/images/copy.svg"
                alt="copy"
                width={24}
                height={24}
            />
            {copySuccess && (
                <span className="absolute top-[-14px] right-[-9px] text-xs text-gray-400">
                    copied
                </span>
            )}
        </button>
    );
}
