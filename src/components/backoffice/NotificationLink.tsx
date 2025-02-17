import React, {JSX, useCallback, useEffect, useRef, useState} from 'react';
import {INotification, NotificationStatus} from "@/commons/interfaces";
import NotificationIconStatus from "@/components/NotificationIconStatus";
import clsx from "clsx";
import {Button} from "@/components/Button";
import {CheckIcon} from "@heroicons/react/16/solid";
import {XCircleIcon} from "@heroicons/react/20/solid";
import {PopoverClose} from "@radix-ui/react-popover";
import PopoverMenuModal from "@/components/backoffice/PopoverMenuModal";

interface NotificationIconProps {
    hasNotification?: boolean;
}

const NotificationIcon = ({hasNotification = false}: NotificationIconProps): JSX.Element => {
    return hasNotification ? (
            <div className="flex justify-center items-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_6163_1787" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                          height="24">
                        <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_6163_1787)">
                        <path
                            d="M12 22C11.45 22 10.9792 21.8042 10.5875 21.4125C10.1958 21.0208 10 20.55 10 20H14C14 20.55 13.8042 21.0208 13.4125 21.4125C13.0208 21.8042 12.55 22 12 22ZM4 19V17H6V10C6 8.61667 6.41667 7.3875 7.25 6.3125C8.08333 5.2375 9.16667 4.53333 10.5 4.2V3.5C10.5 3.08333 10.6458 2.72917 10.9375 2.4375C11.2292 2.14583 11.5833 2 12 2C12.4167 2 12.7708 2.14583 13.0625 2.4375C13.3542 2.72917 13.5 3.08333 13.5 3.5V3.825C13.3333 4.15833 13.2083 4.50833 13.125 4.875C13.0417 5.24167 13 5.61667 13 6C13 7.38333 13.4875 8.5625 14.4625 9.5375C15.4375 10.5125 16.6167 11 18 11V17H20V19H4ZM18 9C17.1667 9 16.4583 8.70833 15.875 8.125C15.2917 7.54167 15 6.83333 15 6C15 5.16667 15.2917 4.45833 15.875 3.875C16.4583 3.29167 17.1667 3 18 3C18.8333 3 19.5417 3.29167 20.125 3.875C20.7083 4.45833 21 5.16667 21 6C21 6.83333 20.7083 7.54167 20.125 8.125C19.5417 8.70833 18.8333 9 18 9Z"
                            fill="#2DD4BF"/>
                    </g>
                </svg>
            </div>
        ) :
        (
            <div className="flex justify-center items-center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_6163_187" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                          width="24"
                          height="24">
                        <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_6163_187)">
                        <path
                            d="M4 19V17H6V10C6 8.61667 6.41667 7.3875 7.25 6.3125C8.08333 5.2375 9.16667 4.53333 10.5 4.2V3.5C10.5 3.08333 10.6458 2.72917 10.9375 2.4375C11.2292 2.14583 11.5833 2 12 2C12.4167 2 12.7708 2.14583 13.0625 2.4375C13.3542 2.72917 13.5 3.08333 13.5 3.5V4.2C14.8333 4.53333 15.9167 5.2375 16.75 6.3125C17.5833 7.3875 18 8.61667 18 10V17H20V19H4ZM12 22C11.45 22 10.9792 21.8042 10.5875 21.4125C10.1958 21.0208 10 20.55 10 20H14C14 20.55 13.8042 21.0208 13.4125 21.4125C13.0208 21.8042 12.55 22 12 22Z"
                            fill="white"/>
                    </g>
                </svg>
            </div>

        )
}

function NotificationTitle({children, status}: {
    children: React.ReactNode,
    status: NotificationStatus
}) {
    const textColor = {
        "success": 'text-teal-600',
        "error": 'text-rose-600',
        "warning": 'text-orange-600',
    }[status];

    return <div className={clsx('text-right  text-xs font-bold leading-tight', textColor)}>{children}</div>
}

function NotificationId({id}: {
    id: string
}) {
    return (
        <div className="h-6 p-1 bg-neutral-200 rounded justify-center items-center gap-2.5 inline-flex">
            <div
                className="text-[#131210] text-[10px] font-medium uppercase leading-none">#{id}
            </div>
        </div>
    )
}

function NotificationPanel({notification, markRead}: { notification: INotification, markRead: (id: string) => void }) {
    return <div
        className="w-[calc(100dvw-24px)] sm:w-full grid grid-cols-[auto_1fr] gap-4 p-3 font-['Roboto'] hover:bg-neutral-100 hover:rounded-lg">
        <div>
            <NotificationIconStatus status={notification.status}/>
        </div>
        <div className="grid grid-cols-[auto_1fr] items-center gap-1">
            <NotificationId id={notification.id}/>
            <NotificationTitle status={notification.status}>
                {notification.title}
            </NotificationTitle>
            <div className="col-span-2">
                <div className="text-black text-xs font-normal  leading-tight">
                    {notification.message}
                </div>
            </div>
            <div className="col-span-2">
                <Button onClick={() => markRead(notification.id)} variant={'light'} size={'sm'} iconPosition={'left'}
                        icon={<>
                            <CheckIcon className="text-black w-5 h-5"/>
                        </>
                        }>
                    {notification.action.label}
                </Button>
            </div>

        </div>
    </div>
}

const API_URL = "/api/notifications";

export default function NotificationLink() {
    const [notifications, setNotifications] = useState<INotification[]>([])

    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const [isFetching, setIsFetching] = useState(false);
    const observerRef = useRef<HTMLDivElement | null>(null);

    const fetchNotifications = useCallback(async () => {
        if (isFetching || !hasMore) return;
        setIsFetching(true);

        try {
            const response = await fetch(`${API_URL}?page=${page}&per_page=8&sortBy=id&direction=desc`);
            if (!response.ok) throw new Error("Unable to get the notifications");
            const data = await response.json();

            setNotifications((prev) => [...prev, ...data.data]);
            setPage((prevPage) => prevPage + 1);
            setHasMore(data.meta.current_page < data.meta.last_page);
        } catch (error) {
            console.error("Unable to get the notifications", error);
        } finally {
            setIsFetching(false);
        }
    }, [page, isFetching, hasMore]);

    const notificationsPending = notifications
        .filter(notification => !notification.action.read);

    useEffect(() => {
        void fetchNotifications();
    }, [fetchNotifications]);

    useEffect(() => {
        if (!observerRef.current || !hasMore) {
            return
        }

        const observer = new IntersectionObserver(
            (entries) => {
                if (entries[0].isIntersecting) {
                    void fetchNotifications();
                }
            },
            {rootMargin: "100px"}
        );

        observer.observe(observerRef.current);
        return () => observer.disconnect();
    }, [fetchNotifications, hasMore]);

    function markRead(id: string) {
        setNotifications((prev) =>
            prev.map((notification) => (notification.id === id ? {
                ...notification,
                action: {...notification.action, read: true}
            } : notification))
        );
    }

    return (
        <PopoverMenuModal className="block z-10 relative"
                          side={'bottom'}
                          align={'end'}
                          icon={<NotificationIcon hasNotification={notifications.length > 0}/>}>
            <div className="flex sm:hidden text-left w-full justify-between px-2 gap-2 mb-4">
                <div
                    className="text-[#131210] w-full text-2xl font-light uppercase leading-7">Notifications
                </div>
                <PopoverClose asChild>
                    <button>
                        <XCircleIcon className="w-6 h-6"/>
                    </button>
                </PopoverClose>
            </div>
            <div
                className="gap-1 flex flex-col h-[calc(100dvh-68px)] sm:w-[465px] sm:h-auto sm:max-h-[460px] overflow-scroll scrollbar-hide">
                {notificationsPending
                    .map(notification => (
                        <NotificationPanel key={notification.id} notification={notification} markRead={markRead}/>
                    ))}

                {hasMore && (
                    <div ref={observerRef} className="text-center p-4 text-gray-500 text-sm">
                        {isFetching ? "Loading..." : "More"}
                    </div>
                )}
            </div>
        </PopoverMenuModal>
    );
}
