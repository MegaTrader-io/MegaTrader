'use client';

import {useRouter} from "next/navigation";
import {useEffect} from "react";
import {useFlash} from "@/app/providers/FlashContext";
import {sleep} from "@/commons/utils";

function Page() {
    const {setFlash} = useFlash()
    const router = useRouter();

    useEffect(() => {
        const fakeDelay = async () => {
            await sleep(800);
            setFlash('You have successfully logged out');

            localStorage.removeItem('isLoggedIn');
            router.replace('/auth/login');
        }

        void fakeDelay();
    });
}

export default Page;