'use client';

import React from 'react';
import {useLoadingBetweenPages} from "@/context/LoadingBetweenPagesContext";

const LoadingBetweenPagesOverlay = () => {
    const {isLoading} = useLoadingBetweenPages();

    if (!isLoading) return null;

    return (
        <div className="fixed inset-0 bg-[#131210]/90  flex justify-center items-center z-[9999999]">
            <div className="loader"></div>
        </div>
    );
};

export default LoadingBetweenPagesOverlay;
