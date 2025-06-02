import React from 'react';
import clsx from 'clsx';

interface Props extends React.SelectHTMLAttributes<HTMLSelectElement> {
    className?: string;
    onChange?: (ev: React.ChangeEvent<HTMLSelectElement>) => void;
    children?: React.ReactNode;
}

const Select: React.FC<Props> = ({onChange, children, className, ...props}) => {
    return (
        <div className="relative w-full">
            <select
                {...props}
                onChange={onChange}
                className={clsx(
                    "w-full py-3 px-4 pr-10 rounded-xl border border-neutral-700 text-stone-400 bg-[#1e1e1e]/70 appearance-none focus:outline-none",
                    className
                )}
            >
                {children}
            </select>
            <div className="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 15L7 10H17L12 15Z" fill="white"/>
                </svg>
            </div>
        </div>
    );
};

export default Select;
