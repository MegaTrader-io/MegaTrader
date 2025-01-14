'use client';

import React, {useState} from 'react';
import {EyeSlashIcon, EyeIcon} from "@heroicons/react/16/solid";

interface InputTextProps extends React.InputHTMLAttributes<HTMLInputElement> {
    className?: string;
    placeholder?: string;
    name: string;
    errorMessage?: string;
}


const InputText: React.FC<InputTextProps> = ({
                                                 className = '',
                                                 placeholder = '',
                                                 name,
                                                 errorMessage = '',
                                                 onChange,
                                                 ...props
                                             }) => {
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
                    className={`h-12 px-4 py-3 bg-[#1e1e1e]/70 placeholder:text-neutral-700 rounded-xl border w-full focus:border-transparent focus:outline-none focus:ring-1 ${
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
};

export default InputText;
