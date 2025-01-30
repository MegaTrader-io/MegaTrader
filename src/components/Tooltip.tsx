import React, {PropsWithChildren, useState} from "react";
import * as BaseTooltip from "@radix-ui/react-tooltip";
import clsx from "clsx";
import TooltipArrow from "@/components/TooltipArrow";

interface Props extends PropsWithChildren {
    content?: React.ReactNode | string,
    className?: string
}

const Tooltip: React.FC<Props> = ({children, className, content}) => {
    const [open, setOpen] = useState(false);

    const handleOpen = () => {
        setOpen(true);
    };

    const handleClose = () => {
        setOpen(false);
    };

    return (
        <BaseTooltip.Provider delayDuration={0}>
            <BaseTooltip.Root open={open} onOpenChange={setOpen}>
                <BaseTooltip.Trigger asChild>
                    <button
                        className={clsx('', className)}
                        onClick={(e) => {
                            e.stopPropagation();
                            handleOpen();
                        }}
                        onBlur={handleClose}
                    >
                        {children}
                    </button>
                </BaseTooltip.Trigger>
                <BaseTooltip.Portal>
                    <BaseTooltip.Content
                        sideOffset={10}
                        className={clsx(
                            'p-3 bg-black rounded-lg border border-neutral-700 box-border w-max max-w-[calc(100vw-10px)] text-stone-400',
                            {'hidden': !open}
                        )}
                        onPointerDownOutside={handleClose}
                        onMouseEnter={handleOpen}
                        onMouseLeave={handleClose}
                    >
                        <div className="w-[265px] text-stone-400 text-xs font-normal leading-tight">
                            {content}
                        </div>
                        <TooltipArrow/>
                    </BaseTooltip.Content>
                </BaseTooltip.Portal>
            </BaseTooltip.Root>
        </BaseTooltip.Provider>
    );
}

export default Tooltip;
