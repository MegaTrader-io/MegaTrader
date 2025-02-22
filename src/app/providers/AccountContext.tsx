'use client'

import React, {createContext, useContext, useState} from 'react';
import {Account} from "@/commons/interfaces";
import {accounts} from "@/commons/data";
import {useLoading} from "@/context/LoadingContext";

interface AccountContextType {
    selectedAccount: Account,
    setSelectedAccount: (account: Account) => void,
    isLoadingAccount: boolean
    fetchAccount: (account: Account) => Promise<{ account: Account }>
}

const AccountContext = createContext<AccountContextType | undefined>(undefined);

export const AccountProvider = ({children}: { children: React.ReactNode }) => {
    const [selectedAccount, setSelectedAccountState] = useState<Account>(accounts[0])
    const {setLoading, isLoading} = useLoading();

    const setSelectedAccount = (account: Account) => {
        setSelectedAccountState(account);
    }

    const fetchAccount = (account: Account): Promise<{ account: Account }> => {
        setLoading(true)
        return new Promise<{ account: Account }>((resolve) => {
            setTimeout(() => {
                setLoading(false);
                resolve({ account });
            }, 2000);
        });
    }

    return <AccountContext.Provider
        value={{selectedAccount, fetchAccount, isLoadingAccount: isLoading, setSelectedAccount}}>
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

