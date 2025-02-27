'use client';

import React, {createContext, useContext, useState, ReactNode} from 'react';

interface LoadingBetweenPagesContextProps {
    isLoading: boolean;
    setLoading: (value: boolean) => void;
}

const LoadingBetweenPagesContext = createContext<LoadingBetweenPagesContextProps>({
    isLoading: false,
    setLoading: () => {
    },
});

export const LoadingBetweenPagesProvider = ({children}: { children: ReactNode }) => {
    const [isLoading, setIsLoading] = useState(false);

    const setLoading = (value: boolean) => {
        setIsLoading(value);
    };

    return (
        <LoadingBetweenPagesContext.Provider value={{isLoading, setLoading}}>
            {children}
        </LoadingBetweenPagesContext.Provider>
    );
};

export const useLoadingBetweenPages = () => useContext(LoadingBetweenPagesContext);
