import React from 'react';
import {XMarkIcon} from "@heroicons/react/16/solid";
import * as BasePopover from "@radix-ui/react-popover";
import {PopoverContent} from "@radix-ui/react-popover";
import EmojiList from "@/app/(backoffice)/account-overview/_components/EmojiList";
import Button from "@/components/BaseButton";

function PopoverSurvey() {


    return (
        <PopoverContent
            className="flex flex-col items-center justify-center gap-2 p-4 relative bg-[#131210] rounded-2xl border border-solid border-neutral-700">
            <div className="space-y-2">
                <div className="flex justify-between gap-2 w-full">
                    <p className="text-white text-base font-normal leading-normal">
                        How did it feel today?
                    </p>
                    <div>
                        <XMarkIcon className="text-white h-6 w-6"/>
                    </div>
                </div>
                <EmojiList/>
                <p className="text-white  text-base font-normal leading-normal">
                    Did I follow my trading plan today?
                </p>


                <div className="flex gap-2">
                    <Button className="w-full" variant={'dark'}>Yes</Button>
                    <Button className="w-full">No</Button>
                </div>


            </div>
            <BasePopover.Arrow className="fill-white"/>
        </PopoverContent>
    );
}

export default PopoverSurvey;