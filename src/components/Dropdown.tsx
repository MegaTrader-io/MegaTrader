'use client'

import {Listbox, ListboxButton, ListboxOption, ListboxOptions} from '@headlessui/react'
import clsx from 'clsx'
import React, {useState} from 'react'
import BaseBadge from "@/components/BaseBadge";

const accounts = [
    {id: 1, name: 'S1Sep2586479132', active: true},
    {id: 2, name: 'S1Sep25864791323', active: false},
]

export default function Example() {
    const [selected, setSelected] = useState(accounts[1])

    return (
        <div className="h-12 w-[256px]">
            <Listbox disabled={false} value={selected} onChange={setSelected}>
                <ListboxButton
                    className={clsx(
                        'relative block w-full pl-4 pr-3 py-3 text-base font-normal leading-normal rounded-xl bg-stone-800 hover:bg-[#1e1e1e] border border-neutral-700 hover:border-transparent text-white  disabled:bg-stone-600 disabled:text-stone-800',
                        'focus:outline-none data-[focus]:outline-2 data-[focus]:-outline-offset-2 data-[focus]:outline-white/25'
                    )}
                >
                    <div className="flex gap-2 items-center">
                        <BaseBadge shape="rounded"
                                   variant={selected.active ? 'secondary' : 'error'}
                                   size="sm">{selected.active ? 'ACTIVE' : 'INACTIVE'}
                        </BaseBadge>
                        <div className="text-stone-400 text-base font-normal truncate">{selected.name}</div>
                        <div>
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <mask id="mask0_4588_2115" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                                      y="0" width="24" height="24">
                                    <rect width="24" height="24" fill="#D9D9D9"/>
                                </mask>
                                <g mask="url(#mask0_4588_2115)">
                                    <path d="M12 15L7 10H17L12 15Z" fill="white"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                </ListboxButton>
                <ListboxOptions
                    anchor="bottom"
                    transition
                    className={clsx(
                        'mt-1 rounded-lg border border-neutral-700 w-[var(--button-width)]  bg-[#131210] p-2 [--anchor-gap:var(--spacing-1)] focus:outline-none',
                        'transition duration-100 ease-in data-[leave]:data-[closed]:opacity-0'
                    )}
                >
                    {accounts.map((account) => (
                        <ListboxOption
                            key={account.name}
                            value={account}
                            className="group flex cursor-default items-center gap-2 rounded-lg px-4 py-3 select-none data-[focus]:bg-white/10"
                        >
                            <BaseBadge shape="rounded"
                                       variant={account.active ? 'secondary' : 'error'}
                                       size="sm">{account.active ? 'ACTIVE' : 'INACTIVE'}
                            </BaseBadge>
                            <div className="text-stone-400 text-base font-normal truncate">{account.name}</div>
                        </ListboxOption>
                    ))}
                </ListboxOptions>
            </Listbox>
        </div>
    )
}