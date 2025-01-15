import {Listbox, ListboxButton, ListboxOption, ListboxOptions} from '@headlessui/react';
import clsx from 'clsx';
import React, {useState} from 'react';

interface DropdownProps<T> {
    items: T[];
    value: T;
    onChange: (item: T) => void;
    disabled?: boolean;
    renderButtonContent: (item: T) => React.ReactNode;
    renderOptionContent: (item: T) => React.ReactNode;
}

export default function Dropdown<T extends { id: string | number }>({
                                                                        items = [],
                                                                        value,
                                                                        onChange,
                                                                        disabled = false,
                                                                        renderButtonContent,
                                                                        renderOptionContent,
                                                                    }: DropdownProps<T>) {
    const [selected, setSelected] = useState(value);

    const handleChange = (item: T) => {
        setSelected(item);
        if (onChange) onChange(item);
    };

    return (
        <div className="h-12 w-[256px]">
            <Listbox disabled={disabled} value={selected} onChange={handleChange}>
                <ListboxButton
                    className={clsx(
                        'relative block w-full pl-4 pr-3 py-3 text-base font-normal leading-normal rounded-xl bg-stone-800 hover:bg-[#1e1e1e] border border-neutral-700 hover:border-transparent text-white  disabled:bg-stone-600 disabled:text-stone-800',
                        'focus:outline-none data-[focus]:outline-2 data-[focus]:-outline-offset-2 data-[focus]:outline-white/25'
                    )}
                >
                    <div className="flex gap-2 items-center uppercase">
                        {renderButtonContent(selected)}
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
                    {items.map((item) => (
                        <ListboxOption
                            key={item.id}
                            value={item}
                            className="group flex cursor-default items-center gap-2 rounded-lg px-4 py-3 select-none data-[focus]:bg-white/10"
                        >
                            {renderOptionContent(item)}
                        </ListboxOption>
                    ))}
                </ListboxOptions>
            </Listbox>
        </div>
    );
}