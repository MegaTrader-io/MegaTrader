'use client';

import React, {
    createContext,
    useContext,
    ReactNode
} from 'react';

interface FlashContextType {
    getFlash: () => string | null;
    setFlash: (msg: string) => void;
}

const FlashContext = createContext<FlashContextType | undefined>(undefined);

export function FlashProvider({children}: { children: ReactNode }) {
    const setFlash = (msg: string) => {
        try {
            sessionStorage.setItem('flash', msg);
        } catch (err) {
            console.error('Flash: unable tu write session flash inside a sessionStorage', err);
        }
    };

    const getFlash = () => {
        const flash = sessionStorage.getItem('flash');

        sessionStorage.removeItem('flash');

        return flash;
    }

    return (
        <FlashContext.Provider value={{getFlash, setFlash}}>
            {children}
        </FlashContext.Provider>
    );
}

export function useFlash() {
    const ctx = useContext(FlashContext);
    if (!ctx) {
        throw new Error('useFlash must be wrapper inside a <FlashProvider>');
    }
    return ctx;
}
