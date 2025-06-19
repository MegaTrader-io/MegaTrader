'use client'

import React, {createContext, useContext, useEffect, useState} from 'react';
import {Account} from "@/commons/interfaces";
import {accounts} from "@/commons/data";
import {useLoading} from "@/context/LoadingContext";
import Intercom, {shutdown} from '@intercom/messenger-js-sdk';
import {usePathname} from "next/navigation";

interface User {
    id: string;
    name: string;
    email: string;
    intercomUserJwt: string;
    createdAt: number;
}

interface AccountContextType {
    selectedAccount: Account,
    setSelectedAccount: (account: Account) => void,
    isLoadingAccount: boolean
    fetchAccount: (account: Account) => Promise<{ account: Account }>
    user: User | null;
    setUser: (user: User | null) => void;
}

const AccountContext = createContext<AccountContextType | undefined>(undefined);

export const AccountProvider = ({children}: { children: React.ReactNode }) => {
    const [selectedAccount, setSelectedAccountState] = useState<Account>(accounts[0])
    const [user, setUser] = useState<User | null>(null);
    const {setLoading, isLoading} = useLoading();

    const setSelectedAccount = (account: Account) => {
        setSelectedAccountState(account);
    }

    const fetchAccount = (account: Account): Promise<{ account: Account }> => {
        setLoading(true)
        return new Promise<{ account: Account }>((resolve) => {
            setTimeout(() => {
                setLoading(false);
                resolve({account});
            }, 2000);
        });
    }

    useEffect(() => {
        const initializeIntercom = async () => {
            const isLoggedIn = localStorage.getItem('isLoggedIn');
            if (!isLoggedIn) {
                shutdown();
                return;
            }

            if (typeof window !== 'undefined' && window.__intercomInitialized) {
                // Ya está activo, no hagas nada
                return;
            }

            try {
                const res = await fetch('/api/user/me');
                const data = await res.json();

                const userData: User = {
                    id: data.id,
                    name: data.name,
                    email: data.email,
                    createdAt: Math.floor(new Date(data.createdAt).getTime() / 1000),
                    intercomUserJwt: data.intercomUserJwt,
                };

                setUser(userData);

                Intercom({
                    app_id: 'izt54gd4',
                    user_id: userData.id,
                    name: userData.name,
                    email: userData.email,
                    created_at: userData.createdAt,
                    intercom_user_jwt: userData.intercomUserJwt,
                });

                window.__intercomInitialized = true;
            } catch (err) {
                console.error('[AccountContext] unable to load Intercom:', err);
            }
        };

        void initializeIntercom();
    }, []);

    return <AccountContext.Provider
        value={{
            selectedAccount,
            fetchAccount,
            isLoadingAccount: isLoading,
            setSelectedAccount,
            user,
            setUser
        }}>
        {children}
    </AccountContext.Provider>
}

export const useAccount = () => {
    const context = useContext(AccountContext);
    if (!context) {
        throw new Error('useAccount must be used within an AccountProvider')
    }

    return context;
}

