import React from "react";
import {MgProps} from "@/commons/interfaces";
import clsx from "clsx";

export type BadgeSize = 'md' | 'sm';

export interface BadgeProps extends MgProps {
    size?: BadgeSize;
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
    const baseStyles = 'text-[#131210] leading-normal font-bold uppercase justify-center items-center gap-2.5 inline-flex';
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
        ? `h-7 px-3 py-1 text-sm`
        : `h-6 px-2 py-1 text-sm`;

    const styles = {
        primary: {
            bgColor: 'bg-primary',
        },
        secondary: {
            bgColor: 'bg-secondary',
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