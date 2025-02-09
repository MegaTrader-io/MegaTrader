import React, {ElementType} from 'react';
import clsx from "clsx";

export function SkeletonTemplate({children}: { children?: React.ReactNode }) {
    return <div className="animate-pulse bg-[#1e1e1e]/70 w-full h-full">
        <div className="bg-slate-800/70 text-slate-800 w-full h-full">
            {children}
        </div>
    </div>
}

function Skeleton({as: Component = 'div', children, className, isLoading = false}: {
    as?: ElementType;
    children?: React.ReactNode | string,
    className?: string,
    isLoading?: boolean
}) {

    if (isLoading) {
        return <div className={clsx('h-full', className)}>
            <SkeletonTemplate/>
        </div>
    }

    return (
        <Component className={className}>
            {children}
        </Component>
    );
}

export default Skeleton;