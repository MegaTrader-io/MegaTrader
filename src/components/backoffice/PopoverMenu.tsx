import React, {PropsWithChildren} from 'react';
import {Popover, PopoverContent, PopoverPortal, PopoverTrigger, PopoverArrow} from "@radix-ui/react-popover";
import {Bars3Icon} from "@heroicons/react/24/solid";

interface Props extends PropsWithChildren {
    className?: string
}

function PopoverMenu({className, children}: Props) {

    return (
        <div className={className}>
            <Popover>
                <PopoverTrigger asChild>
                    <button
                        className="btn-primary block"
                        onClick={() => {
                        }}
                    >
                        <Bars3Icon className="w-6 h-6 text-white"/>
                    </button>
                </PopoverTrigger>
                <PopoverPortal>
                    <PopoverContent
                        className="flex flex-col items-center justify-center gap-2 p-2 relative bg-white rounded-lg border border-solid border-[#494949] z-[1000]">
                        {children}
                        <PopoverArrow width={26} height={14} className="fill-white"/>
                    </PopoverContent>
                </PopoverPortal>
            </Popover>
        </div>
    );
}

export default PopoverMenu;