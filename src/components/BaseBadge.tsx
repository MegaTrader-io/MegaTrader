import React from "react";
import {MgProps} from "@/commons/interfaces";
import clsx from "clsx";


export interface BadgeProps extends MgProps {
    size?: 'md' | 'sm'
    variant?: 'primary' | 'secondary' | 'error' | 'info';
    shape?: 'rounded' | 'pill';
}

const BaseBadge = ({
                       size = 'md',
                       shape = 'rounded',
                       variant = 'secondary',
                       className = '',
                       children,
                       ...props
                   }: BadgeProps) => {
    const baseStyles = 'text-[#131210] leading-normal font-medium uppercase justify-center items-center gap-2.5 inline-flex';
    const roundedStyles = {
        'md': {
            'rounded': 'rounded-lg',
            'pill': 'rounded-2xl',
        },
        'sm': {
            'rounded': 'rounded',
            'pill': 'rounded-xl',
        }
    }[size][shape]

    const sizeStyles = size === 'md'
        ? `h-7 px-3 py-0.5 text-xs`
        : `h-4 px-2 text-[10px]`;

    const styles = {
        primary: {
            bgColor: 'bg-primary',
        },
        secondary: {
            bgColor: 'bg-teal-500',
        },
        error: {
            bgColor: 'bg-rose-500',
        },
        info: {
            bgColor: 'bg-blue-500',
        }
    }[variant]


    return (
        <div  {...props}
              className={clsx(
                  baseStyles,
                  roundedStyles,
                  sizeStyles,
                  styles.bgColor,
                  className
              )}>
            {children}
        </div>
    )
}

export default BaseBadge;