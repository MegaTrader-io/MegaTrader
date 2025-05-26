'use client';

import React, {forwardRef, InputHTMLAttributes, useState} from 'react';
import {EyeIcon, EyeSlashIcon, MagnifyingGlassIcon} from "@heroicons/react/16/solid";

interface InputTextProps extends InputHTMLAttributes<HTMLInputElement> {
    className?: string;
    placeholder?: string;
    name: string;
    errorMessage?: string|null;
    searchInput?: boolean
}

const InputText = forwardRef<HTMLInputElement | null, InputTextProps>(
    ({
         onChange,
         className = '',
         placeholder = '',
         name,
         errorMessage = '',
         searchInput = false,
         ...props
     }, ref) => {
        const [type, setType] = useState(props.type || 'text');
        const EyeIconComponent = props.type === 'password' && type === 'password' ? EyeSlashIcon : EyeIcon;

        const [internalValue, setInternalValue] = useState(props.value || '');
        const hasError = Boolean(errorMessage);

        const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
            if (onChange) {
                onChange(event);
            } else {
                setInternalValue(event.target.value);
            }
        };

        return (
            <div className="w-full">
                <div className="flex items-center relative">
                    <input
                        ref={ref}
                        {...props}
                        type={type}
                        value={onChange ? props.value : internalValue}
                        onChange={handleChange}
                        placeholder={placeholder}
                        name={name}
                        id={name}
                        aria-label={placeholder || name}
                        aria-invalid={hasError}
                        aria-describedby={hasError ? `${name}-error` : undefined}
                        className={`h-12 px-4 py-3 bg-[#1e1e1e]/70 disabled:bg-stone-600 disabled:text-stone-400 placeholder:text-neutral-700 rounded-xl border w-full font-medium focus:border-transparent text-base focus:outline-none focus:ring-1 ${
                            hasError
                                ? 'ring-1 ring-red-500 text-red-500 border-transparent'
                                : 'hover:border-white text-stone-400 border-neutral-700 focus:ring-mgt-primary'
                        } ${className}`}
                    />

                    {props.type === 'password' && (
                        <EyeIconComponent className="h-6 w-6 text-[#A8A29E] absolute right-4 cursor-pointer"
                                          onClick={() => {
                                              setType(prev => prev === 'password' ? 'text' : 'password');
                                          }}/>
                    )}

                    {searchInput && (
                        <MagnifyingGlassIcon className="h-6 w-6 text-[#A8A29E] absolute right-4 cursor-pointer"/>
                    )}
                </div>

                {hasError && (
                    <span
                        id={`${name}-error`}
                        className="text-rose-500 text-xs mt-4 leading-tight"
                    >
                    {errorMessage}
                </span>
                )}
            </div>
        );
    }
);

InputText.displayName = 'InputText';

export default InputText;
