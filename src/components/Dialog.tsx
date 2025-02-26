import React, {useEffect, useState} from 'react';

import {
    Dialog as AlertDialogRoot,
    DialogPortal as AlertDialogPortal,
    DialogClose as AlertDialogCancel,
    DialogContent as AlertDialogContent,
    DialogOverlay as AlertDialogOverlay,
    DialogTitle as AlertDialogTitle,
} from "@radix-ui/react-dialog";

import {XCircleIcon} from "@heroicons/react/20/solid";
import clsx from "clsx";

function Dialog({children, onClose, className = '', showModal = false, title = ''}: {
    className?: string,
    children?: React.ReactNode,
    onClose: () => void,
    showModal?: boolean,
    title?: string
}) {
    const [open, setOpen] = useState(false);

    useEffect(() => {
        setOpen(showModal)
    }, [showModal]);

    return (
        <AlertDialogRoot open={open} onOpenChange={(_open) => {
            setOpen(_open);
            if (!_open) {
                onClose()
            }
        }}>
            <AlertDialogPortal>
                <AlertDialogOverlay
                    className="fixed inset-0 bg-[#131210]/60 data-[state=open]:animate-overlayShow z-[60]"/>
                <AlertDialogContent
                    className={clsx('z-[2000] fixed left-1/2 top-1/2 max-h-[85vh] lg:max-h-full -translate-x-1/2 -translate-y-1/2 rounded-2xl pt-4 bg-[#131210] shadow-[0px_20px_20px_20px_rgba(0,0,0,0.10)] border border-neutral-700 flex-col justify-start items-center gap-8 inline-flex overflow-hidden focus:outline-none data-[state=open]:animate-contentShow', className)}>
                    <AlertDialogTitle className="w-full px-4">
                        <div className="flex justify-between items-center w-full">
                            <div className="text-white text-2xl font-medium uppercase leading-7">
                                {title}
                            </div>

                            <AlertDialogCancel asChild>
                                <button
                                    className="select-none">
                                    <XCircleIcon className="text-white w-6 h-6"/>
                                </button>
                            </AlertDialogCancel>
                        </div>
                    </AlertDialogTitle>
                    <div className="w-full overflow-auto max-h-[70vh] lg:max-h-full pr-4 pl-4 pb-4">
                        {children}
                    </div>
                </AlertDialogContent>
            </AlertDialogPortal>
        </AlertDialogRoot>
    );
}

export default Dialog;