import React, {PropsWithChildren} from 'react';
import {Popover, PopoverContent, PopoverPortal, PopoverTrigger, PopoverArrow} from "@radix-ui/react-popover";
import {Bars3Icon} from "@heroicons/react/24/solid";

interface Props extends PropsWithChildren {
    className?: string,
    icon?: React.ReactNode
}

function PopoverMenu({className, children, icon}: Props) {

    return (
        <div className={className}>
            <Popover>
                <PopoverTrigger asChild>
                    <button className="btn-primary block">
                        {!icon && <Bars3Icon className="w-6 h-6 text-white"/>}
                        {icon && icon}
                    </button>
                </PopoverTrigger>
                <PopoverPortal>
                    <PopoverContent
                        className="flex flex-col items-center justify-center gap-2 p-2 relative bg-white rounded-lg border border-solid border-[#494949] z-[1000] lg:hidden">
                        {children}
                        <PopoverArrow width={26} height={14} className="fill-white"/>
                    </PopoverContent>
                </PopoverPortal>
            </Popover>
        </div>
    );
}

export default PopoverMenu;