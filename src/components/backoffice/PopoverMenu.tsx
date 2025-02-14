import React, {PropsWithChildren, useEffect, useRef, useState} from 'react';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
    PopoverArrow,
    PopoverPortal
} from "@radix-ui/react-popover";
import {Bars3Icon} from "@heroicons/react/24/solid";

interface Props extends PropsWithChildren {
    className?: string;
    icon?: React.ReactNode;
    modal?: boolean
    side?: "top" | "bottom" | "left" | "right";
    align?: "start" | "center" | "end";
}

function PopoverMenu({className, children, icon, modal = false, side = "bottom", align = "center"}: Props) {
    const [isVisible, setIsVisible] = useState(true);
    const popoverRef = useRef<HTMLDivElement>(null)
    const prevTransformRef = useRef<string | null>(null);
    const [open, setOpen] = useState(false);
    const buttonRef = useRef<HTMLButtonElement>(null);

    function adjustPopoverPosition() {
        if (!modal) {
            return;
        }
        const isMobile = window.innerWidth < 640;
        setTimeout(() => {
            const popoverWrapper = (popoverRef.current?.closest("[data-radix-popper-content-wrapper]") || document?.querySelector('.custom-popover') && document.querySelector('.custom-popover')!.closest("[data-radix-popper-content-wrapper]")) as HTMLElement;
            console.info('isMobile', isMobile)
            if (popoverWrapper) {
                if (isMobile) {
                    if (!prevTransformRef.current) {
                        prevTransformRef.current = popoverWrapper.style.transform;
                    }

                    popoverWrapper.style.transform = "translate(0px, 0px)";
                } else {
                    if (prevTransformRef.current) {
                        popoverWrapper.style.transform = prevTransformRef.current;
                        popoverWrapper.style.transition = "";
                        prevTransformRef.current = null;
                    }
                }
            }
        }, 0)
    }

    useEffect(() => {
        if (!buttonRef.current) return;

        const observer = new IntersectionObserver(
            ([entry]) => {
                setIsVisible(entry.isIntersecting);
                if (!entry.isIntersecting) {
                    setOpen(false);
                }

                console.info('xxx');
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
    }, [open]);

    // < 640 //mobile

    return (
        <div className={className}>
            <Popover modal={modal} open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <button ref={buttonRef} className="btn-primary block">
                        {!icon && <Bars3Icon className="w-6 h-6 text-white"/>}
                        {icon && icon}
                    </button>
                </PopoverTrigger>
                {isVisible && (
                    <PopoverPortal>
                        <PopoverContent ref={popoverRef} asChild side={side} align={align} className='z-[1000]'>
                            <div className="custom-popover">
                                <div
                                    className="flex flex-col items-center justify-center gap-2 p-2 relative bg-white rounded-lg border border-solid border-[#494949]">
                                    {children}
                                    <PopoverArrow width={26} height={14} className="fill-white !hidden sm:!block"/>
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