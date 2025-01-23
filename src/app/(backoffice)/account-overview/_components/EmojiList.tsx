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

const EmojiList: React.FC = ({emojiId}: { emojiId?: number | null }) => {
    const [selectedEmoji, setSelectedEmoji] = useState<Emoji | null>(emojis.find(i => i.id === emojiId) || null);

    const handleSelectEmoji = (emoji: Emoji) => {
        setSelectedEmoji(emoji);
    };

    return (
        <div className="flex justify-between py-2">
            {emojis.map((emoji) => (
                <button
                    key={emoji.id}
                    className={` ${
                        selectedEmoji?.id === emoji.id ? "text-primary" : "text-[#57534E]"
                    }`}
                    onClick={() => handleSelectEmoji(emoji)}
                >
                    {emoji.icon}
                </button>
            ))}
        </div>
    );
};

export default EmojiList;
