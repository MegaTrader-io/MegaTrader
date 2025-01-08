import React from 'react';

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
                                                 ...props
                                             }) => {
    const hasError = Boolean(errorMessage);

    return (
        <div className="w-full">
            <input
                {...props}
                placeholder={placeholder}
                name={name}
                id={name}
                aria-label={placeholder || name}
                aria-invalid={hasError}
                aria-describedby={hasError ? `${name}-error` : undefined}
                className={`h-12 px-4 py-3 bg-[#1e1e1e]/70 rounded-xl border w-full focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                    hasError ? 'border-red-500' : 'border-neutral-700'
                } ${className}`}
            />
            {hasError && (
                <span
                    id={`${name}-error`}
                    className="text-sm text-red-500 mt-1 block"
                >
          {errorMessage}
        </span>
            )}
        </div>
    );
};

export default InputText;
