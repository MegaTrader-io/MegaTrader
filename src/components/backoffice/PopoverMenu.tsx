import React, {PropsWithChildren, useEffect, useRef, useState} from 'react';
import {Popover, PopoverContent, PopoverPortal, PopoverTrigger, PopoverArrow} from "@radix-ui/react-popover";
import {Bars3Icon} from "@heroicons/react/24/solid";

interface Props extends PropsWithChildren {
    className?: string;
    icon?: React.ReactNode;
    side?: "top" | "bottom" | "left" | "right";
    align?: "start" | "center" | "end";
}

function PopoverMenu({className, children, icon, side = "bottom", align = "center"}: Props) {
    const [isVisible, setIsVisible] = useState(true);
    const [open, setOpen] = useState(false);
    const buttonRef = useRef<HTMLButtonElement>(null);

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

    return (
        <div className={className}>
            <Popover open={open} onOpenChange={setOpen}>
                <PopoverTrigger asChild>
                    <button ref={buttonRef} className="btn-primary block">
                        {!icon && <Bars3Icon className="w-6 h-6 text-white"/>}
                        {icon && icon}
                    </button>
                </PopoverTrigger>
                {isVisible && (
                    <PopoverPortal>
                        <PopoverContent
                            side={side}
                            align={align}
                            className="flex flex-col items-center justify-center gap-2 p-2 relative bg-white rounded-lg border border-solid border-[#494949] z-[1000]"
                        >
                            {children}
                            <PopoverArrow width={26} height={14} className="fill-white"/>
                        </PopoverContent>
                    </PopoverPortal>
                )}
            </Popover>
        </div>
    );
}

export default PopoverMenu;