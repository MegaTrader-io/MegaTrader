'use client'

import React, {forwardRef, InputHTMLAttributes, useState} from 'react';

interface CheckboxProps extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    children?: React.ReactElement;
    onChange?: (e: React.ChangeEvent<HTMLInputElement>) => void
}

export const InputCheckbox = forwardRef<HTMLInputElement, CheckboxProps>(
    ({className = '', label, children = null, ...props}, ref) => {
        const [isChecked, setIsChecked] = useState(props.checked || false);

        const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
            setIsChecked(e.target.checked);
            if (props.onChange) {
                props.onChange(e);
            }
        };

        return (
            <label className="flex items-start cursor-pointer">
                <div className="relative mt-1 mr-2">
                    <input
                        type="checkbox"
                        className="sr-only"
                        ref={ref}
                        {...props}
                        checked={isChecked}
                        onChange={handleChange}
                    />
                    <div
                        className={`w-4 h-4 border-mgt-primary border-2 rounded-sm justify-center ${className}`}
                    >
                        <svg
                            className={`w-3 h-3 bg-primary text-black ${isChecked ? 'block' : 'hidden'}`}
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
                {children && children}
                {!children && label && (
                    <span className="text-white text-base font-normal leading-normal select-none">{label}</span>
                )}
            </label>
        );
    }
);

InputCheckbox.displayName = 'Checkbox';
