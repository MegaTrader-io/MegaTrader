'use client'

import React, {createContext, useContext, useState} from 'react';
import {Account} from "@/commons/interfaces";
import {accounts} from "@/commons/data";

interface AccountContextType {
    selectedAccount: Account,
    setSelectedAccount: (account: Account) => void,
    isLoadingAccount: boolean
}

const AccountContext = createContext<AccountContextType | undefined>(undefined);

export const AccountProvider = ({children}: { children: React.ReactNode }) => {
    const [selectedAccount, setSelectedAccountState] = useState<Account>(accounts[0])
    const [isLoadingAccount, setIsLoadingAccount] = useState<boolean>(true)

    const setSelectedAccount = (account: Account) => {
        console.info('account selected', account);

        setIsLoadingAccount(true)
        setTimeout(() => {
            setSelectedAccountState(account);
            setIsLoadingAccount(false)
        }, 2000)
    }

    return <AccountContext.Provider value={{selectedAccount, isLoadingAccount, setSelectedAccount}}>
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

