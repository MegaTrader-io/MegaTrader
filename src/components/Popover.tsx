import React, {PropsWithChildren, ReactNode} from "react";
import * as BasePopover from "@radix-ui/react-popover";
import {Cross2Icon} from "@radix-ui/react-icons";

interface PopoverProps extends PropsWithChildren {
    trigger: ReactNode;
}

const Popover: React.FC<PopoverProps> = ({trigger, children}) => {
    return (
        <BasePopover.Root>
            <BasePopover.Trigger asChild>
                {trigger}
            </BasePopover.Trigger>
            <BasePopover.Portal>
                <BasePopover.Content
                    className="w-[260px] rounded bg-white p-5 shadow-[0_10px_38px_-10px_hsla(206,22%,7%,.35),0_10px_20px_-15px_hsla(206,22%,7%,.2)] will-change-[transform,opacity] focus:shadow-[0_10px_38px_-10px_hsla(206,22%,7%,.35),0_10px_20px_-15px_hsla(206,22%,7%,.2)] data-[state=open]:data-[side=bottom]:animate-slideUpAndFade data-[state=open]:data-[side=left]:animate-slideRightAndFade data-[state=open]:data-[side=right]:animate-slideLeftAndFade data-[state=open]:data-[side=top]:animate-slideDownAndFade"
                    sideOffset={5}
                >
                    <div className="flex flex-col gap-2.5">
                        {children}
                    </div>
                    <BasePopover.Close
                        className="absolute right-[5px] top-[5px] inline-flex size-[25px] cursor-default items-center justify-center rounded-full text-violet11 outline-none hover:bg-violet4 focus:shadow-[0_0_0_2px] focus:shadow-violet7"
                        aria-label="Close"
                    >
                        <Cross2Icon/>
                    </BasePopover.Close>
                    <BasePopover.Arrow className="fill-white"/>
                </BasePopover.Content>
            </BasePopover.Portal>
        </BasePopover.Root>
    );
};

export default Popover;
