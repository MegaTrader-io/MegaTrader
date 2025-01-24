import React, {useRef, useState} from 'react';
import {XMarkIcon} from "@heroicons/react/16/solid";
import * as BasePopover from "@radix-ui/react-popover";
import {PopoverContent} from "@radix-ui/react-popover";
import EmojiList from "@/app/(backoffice)/account-overview/_components/EmojiList";
import Button from "@/components/BaseButton";
import {Emoji, SurveyState} from "@/commons/interfaces";
import TextArea from "@/components/TextArea";
import clsx from "clsx";

type editableFields = Pick<SurveyState, "emojiId" | "simpleQuestion" | "note">

const surveyDefaultData = {
    emojiId: undefined,
    simpleQuestion: undefined,
    note: null,
}

function PopoverSurvey({surveyData, onClick}: {
    surveyData?: SurveyState | null,
    onClick: (survey: SurveyState) => void
}) {
    const saveBtn = useRef<HTMLButtonElement | null>(null);
    const [survey, setSurvey] = useState<SurveyState>(surveyData || surveyDefaultData)
    const [canEdit, setCanEdit] = useState<boolean>(surveyData?.id === undefined)

    function updateState<K extends keyof editableFields>(field: K, value: editableFields[K]) {
        setSurvey(survey => {
            return {...survey, [field]: value}
        })
    }

    function changeEmoji(emoji: Emoji) {
        updateState('emojiId', emoji.id)
    }

    function saveSurvey() {
        const isNew: boolean = survey.id === undefined;
        if (isNew) {
            console.info('is created');
            onClick({...survey, id: (new Date()).getTime()} as SurveyState)
        } else {
            console.info('is edited');
            onClick({...survey})
        }

        setCanEdit(value => !value)
        console.info(survey);
        saveBtn.current?.blur();
    }

    return (
        <PopoverContent
            className="flex flex-col items-center justify-center gap-2 p-4 relative bg-[#131210] rounded-2xl border border-solid border-neutral-700">
            <div className="space-y-2">
                <div className="flex justify-between gap-2 w-full">
                    <p className="text-white text-base font-normal leading-normal">
                        How did it feel today?
                    </p>
                    <BasePopover.Close aria-label="Close">
                        <XMarkIcon className="text-white h-6 w-6"/>
                    </BasePopover.Close>
                </div>
                <EmojiList emojiId={survey.emojiId} onClick={changeEmoji} disabled={canEdit}/>
                <p className="text-white  text-base font-normal leading-normal">
                    Did I follow my trading plan today?
                </p>

                <div className="flex gap-2">
                    <Button
                        className={clsx('w-full', {'!bg-primary': !canEdit && survey.simpleQuestion !== undefined && survey.simpleQuestion})}
                        disabled={!canEdit}
                        onClick={() => {
                            setSurvey(survey => {
                                return {...survey, simpleQuestion: true}
                            })
                        }}
                        variant={survey.simpleQuestion !== undefined && survey.simpleQuestion ? 'primary' : 'dark'}>Yes</Button>


                    <Button
                        className={clsx('w-full', {'!bg-primary': survey.simpleQuestion !== undefined && !survey.simpleQuestion})}
                        disabled={!canEdit}
                        onClick={() => {
                            setSurvey(survey => {
                                return {...survey, simpleQuestion: false}
                            })
                        }}
                        variant={survey.simpleQuestion !== undefined && !survey.simpleQuestion ? 'primary' : 'dark'}>No</Button>
                </div>

                <div>
                    <TextArea
                        disabled={!canEdit}
                        defaultValue={survey.note}
                        onChange={(e) => updateState('note', e.target.value)}
                        className="h-[100px] px-4 py-3 w-full bg-[#1e1e1e]/70 rounded-xl border border-neutral-700 focus:out"
                        placeholder="What's the most important thing I learn today?"
                        name="note">
                    </TextArea>
                </div>
                <Button
                    ref={saveBtn}
                    onClick={saveSurvey}
                    styleType={canEdit ? 'filled' : 'text'}
                    variant={canEdit ? 'primary' : 'dark'}
                    className="w-full" size="sm">
                    {canEdit ? 'SAVE' : 'EDIT'}
                </Button>
            </div>
            <BasePopover.Arrow className="fill-neutral-700"/>
        </PopoverContent>
    );
}

export default PopoverSurvey;