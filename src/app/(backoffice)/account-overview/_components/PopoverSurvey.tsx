import React, {useState} from 'react';
import {XMarkIcon} from "@heroicons/react/16/solid";
import * as BasePopover from "@radix-ui/react-popover";
import {PopoverContent} from "@radix-ui/react-popover";
import EmojiList from "@/app/(backoffice)/account-overview/_components/EmojiList";
import Button from "@/components/BaseButton";
import {Emoji} from "@/commons/interfaces";
import TextArea from "@/components/TextArea";

interface SurveyState {
    id?: number | undefined,
    emojiId?: number | undefined
    simpleQuestion?: boolean | undefined
    note?: string | undefined
}

const defaultData = {
    id: undefined,
    emojiId: undefined,
    simpleQuestion: undefined,
    note: undefined,
}

function PopoverSurvey({surveyData = defaultData}: { surveyData?: SurveyState }) {
    const [survey, setSurvey] = useState<SurveyState>(surveyData)

    function changeEmoji(emoji: Emoji) {
        setSurvey(survey => {
            return {...survey, emojiId: emoji.id}
        })
    }

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
                <EmojiList emojiId={survey.emojiId} onClick={changeEmoji}/>
                <p className="text-white  text-base font-normal leading-normal">
                    Did I follow my trading plan today?
                </p>

                <div className="flex gap-2">
                    <Button className="w-full"
                            onClick={() => {
                                setSurvey(survey => {
                                    return {...survey, simpleQuestion: true}
                                })
                            }}
                            variant={survey.simpleQuestion !== undefined && survey.simpleQuestion ? 'primary' : 'dark'}>Yes</Button>
                    <Button className="w-full"
                            onClick={() => {
                                setSurvey(survey => {
                                    return {...survey, simpleQuestion: false}
                                })
                            }}
                            variant={survey.simpleQuestion !== undefined && !survey.simpleQuestion ? 'primary' : 'dark'}>No</Button>
                </div>

                <div>
                    <TextArea
                        className="h-[100px] px-4 py-3 w-full bg-[#1e1e1e]/70 rounded-xl border border-neutral-700 focus:out"
                        placeholder="What's the most important thing I learn today?"
                        name="note">
                    </TextArea>
                </div>
                <Button className="w-full" size="sm">
                    SAVE
                </Button>
            </div>
            <BasePopover.Arrow className="fill-neutral-700"/>
        </PopoverContent>
    );
}

export default PopoverSurvey;