import React, {useState} from 'react';
import {AnimatePresence, motion} from 'framer-motion';

type ChartBarProps = {
    data: number[];
    labels: string[];
};

const ChartBar: React.FC<ChartBarProps> = ({data, labels}) => {
    const [selectedIndex, setSelectedIndex] = useState<number | null>(null);
    const [hoverIndex, setHoverIndex] = useState<number | null>(null);

    const maxValue = Math.max(...data);
    const barColor = '#14B8A6';
    const defaultColor = '#404040';
    const hoverColor = '#78716C';

    return (
        <div className="flex items-end justify-between w-full h-full gap-2">
            {data.map((value, index) => {
                const heightPercent = (value / maxValue) * 100;
                const isSelected = selectedIndex === index;
                const isHovered = hoverIndex === index;

                return (
                    <div
                        key={index}
                        id={`bar_${index}`}
                        className="grid grid-rows-[auto_36px] gap-2 items-end group h-[290px]"
                        onMouseEnter={() => setHoverIndex(index)}
                        onMouseLeave={() => setHoverIndex(null)}
                        onClick={() => setSelectedIndex(index)}
                    >
                        <div className="h-full items-end flex">
                            <div
                                className="w-9 rounded-full transition-all duration-300 relative"
                                style={{
                                    height: `${heightPercent}%`,
                                    backgroundColor: isSelected
                                        ? barColor
                                        : isHovered
                                            ? hoverColor
                                            : defaultColor,
                                }}
                            >
                                <AnimatePresence>
                                    {(isHovered || isSelected) && (
                                        <motion.div
                                            initial={{opacity: 0, y: 10}}
                                            animate={{opacity: 1, y: 0}}
                                            exit={{opacity: 0, y: 10}}
                                            className={`absolute transform z-10 !translate-x-[-25%] translate-y-[-36px] px-3 py-1 rounded-full text-sm font-bold ${
                                                isSelected ? 'bg-teal-500 text-black' : 'bg-neutral-200 text-black'
                                            }`}
                                        >
                                            ${value.toLocaleString()}
                                        </motion.div>
                                    )}
                                </AnimatePresence>

                            </div>
                        </div>


                        <div
                            className={`mt-2 text-base w-9 h-9 rounded-full font-medium flex items-center justify-center transition-colors ${
                                isSelected
                                    ? 'bg-teal-500 text-black'
                                    : 'bg-neutral-700 text-white group-hover:bg-neutral-600'
                            }`}
                        >
                            {labels[index]}
                        </div>
                    </div>
                );
            })}
        </div>
    );
};

export default ChartBar;
