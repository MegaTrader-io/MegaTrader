import React, {useState} from 'react';
import {
    Root as AlertDialogRoot,
    AlertDialogTrigger,
    AlertDialogPortal,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogOverlay,
    AlertDialogTitle,
} from "@radix-ui/react-alert-dialog";
import {XCircleIcon} from "@heroicons/react/20/solid";
import clsx from "clsx";

interface DropdownDialogProps<T> {
    items: T[];
    value: T;
    onChange: (item: T) => void;
    disabled?: boolean;
    renderButtonContent: (item: T) => React.ReactNode;
    renderOptionContent: (item: T) => React.ReactNode;
}

export default function DropdownDialog<T extends { id: string | number, name: string }>({
                                                                                            items = [],
                                                                                            value,
                                                                                            onChange,
                                                                                            renderButtonContent,
                                                                                            renderOptionContent,
                                                                                        }: DropdownDialogProps<T>) {
    const [open, setOpen] = useState(false);
    const [selected, setSelected] = useState(value);

    const handleChange = (item: T) => {
        setSelected(item);
        if (onChange) onChange(item);
        setOpen(false)
    };

    return (
        <AlertDialogRoot open={open} onOpenChange={setOpen}>
            <AlertDialogTrigger asChild>
                {renderButtonContent(selected)}
            </AlertDialogTrigger>
            <AlertDialogPortal>
                <AlertDialogOverlay className="fixed inset-0 bg-[#131210]/60 data-[state=open]:animate-overlayShow"/>
                <AlertDialogContent
                    className="fixed left-1/2 top-1/2 max-h-[85vh] w-[360px] -translate-x-1/2 -translate-y-1/2 rounded-2xl p-4 bg-[#131210] shadow-[0px_20px_20px_20px_rgba(0,0,0,0.10)] border border-neutral-700 flex-col justify-start items-center gap-8 inline-flex overflow-hidden focus:outline-none data-[state=open]:animate-contentShow">
                    <AlertDialogTitle className="w-full">
                        <div className="flex justify-between items-center w-full">
                            <div className="text-white text-2xl font-medium uppercase leading-7">
                                SELECT ACCOUNT
                            </div>

                            <AlertDialogCancel asChild>
                                <button
                                    className="select-none">
                                    <XCircleIcon className="text-white w-6 h-6"/>
                                </button>
                            </AlertDialogCancel>
                        </div>
                    </AlertDialogTitle>
                    <div>
                        <ul className="space-y-2">
                            {items.map((item) => {
                                return <li
                                    onClick={() => {
                                        handleChange(item)
                                    }}
                                    className={clsx('cursor-pointer flex  pl-4 pr-3 py-3 gap-2 items-center', {'bg-stone-800 rounded-xl border border-neutral-700': selected.id === item.id})}
                                    key={item.id}>
                                    {renderOptionContent(item)}
                                </li>
                            })}
                        </ul>
                    </div>
                </AlertDialogContent>
            </AlertDialogPortal>
        </AlertDialogRoot>
    );
}