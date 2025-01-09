'use client'

import React, {forwardRef, InputHTMLAttributes, useState} from 'react';

interface CheckboxProps extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
}

export const InputCheckbox = forwardRef<HTMLInputElement, CheckboxProps>(
    ({className = '', label, ...props}, ref) => {
        const [isChecked, setIsChecked] = useState(props.checked || false);

        const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
            setIsChecked(e.target.checked);
            if (props.onChange) {
                props.onChange(e);
            }
        };

        return (
            <label className="flex items-center cursor-pointer">
                <div className="relative">
                    <input
                        type="checkbox"
                        className="sr-only"
                        ref={ref}
                        {...props}
                        checked={isChecked}
                        onChange={handleChange}
                    />
                    <div
                        className={`w-4 h-4 border-[#ffb34a] border-2 rounded-sm flex items-center justify-center ${className}`}
                    >
                        <svg
                            className={`w-2 h-2 text-[#ffb34a] ${isChecked ? 'block' : 'hidden'}`}
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="4"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                        >
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                </div>
                {label && (
                    <span className="ml-2 text-white text-base font-normal leading-normal select-none">{label}</span>
                )}
            </label>
        );
    }
);

InputCheckbox.displayName = 'Checkbox';
