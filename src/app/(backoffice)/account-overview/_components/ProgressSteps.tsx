import React from "react";

const ProgressSteps = ({variant = 'secondary', currentStep, totalSteps}: {
    variant?: 'secondary' | 'error';
    currentStep: number,
    totalSteps: number
}) => {
    let color = '#14B8A6'

    if (variant === 'error') {
        color = '#F43F5E';
    }

    const progressPercentage = currentStep === 0 ? 100 : (currentStep / totalSteps) * 100;

    return (
        <div className="relative flex items-center justify-center w-12 h-12">
            <svg className="w-full h-full" viewBox="0 0 100 100">
                <circle
                    cx="50"
                    cy="50"
                    r="40"
                    stroke="#333"
                    strokeWidth="10"
                    fill="none"
                />
                <circle
                    cx="50"
                    cy="50"
                    r="40"
                    stroke={color}
                    strokeWidth="10"
                    fill="none"
                    strokeDasharray="251.2"
                    strokeDashoffset={251.2 - (progressPercentage / 100) * 251.2}
                    strokeLinecap="round"
                    transform="rotate(-90 50 50)"
                />
            </svg>
            <div className='hidden text-[#14B8A6]'/>
            <div className='hidden text-[#F43F5E]'/>
            <div className="absolute text-xs font-bold text-stone-400">
                <span className={`text-[${color}] leading-tight`}>{currentStep}</span> / {totalSteps}
            </div>
        </div>
    );
};

export default ProgressSteps;
