import React, { forwardRef, InputHTMLAttributes } from 'react'

interface CheckboxProps extends InputHTMLAttributes<HTMLInputElement> {
    label?: string
}

export const InputCheckbox = forwardRef<HTMLInputElement, CheckboxProps>(
    ({ className = '', label, ...props }, ref) => {
        return (
            <label className="flex items-center cursor-pointer">
                <div className="relative">
                    <input
                        type="checkbox"
                        className="sr-only"
                        ref={ref}
                        {...props}
                    />
                    <div className={`w-4 h-4 border border-gray-500 rounded-sm ${className}`}>
                        <svg
                            className="w-3 h-3 text-orange-400 hidden"
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
                {label && <span className="ml-2 text-sm text-gray-400">{label}</span>}
            </label>
        )
    }
)

InputCheckbox.displayName = 'Checkbox'

