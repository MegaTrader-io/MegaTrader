import React, {PropsWithChildren, useEffect, useRef, useState} from "react";
import {Popover, PopoverArrow, PopoverContent, PopoverPortal, PopoverTrigger,} from "@radix-ui/react-popover";
import {Bars3Icon} from "@heroicons/react/24/solid";

interface Props extends PropsWithChildren {
    className?: string;
    collisionPadding?: number | undefined;
    icon?: React.ReactNode;
    side?: "top" | "bottom" | "left" | "right";
    align?: "start" | "center" | "end";
}

function PopoverMenu({
                         className,
                         children,
                         icon,
                         collisionPadding = undefined,
                         side = "bottom",
                         align = "center"
                     }: Props) {
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

    useEffect(() => {
        const handleResize = () => {
            if (open) setOpen(false);
        };

        window.addEventListener("resize", handleResize);
        return () => {
            window.removeEventListener("resize", handleResize);
        };
    }, [open]);

    const handleClick = (e: React.MouseEvent) => {
        const target = e.target as HTMLElement;
        if (target.closest('[data-dismiss="true"]')) {
            setOpen(false);
        }
    };

    const handleOpenMenu = (open: boolean) => {
        const isMobile = window.innerWidth <= 1024;
        const doesExistsIntroTooltip = document.querySelector('.introjs-tooltip');
        if (doesExistsIntroTooltip && isMobile) {
            return;
        }

        setOpen(open);
    }

    return (
        <div className={className}>
            <Popover open={open} onOpenChange={handleOpenMenu}>
                <PopoverTrigger asChild>
                    <button ref={buttonRef} className="btn-primary max-w-[48px] !px-3 block">
                        {!icon ? <Bars3Icon className="w-6 h-6 text-white"/> : icon}
                    </button>
                </PopoverTrigger>
                {isVisible && (
                    <PopoverPortal>
                        <PopoverContent
                            side={side}
                            align={align}
                            sideOffset={8}
                            onClick={handleClick}
                            collisionPadding={collisionPadding}
                            className="flex flex-col items-center justify-center gap-2 p-2 relative bg-white rounded-lg border border-solid border-[#494949] z-[1000] lg:hidden"
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
