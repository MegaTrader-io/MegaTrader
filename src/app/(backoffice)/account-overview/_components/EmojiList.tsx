import React, {useState} from "react";
import {Emoji} from "@/commons/interfaces";
import VeryHappy from "@/app/(backoffice)/account-overview/_components/Emojis/VeryHappy";
import Happy from "@/app/(backoffice)/account-overview/_components/Emojis/Happy";
import Neutral from "@/app/(backoffice)/account-overview/_components/Emojis/Neutral";
import Sad from "@/app/(backoffice)/account-overview/_components/Emojis/Sad";
import VerySad from "@/app/(backoffice)/account-overview/_components/Emojis/VerySad";

export const emojis: Emoji[] = [
    {id: 1, name: "very_happy", description: "Very Happy", icon: <VeryHappy/>},
    {id: 2, name: "happy", description: "Happy", icon: <Happy/>},
    {id: 3, name: "neutral", description: "Neutral", icon: <Neutral/>},
    {id: 4, name: "sad", description: "Sad", icon: <Sad/>},
    {id: 5, name: "very_sad", description: "Very Sad", icon: <VerySad/>},
];

interface Props {
    emojiId?: number | undefined,
    onClick?: (emoji: Emoji) => void,
    disabled?: boolean,
    errorMessage?: string;
}

const EmojiList: React.FC<Props> = ({emojiId = undefined, onClick, disabled = false, errorMessage = ''}) => {
    const [selectedEmoji, setSelectedEmoji] = useState<Emoji | undefined>(emojis.find(i => i.id === emojiId));
    const hasError = Boolean(errorMessage);

    const handleSelectEmoji = (emoji: Emoji) => {
        if (!disabled) {
            return;
        }

        if (onClick) {
            onClick(emoji)
        }

        setSelectedEmoji(emoji);
    };

    return (
        <div>
            <div className="flex justify-between py-2">
                {emojis.map((emoji) => (
                    <button
                        disabled={!disabled}
                        key={emoji.id}
                        className={`disabled:cursor-not-allowed disabled:opacity-50 ${
                            selectedEmoji?.id === emoji.id ? "text-primary" : "text-[#57534E]"
                        }`}
                        onClick={() => handleSelectEmoji(emoji)}
                    >
                        {emoji.icon}
                    </button>
                ))}
            </div>

            {hasError && (
                <span
                    className="text-rose-500 text-xs mt-4 leading-tight"
                >
                    {errorMessage}
                </span>
            )}
        </div>
    );
};

export default EmojiList;
