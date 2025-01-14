import BaseButton, {Icon, IconPosition} from './BaseButton';
import React, {FC} from "react";

interface ButtonProps {
    children?: React.ReactNode;
    type?: 'button' | 'submit' | 'reset';
    variant?: 'primary' | 'secondary' | 'light' | 'dark';
    styleType?: 'filled' | 'text';
    className?: string;
    disabled?: boolean;
    icon?: Icon;
    iconPosition?: IconPosition;
    size?: 'md' | 'sm';
}

export const Button: FC<ButtonProps> = ({
                                            children,
                                            variant = 'primary',
                                            type = 'button',
                                            styleType = 'filled',
                                            disabled = false,
                                            className = '',
                                            icon,
                                            iconPosition,
                                            size = 'md'
                                        }) => {
    return (
        <BaseButton
            type={type}
            variant={variant}
            styleType={styleType}
            className={className}
            icon={icon}
            iconPosition={iconPosition}
            disabled={disabled}
            size={size}
        >
            {children}
        </BaseButton>
    );
};
