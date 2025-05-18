import React from 'react';
import clsx from 'clsx';

type SkeletonProps = {
    variant?: 'box' | 'circle' | 'text';
    className?: string;
    width?: string;
    height?: string;
    borderRadius?: string;
    animationSpeed?: string;
    animated?: boolean;
};

export function SkeletonTemplate({children}: { children?: React.ReactNode }) {
    return <div className="animate-pulse bg-[#1e1e1e]/70 w-full h-full">
        <div className="bg-slate-800/70 text-slate-800 w-full h-full">
            {children}
        </div>
    </div>
}

const Skeleton: React.FC<SkeletonProps> = ({
                                               className = 'w-full h-4 rounded-2xl',
                                               animationSpeed = '2s',
                                               animated = true,
                                           }) => {

    return (
        <div
            className={clsx("relative overflow-hidden", className)}
            style={{
                backgroundImage: 'linear-gradient(to right,#363636 -100%, #1E1E1E 100%)',
            }}
        >
            {animated && (
                <div
                    className={clsx(
                        'absolute inset-0 bg-shimmer bg-shimmer-size animate-shimmer'
                    )}
                    style={{animationDuration: animationSpeed}}
                />
            )}
        </div>
    );
};

export default Skeleton;
