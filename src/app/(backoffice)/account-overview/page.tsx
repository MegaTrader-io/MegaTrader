'use client';

import Card from "@/components/Card";
import {Button} from "@/components/Button";
import React, {useState} from "react";
import Link from "next/link";
import {PlusIcon} from "@heroicons/react/16/solid";
import Dropdown from "@/components/Dropdown";
import BaseBadge from "@/components/BaseBadge";
import Image from "next/image";

interface Account {
    id: number
    name: string
    active: boolean
}

const accounts: Account[] = [
    {id: 1, name: 'S1Sep2586479132', active: true},
    {id: 2, name: 'S1Sep2586479133', active: false},
];

export default function AccountOverView() {
    const [selectedAccount, setSelectedAccount] = useState(accounts[0]);

    return <>
        <Card className="w-full grid grid-cols-[auto_1fr_auto] items-center justify-between gap-4">
            <Dropdown
                items={accounts}
                value={selectedAccount}
                onChange={setSelectedAccount}
                renderButtonContent={(item) => (
                    <div className="flex gap-2 items-center">
                        <BaseBadge shape="rounded"
                                   variant={item.active ? 'secondary' : 'error'}
                                   size="md">{item.active ? 'ACTIVE' : 'INACTIVE'}
                        </BaseBadge>
                        <div className="text-stone-400 text-base font-normal truncate">{item.name}</div>
                        <Image src="/assets/images/arrow-down.svg" alt='selection' width={24} height={24}/>
                    </div>
                )}
                renderOptionContent={(item) => (
                    <>
                        <BaseBadge shape="rounded" variant={item.active ? 'secondary' : 'error'}
                                   size="md">{item.active ? 'ACTIVE' : 'INACTIVE'}</BaseBadge>
                        <div className="text-stone-400 text-base font-normal truncate">{item.name}</div>
                    </>
                )}
            />

            <div className="w-full">
                <Link href="#" className="text-base btn-link">
                    Manage Subscription
                </Link>
            </div>
            <div className="flex gap-2">
                <Button variant="dark">
                    RESET
                </Button>
                <Button variant="dark"
                        icon={<PlusIcon/>}
                        iconPosition="left">
                    CREATE NEW
                </Button>
            </div>
        </Card>
    </>
}