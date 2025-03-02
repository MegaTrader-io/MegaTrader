import React, {useEffect, useState, useRef} from 'react';
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
    const [isMobile, setIsMobile] = useState(false);  // Inicializamos en false para evitar errores en SSR
    const [open, setOpen] = useState(false);
    const popoverRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        setOpen(showModal);
    }, [showModal]);

    useEffect(() => {
        const handleResize = () => {
            setIsMobile(window.innerWidth < 640);
        };

        if (typeof window !== "undefined") {
            handleResize();
            window.addEventListener('resize', handleResize);
        }

        return () => {
            if (typeof window !== "undefined") {
                window.removeEventListener('resize', handleResize);
            }
        };
    }, []);

    useEffect(() => {
        setTimeout(() => {
            if (!popoverRef.current) return;

            if (isMobile) {
                // popoverRef.current.classList.add('dialog-fullscreen');
            } else {
                // popoverRef.current.classList.remove('dialog-fullscreen');
            }
        }, 200)
    }, [isMobile, open]);

    return (
        <AlertDialogRoot open={open} onOpenChange={(_open) => {
            setOpen(_open);
            if (!_open) {
                onClose();
            }
        }}>
            <AlertDialogPortal>
                <AlertDialogOverlay
                    className="fixed inset-0 bg-[#131210]/90 data-[state=open]:animate-overlayShow z-[60]"/>
                <AlertDialogContent
                    ref={popoverRef}
                    className={clsx(
                        'z-[2000] fixed left-1/2 top-1/2 max-h-[85vh] lg:max-h-full -translate-x-1/2 -translate-y-1/2 rounded-2xl pt-4 bg-[#131210] shadow-[0px_20px_20px_20px_rgba(0,0,0,0.10)] border border-neutral-700 flex-col justify-start items-center gap-8 inline-flex overflow-hidden focus:outline-none data-[state=open]:animate-contentShow',
                        className,
                        open && isMobile ? 'w-screen h-dvh max-h-dvh rounded-none border-0': null
                    )}>
                    <AlertDialogTitle className="w-full px-4">
                        <div className="flex justify-between items-center w-full">
                            <div className="text-white text-2xl font-medium uppercase leading-7">
                                {title}
                            </div>

                            <AlertDialogCancel asChild>
                                <button className="select-none">
                                    <XCircleIcon className="text-white w-6 h-6"/>
                                </button>
                            </AlertDialogCancel>
                        </div>
                    </AlertDialogTitle>
                    <div className="w-full h-full sm:h-auto overflow-auto max-h-[70vh] lg:max-h-full pr-4 pl-4 pb-4">
                        {children}
                    </div>
                </AlertDialogContent>
            </AlertDialogPortal>
        </AlertDialogRoot>
    );
}

export default Dialog;
