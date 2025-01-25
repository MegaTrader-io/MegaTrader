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
    const [emojiError, setEmojiError] = useState("");
    const [questionError, setQuestionError] = useState("");

    function updateState<K extends keyof editableFields>(field: K, value: editableFields[K]) {
        setSurvey(survey => {
            return {...survey, [field]: value}
        })
    }

    function changeEmoji(emoji: Emoji) {
        setEmojiError('');

        updateState('emojiId', emoji.id)
    }

    function saveSurvey() {
        const isNew: boolean = survey.id === undefined;
        if (isNew) {
            const isInvalidForm = !survey.emojiId || survey.simpleQuestion === undefined;

            if (!survey.emojiId) {
                setEmojiError('This field is required');
            }

            if (survey.simpleQuestion === undefined) {
                setQuestionError('This field is required');
            }

            if (isInvalidForm) {
                return
            }

            onClick({...survey, id: (new Date()).getTime()} as SurveyState)
        } else {
            onClick({...survey})
        }

        setCanEdit(value => !value)
        console.info(survey);
        saveBtn.current?.blur();
    }

    return (
        <PopoverContent
            className="flex flex-col items-center justify-center gap-2 p-4 relative bg-[#131210] rounded-2xl border border-solid border-[#494949]">
            <div className="space-y-2">
                <div className="flex justify-between gap-2 w-full">
                    <p className="text-white text-base font-normal leading-normal">
                        How did it feel today?
                    </p>
                    <BasePopover.Close aria-label="Close">
                        <XMarkIcon className="text-white h-6 w-6"/>
                    </BasePopover.Close>
                </div>

                <EmojiList
                    emojiId={survey.emojiId}
                    onClick={changeEmoji}
                    disabled={canEdit}
                    errorMessage={emojiError}
                />

                <p className="text-white  text-base font-normal leading-normal">
                    Did I follow my trading plan today?
                </p>

                <div>
                    <div className="flex gap-2">
                        <Button
                            className={clsx('w-full', {
                                'disabled:!bg-primary disabled:!opacity-100': !canEdit && survey.simpleQuestion !== undefined && survey.simpleQuestion,
                                'disabled:!bg-stone-800 disabled:!text-white disabled:!opacity-100': survey.simpleQuestion !== undefined && !survey.simpleQuestion
                            })}
                            disabled={!canEdit}
                            onClick={() => {
                                setQuestionError('')
                                updateState('simpleQuestion', true)
                            }}
                            variant={survey.simpleQuestion !== undefined && survey.simpleQuestion ? 'primary' : 'dark'}>Yes</Button>
                        <Button
                            className={clsx('w-full', {
                                'disabled:!bg-primary disabled:!opacity-100': survey.simpleQuestion !== undefined && !survey.simpleQuestion,
                                'disabled:!bg-stone-800 disabled:!text-white disabled:!opacity-100': !canEdit && survey.simpleQuestion !== undefined && survey.simpleQuestion
                            })}
                            disabled={!canEdit}
                            onClick={() => {
                                setQuestionError('')
                                updateState('simpleQuestion', false)
                            }}
                            variant={survey.simpleQuestion !== undefined && !survey.simpleQuestion ? 'primary' : 'dark'}>No</Button>
                    </div>

                    {questionError && (
                        <span
                            className="text-rose-500 text-xs mt-4 leading-tight"
                        >
                    {questionError}
                </span>
                    )}
                </div>


                <div>
                    <TextArea
                        disabled={!canEdit}
                        defaultValue={survey.note || ''}
                        onChange={(e) => updateState('note', e.target.value)}
                        className={clsx('h-[100px] px-4 py-3 w-full bg-[#1e1e1e]/70 rounded-xl border border-neutral-700 focus:out', {
                            'disabled:!bg-[#1e1e1e]/70 disabled:text-stone-400': !canEdit
                        })}
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
            <BasePopover.Arrow asChild>
                <svg width="29" height="16" viewBox="0 1 29 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g filter="url(#filter0_d_4706_5257)">
                        <path d="M14.5 14L28.5 0H0.5L14.5 14Z" fill="#131210"/>
                    </g>
                    <defs>
                        <filter id="filter0_d_4706_5257" x="0.5" y="0" width="28" height="15.5" filterUnits="userSpaceOnUse" colorInterpolationFilters="sRGB">
                            <feFlood floodOpacity="0" result="BackgroundImageFix"/>
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
                            <feOffset dy="1.6"/>
                            <feComposite in2="hardAlpha" operator="out"/>
                            <feColorMatrix type="matrix" values="0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 0 0.25098 0 0 0 1 0"/>
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_4706_5257"/>
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_4706_5257" result="shape"/>
                        </filter>
                    </defs>
                </svg>
            </BasePopover.Arrow>
        </PopoverContent>
    );
}

export default PopoverSurvey;