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
            <div className="relative">
                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M0 17V15H2V8C2 6.61667 2.41667 5.3875 3.25 4.3125C4.08333 3.2375 5.16667 2.53333 6.5 2.2V1.5C6.5 1.08333 6.64583 0.729167 6.9375 0.4375C7.22917 0.145833 7.58333 0 8 0C8.41667 0 8.77083 0.145833 9.0625 0.4375C9.35417 0.729167 9.5 1.08333 9.5 1.5V2.2C10.8333 2.53333 11.9167 3.2375 12.75 4.3125C13.5833 5.3875 14 6.61667 14 8V15H16V17H0ZM8 20C7.45 20 6.97917 19.8042 6.5875 19.4125C6.19583 19.0208 6 18.55 6 18H10C10 18.55 9.80417 19.0208 9.4125 19.4125C9.02083 19.8042 8.55 20 8 20Z"
                        fill="#2DD4BF"/>
                </svg>
                <div className="bg-[#2DD4BF] w-2.5 h-2.5 rounded-full absolute -right-[3px] top-[0px] border-2 border-stone-800">
                </div>
            </div>
        ) :
        (
            <div>
                <svg width="16" height="20" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M0 17V15H2V8C2 6.61667 2.41667 5.3875 3.25 4.3125C4.08333 3.2375 5.16667 2.53333 6.5 2.2V1.5C6.5 1.08333 6.64583 0.729167 6.9375 0.4375C7.22917 0.145833 7.58333 0 8 0C8.41667 0 8.77083 0.145833 9.0625 0.4375C9.35417 0.729167 9.5 1.08333 9.5 1.5V2.2C10.8333 2.53333 11.9167 3.2375 12.75 4.3125C13.5833 5.3875 14 6.61667 14 8V15H16V17H0ZM8 20C7.45 20 6.97917 19.8042 6.5875 19.4125C6.19583 19.0208 6 18.55 6 18H10C10 18.55 9.80417 19.0208 9.4125 19.4125C9.02083 19.8042 8.55 20 8 20Z"
                        fill="white"/>
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
    }, []);

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
