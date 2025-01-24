'use client';

import React, {useState} from 'react';

interface TextAreaProps extends React.InputHTMLAttributes<HTMLTextAreaElement> {
    className?: string;
    placeholder?: string;
    name: string;
    errorMessage?: string;
}


const TextArea: React.FC<TextAreaProps> = ({
                                               className = '',
                                               placeholder = '',
                                               name,
                                               errorMessage = '',
                                               onChange,
                                               ...props
                                           }) => {
    const [internalValue, setInternalValue] = useState(props.value || '');
    const hasError = Boolean(errorMessage);

    const handleChange = (event: React.ChangeEvent<HTMLTextAreaElement>) => {
        if (onChange) {
            onChange(event);
        } else {
            setInternalValue(event.target.value);
        }
    };

    return (
        <div className="w-full">
            <div className="flex items-center relative">
                <textarea
                    {...props}
                    value={onChange ? props.value : internalValue}
                    onChange={handleChange}
                    placeholder={placeholder}
                    name={name}
                    id={name}
                    aria-label={placeholder || name}
                    aria-invalid={hasError}
                    aria-describedby={hasError ? `${name}-error` : undefined}
                    className={`h-12 px-4 py-3 bg-[#1e1e1e]/70 disabled:cursor-not-allowed disabled:hover:border-transparent placeholder:text-neutral-700 rounded-xl border w-full focus:border-transparent focus:outline-none focus:ring-1 ${
                        hasError
                            ? 'ring-1 ring-red-500 text-red-500 border-transparent'
                            : 'hover:border-white text-stone-400 border-neutral-700 focus:ring-mgt-primary'
                    } ${className}`}
                />
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

export default TextArea;
