import React, {ElementType, HTMLAttributes, ReactNode} from 'react';

interface LinkProps extends HTMLAttributes<HTMLElement> {
    children: ReactNode;
    className?: string;
    href?: string
    as?: ElementType;
}

export default function Link({
                                 children,
                                 className = '',
                                 as: Component = 'a',
                                 ...props
                             }: LinkProps) {
    return (
        <Component
            className={`h-12 px-4 py-3 bg-[#292524] rounded-xl border border-neutral-700 justify-center items-center gap-2 inline-flex text-neutral-50 text-base font-normal uppercase leading-normal ${className}`}
            {...props}
        >
            {children}
        </Component>
    );
}
