'use client';

import React from 'react';
import Skeleton from "@/components/Skeleton";

function SkeletonAffiliate() {
    const animated = false;
    return (
        <>
            <div className="space-y-4 md:space-y-0 w-full md:grid md:grid-cols-2 lg:flex lg:justify-around gap-4">
                {Array(4).fill('').map((_, index) => (
                    <Skeleton key={index} className={'min-w-[300px] h-[124px] rounded-2xl'} animated={animated}/>
                ))}
            </div>

            <Skeleton className={'w-full h-[80px] rounded-2xl'} animated={animated}/>

            <div className='space-y-4 lg:space-y-0 lg:grid lg:grid-cols-12 lg:gap-4 w-full'>
                <Skeleton className={'w-full h-[332px] rounded-2xl lg:col-span-7'} animated={animated}/>
                <Skeleton className={'w-full h-[332px] rounded-2xl lg:col-span-5'} animated={animated}/>
            </div>

            <Skeleton className={'w-full h-[441px] rounded-2xl'} animated={animated}/>
        </>
    );
}

export default SkeletonAffiliate;