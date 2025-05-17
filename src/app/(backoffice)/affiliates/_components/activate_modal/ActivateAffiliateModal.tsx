import * as React from 'react';
import Dialog from "@/components/Dialog";
import {Button} from "@/components/Button";
import Image from "next/image";
import Card from "@/components/Card";

const featureList = [
    {
        icon: '/assets/images/features/copy.svg',
        title: 'Copy link',
        subtitle: 'Grab your personal referral link and start sharing instantly.',
    },
    {
        icon: '/assets/images/features/share.svg',
        title: 'Spread the word',
        subtitle: 'Share your referral link with anyone and grow your earnings.',
    },
    {
        icon: '/assets/images/features/dolar.svg',
        title: 'Share the wealth',
        subtitle: 'Get 20% commission on every sale made through your link.',
    }
]

export const ActivateAffiliateModal = ({open, onClose}: {
    open: boolean,
    onClose: () => void,
}) => {
    const onSubmit = () => {

    }

    return (
        <Dialog showModal={open}
                childrenClassName={'max-h-dvh -top-[10px] relative md:space-y-12 pr-8 pl-8'}
                className="w-[calc(100vw-32px)] sm:w-[700px] md:!gap-0"
                onClose={onClose}>
            <div
                className="flex flex-col items-center space-y-8 md:space-y-0 md:flex-row md:flex md:space-x-8 relative">
                <div className="space-y-4 justify-center items-center flex-col self-center">
                    <div
                        className="justify-start text-white text-[32px] font-medium uppercase leading-10">
                        Start Earning with Referrals
                    </div>
                    <p className="w-[288px] text-left text-stone-400 text-base font-medium  leading-normal">
                        Activate your dashboard to start earning commission from every referral sale.
                    </p>
                    <form onSubmit={onSubmit} noValidate={false}>
                        <Button type={'submit'} variant={'primary'}>ACTIVATE</Button>
                    </form>
                </div>
                <Image className="py-12 md:py-0" src={'/assets/images/Pic.png'} alt={'faces'} width={316}
                       height={327}></Image>
            </div>
            <div className="block space-y-4 sm:space-y-0 sm:grid sm:grid-cols-3 sm:gap-4">
                {featureList.map(feature => (
                    <Card key={feature.title} className="space-y-2">
                        <Image src={feature.icon} alt={feature.title} width={32} height={32}></Image>
                        <h2
                            className="justify-start text-teal-400 text-xl font-bold leading-loose">
                            {feature.title}
                        </h2>
                        <p
                            className="justify-start text-stone-400 text-base font-medium leading-normal">
                            {feature.subtitle}
                        </p>
                    </Card>
                ))}
            </div>
        </Dialog>
    );
};