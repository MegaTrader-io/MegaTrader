'use client';

import Image from "next/image";
import React, {useEffect, useState} from "react";
import {IShowAlert} from "@/app/(backoffice)/refferals/page";
import Alert from "@/components/Alert";
import HomeLayout from "@/components/HomeLayout";
import SubscribeForm from "@/components/SubscribeForm";
import SocialMedia from "@/components/landing-page/SocialMedia";

const Home = () => {
    const [params, setParams] = useState<URLSearchParams | undefined>();
    const [showAlert, setShowAlert] = useState<IShowAlert | null>(null);

    useEffect(() => {
        if (typeof window === 'undefined') {
            return;
        }

        setParams(new URLSearchParams(window.location.search))
    }, [])

    function cbShowAlert(payload: IShowAlert | null) {
        setShowAlert(payload)
    }

    return <HomeLayout>
        <div
            className="mx-auto sm:w-[406px] xl:w-[406px] space-y-8 h-full">
            {showAlert && (
                <div className="w-full">
                    <Alert type={showAlert.type}
                           message={showAlert.message}/>
                </div>
            )}

            <div className="flex gap-4 items-center justify-center">
                <Image src={'/assets/images/logo-mt.svg'} width={60} height={60} alt={'Logo Megatrader'}/>
                <Image
                    src="../assets/images/megatrader-original.svg"
                    alt="Logo"
                    width={250}
                    height={45}
                    className="w-[250px] h-[44.63px]"
                />
            </div>

            {params && params.get('banner') && [1, 2].includes(Number(params.get('banner'))) && (
                <Image
                    src={`/assets/images/50_Off_Banner_${params.get('banner')}.png`}
                    width={553}
                    quality={100}
                    height={Number(params.get('banner')) === 1 ? 154 : 74}
                    alt={'Logo Megatrader'}/>
            )}

            <div className="space-y-4">
                <h1 className="self-stretch text-center justify-start text-white text-[40px] font-medium uppercase leading-[48px]">
                    Coming soon!
                </h1>
                <h2 className="self-stretch text-center justify-start text-stone-400 text-base font-medium leading-normal">
                    Empowering traders with innovative solutions, unmatched reliability, and tools designed to elevate
                    your trading journey to new heights.
                </h2>
            </div>
            <SubscribeForm cbShowAlert={cbShowAlert}/>
            <SocialMedia className="justify-center"/>
        </div>
    </HomeLayout>
}

export default Home;