'use client';

import React from 'react';
import {useLoading} from '@/context/LoadingContext';

const LoadingOverlay = () => {
    const {isLoading} = useLoading();

    if (!isLoading) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
            <div className="loader"></div>
        </div>
    );
};

export default LoadingOverlay;
