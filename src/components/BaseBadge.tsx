import React from "react";
import {MgProps} from "@/commons/interfaces";
import clsx from "clsx";


interface BadgeProps extends MgProps {
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
    const baseStyles = 'text-[#131210] font-medium';
    const roundedStyles = shape === 'rounded'
        ? 'rounded-2xl'
        : 'rounded-xl';

    const sizeStyles = size === 'md'
        ? `h-7 px-3 py-0.5 text-xs`
        : `h-4 px-2 text-[10px]`;

    const styles = {
        primary: {
            container: 'bg-primary justify-center items-center gap-2.5 inline-flex',
        },
        secondary: {
            container: 'bg-teal-500 justify-center items-center gap-2.5 inline-flex',
        },
        error: {
            container: 'bg-rose-500 justify-center items-center gap-2.5 inline-flex',
        },
        info: {
            container: 'bg-blue-500 justify-center items-center gap-2.5 inline-flex',
        }
    }[variant]


    return (
        <div  {...props}
              className={clsx(
                  baseStyles,
                  roundedStyles,
                  sizeStyles,
                  styles.container,
                  className
              )}>
            {children}
        </div>
    )
}

export default BaseBadge;