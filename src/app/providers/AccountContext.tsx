'use client';

import React, {createContext, useContext, useEffect, useState} from 'react';
import {Account} from "@/commons/interfaces";
import {accounts} from "@/commons/data";
import {useLoading} from "@/context/LoadingContext";
import Intercom, {shutdown} from '@intercom/messenger-js-sdk';
import {useRouter} from "next/navigation";

interface User {
    id: string;
    name: string;
    email: string;
    phone: string;
    intercomUserJwt: string;
    createdAt: number;
}

interface AccountContextType {
    selectedAccount: Account;
    setSelectedAccount: (account: Account) => void;
    isLoadingAccount: boolean;
    fetchAccount: (account: Account) => Promise<{ account: Account }>;
    user: User | null;
    setUser: (user: User | null) => void;
}

const AccountContext = createContext<AccountContextType | undefined>(undefined);

export const AccountProvider = ({children}: { children: React.ReactNode }) => {
    const [selectedAccount, setSelectedAccountState] = useState<Account>(accounts[0]);
    const [user, setUser] = useState<User | null>(null);
    const [isVerified, setIsVerified] = useState<boolean | null>(null); // 👈 manejo de verificación
    const {setLoading, isLoading} = useLoading();
    const router = useRouter();

    const setSelectedAccount = (account: Account) => {
        setSelectedAccountState(account);
    };

    const fetchAccount = (account: Account): Promise<{ account: Account }> => {
        setLoading(true);
        return new Promise<{ account: Account }>((resolve) => {
            setTimeout(() => {
                setLoading(false);
                resolve({account});
            }, 2000);
        });
    };

    useEffect(() => {
        const initialize = async () => {
            const isLoggedIn = localStorage.getItem('isLoggedIn');
            if (!isLoggedIn) {
                shutdown();
                router.replace('/auth/login');
                setIsVerified(false);
                return;
            }

            setIsVerified(true);

            if (typeof window !== 'undefined' && window.__intercomInitialized) return;

            try {
                const res = await fetch('/api/user/me');
                const data = await res.json();

                const userData: User = {
                    id: data.id,
                    name: data.name,
                    email: data.email,
                    phone: data.phone,
                    createdAt: data.createdAt,
                    intercomUserJwt: data.intercomUserJwt,
                };

                setUser(userData);

                Intercom({
                    app_id: 'izt54gd4',
                    user_id: userData.id,
                    name: userData.name,
                    email: userData.email,
                    phone: userData.phone,
                    created_at_utc: userData.createdAt,
                    intercom_user_jwt: userData.intercomUserJwt,
                });

                window.__intercomInitialized = true;
            } catch (err) {
                console.error('[AccountContext] unable to load Intercom:', err);
            }
        };

        void initialize();

        return () => {
            shutdown();
        }
    }, []);

    if (isVerified !== true) return null;

    return (
        <AccountContext.Provider
            value={{
                selectedAccount,
                fetchAccount,
                isLoadingAccount: isLoading,
                setSelectedAccount,
                user,
                setUser,
            }}
        >
            {children}
        </AccountContext.Provider>
    );
};

export const useAccount = () => {
    const context = useContext(AccountContext);
    if (!context) {
        throw new Error('useAccount must be used within an AccountProvider');
    }
    return context;
};