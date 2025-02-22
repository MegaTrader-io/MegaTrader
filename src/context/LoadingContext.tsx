'use client';

import React, {createContext, useContext, useState, ReactNode} from 'react';

interface LoadingContextProps {
    isLoading: boolean;
    setLoading: (value: boolean) => void;
}

const LoadingContext = createContext<LoadingContextProps>({
    isLoading: false,
    setLoading: () => {
    },
});

export const LoadingProvider = ({children}: { children: ReactNode }) => {
    const [isLoading, setIsLoading] = useState(false);

    const setLoading = (value: boolean) => {
        setIsLoading(value);
    };

    return (
        <LoadingContext.Provider value={{isLoading, setLoading}}>
            {children}
        </LoadingContext.Provider>
    );
};

export const useLoading = () => useContext(LoadingContext);
