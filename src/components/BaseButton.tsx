import React, {JSX, forwardRef, PropsWithChildren} from 'react';
import clsx from "clsx";

export type Icon = JSX.Element;
export type IconPosition = 'left' | 'right' | '';

interface ButtonProps extends PropsWithChildren {
    type?: 'button' | 'submit' | 'reset';
    variant?: 'primary' | 'secondary' | 'light' | 'dark';
    styleType?: 'filled' | 'text';
    size?: 'sm' | 'md';
    className?: string;
    icon?: Icon;
    iconPosition?: IconPosition;
    disabled?: boolean;
    onClick?: () => void
    id?: string
}

const Content = ({
                     icon,
                     iconPosition,
                     iconClassName,
                     children
                 }: {
    icon?: Icon;
    iconPosition?: IconPosition;
    iconClassName: string;
    children?: React.ReactNode;
}) => {
    if (icon && !children) {
        return <span className={clsx(iconClassName)}>{icon}</span>;
    }

    return (
        <>
            {icon && iconPosition === 'left' && <span className={clsx(iconClassName, {'mr-2': children})}>{icon}</span>}
            {children}
            {icon && iconPosition === 'right' &&
                <span className={clsx(iconClassName, {'ml-2': children})}>{icon}</span>}
        </>
    );
};

const Button = forwardRef<HTMLButtonElement, ButtonProps>(({
                                                               type = 'button',
                                                               variant = 'primary',
                                                               styleType = 'filled',
                                                               size = 'md',
                                                               className = '',
                                                               disabled = false,
                                                               icon,
                                                               iconPosition,
                                                               onClick,
                                                               children,
                                                               id
                                                           }, ref) => {
    const baseStyles = className + ' btn-base';
    let iconClassName = '';

    let defaultPaddingMD = 'px-4';
    if (size === 'md' && icon) {
        iconClassName = 'w-6 h-6';

        if (iconPosition === 'left') {
            defaultPaddingMD = 'pl-3 pr-4';
        } else if (iconPosition === 'right') {
            defaultPaddingMD = 'pl-4 pr-3';
        }
    } else if (size === 'sm' && icon) {
        iconClassName = 'w-5 h-5';

        if (iconPosition === 'left') {
            defaultPaddingMD = 'pl-2 pr-3';
        } else if (iconPosition === 'right') {
            defaultPaddingMD = 'pl-3 pr-2';
        }
    }

    const sizeStyles = size === 'sm'
        ? `${defaultPaddingMD} py-1 text-xs font-bold leading-tight rounded`
        : `${defaultPaddingMD} py-3 text-base font-medium leading-normal rounded-xl`;

    const variantStyles = {
        primary: {
            filled: 'bg-primary hover:bg-yellow-600 text-black focus:ring-primary disabled:bg-stone-600 disabled:text-stone-800',
            text: 'text-primary hover:text-yellow-600 hover:bg-yellow-100 focus:ring-primary',
        },
        secondary: {
            filled: 'bg-secondary hover:bg-teal-600 text-white focus:ring-secondary',
            text: 'text-secondary hover:text-teal-600 hover:bg-teal-100 focus:ring-secondary',
        },
        light: {
            filled: 'bg-white hover:bg-gray-100 text-gray-900 focus:ring-gray-300',
            text: 'text-white hover:text-black hover:bg-gray-200 focus:ring-gray-300',
        },
        dark: {
            filled: 'bg-stone-800 hover:bg-stone-900 border border-neutral-700 text-white focus:ring-gray-700 disabled:bg-stone-600 disabled:text-stone-800',
            text: 'text-primary hover:bg-primary/10 focus:bg-primary focus:text-slate-950 focus:ring-primary',
        },
    };

    const disabledStyles = 'opacity-50 cursor-not-allowed';

    return (
        <button
            id={id}
            ref={ref}
            type={type}
            onClick={onClick}
            disabled={disabled}
            className={clsx(
                baseStyles,
                sizeStyles,
                variantStyles[variant][styleType],
                disabled && disabledStyles
            )}
        >
            <Content iconClassName={iconClassName} iconPosition={iconPosition} icon={icon}>
                {children}
            </Content>
        </button>
    );
});

Button.displayName = 'Button';

export default Button;
