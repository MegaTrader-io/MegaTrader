"use client";

import {useEffect, useState} from "react";
import {usePathname, useRouter} from "next/navigation";
import {useLoadingBetweenPages} from "@/context/LoadingBetweenPagesContext";

export default function NavigationLoader() {
    const pathname = usePathname();
    const router = useRouter();
    const {setLoading} = useLoadingBetweenPages();
    const [prevPath, setPrevPath] = useState(pathname);

    useEffect(() => {
        const handleClick = (event: MouseEvent) => {
            const target = event.target as HTMLElement;
            const link = target.closest("a");

            if (link && link.href && link.origin === window.location.origin) {
                setLoading(true);
            }
        };

        document.body.addEventListener("click", handleClick, true);

        const timeout = setTimeout(() => setLoading(false), 800);
        setPrevPath(pathname);

        return () => {
            document.body.removeEventListener("click", handleClick, true);
            clearTimeout(timeout);
        };
    }, [pathname, prevPath, setLoading, router]);

    return null;
}
