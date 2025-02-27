import React, {PropsWithChildren, useCallback, useEffect, useRef, useState} from 'react';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
    PopoverArrow,
    PopoverPortal
} from "@radix-ui/react-popover";
import {Bars3Icon} from "@heroicons/react/24/solid";
import clsx from "clsx";

interface Props extends PropsWithChildren {
    className?: string;
    icon?: React.ReactNode;
    modal?: boolean
    side?: "top" | "bottom" | "left" | "right";
    align?: "start" | "center" | "end";
}

function PopoverMenu({className, children, icon, modal = true, side = "bottom", align = "center"}: Props) {
    const [isVisible, setIsVisible] = useState(true);
    const [isMobile, setIsMobile] = useState(false);
    const popoverRef = useRef<HTMLDivElement>(null)
    const prevTransformRef = useRef<string | null>(null);
    const [open, setOpen] = useState(false);
    const buttonRef = useRef<HTMLButtonElement>(null);

    const adjustPopoverPosition = useCallback(() => {
        if (!modal) {
            return;
        }

        setIsMobile(window.innerWidth < 640);

        setTimeout(() => {
            const popoverWrapper = (popoverRef.current?.closest("[data-radix-popper-content-wrapper]") ||
                document?.querySelector('.custom-popover')?.closest("[data-radix-popper-content-wrapper]")) as HTMLElement;

            if (popoverWrapper) {
                if (isMobile) {
                    if (!prevTransformRef.current) {
                        prevTransformRef.current = popoverWrapper.style.transform;
                    }
                    popoverWrapper.style.width = "100vw";
                    popoverWrapper.style.transform = "translate(0px, 0px)";
                } else {
                    if (prevTransformRef.current) {
                        popoverWrapper.style.width = "auto";
                        prevTransformRef.current = null;
                    }
                }
            }
        }, 0);
    }, [modal, isMobile]);

    useEffect(() => {
        if (!buttonRef.current) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                setIsVisible(entry.isIntersecting);
                if (!entry.isIntersecting) {
                    setOpen(false);
                }
            },
            {threshold: 0.1}
        );

        observer.observe(buttonRef.current);
        return () => observer.disconnect();
    }, []);

    useEffect(() => {
        if (open) {
            adjustPopoverPosition();
        }

        window.addEventListener('resize', adjustPopoverPosition);
        return () => window.removeEventListener('resize', adjustPopoverPosition);
    }, [open, isMobile, adjustPopoverPosition]);

    return (
        <div className={className}>
            <Popover modal={!!(modal && isMobile)} open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <button ref={buttonRef} className="bg-[#292524] rounded-xl border border-neutral-700 p-3 w-12 h-12 items-center justify-center">
                        {!icon && <Bars3Icon className="w-6 h-6 text-white"/>}
                        {icon && icon}
                    </button>
                </PopoverTrigger>
                {isVisible && (
                    <PopoverPortal>
                        <PopoverContent ref={popoverRef} asChild side={side} align={align}
                                        className={clsx('z-[1000]', {
                                            'bg-white': isMobile && modal,
                                            'h-[460px]': modal === false && !isMobile && modal
                                        })}>
                            <div className="custom-popover scroll-auto">
                                <div
                                    className={clsx('flex flex-col items-center justify-center gap-2 p-2 relative bg-white', [!isMobile || modal === false ? 'rounded-lg border border-solid border-[#494949]' : null])}>
                                    {children}
                                    <PopoverArrow width={26} height={14}
                                                  className={clsx('fill-white', {'!hidden sm:!block': modal})}/>
                                </div>
                            </div>
                        </PopoverContent>
                    </PopoverPortal>
                )}
            </Popover>
        </div>
    );
}

export default PopoverMenu;