import Link from "next/link";
import Image from "next/image";
import React from "react";
import Card from "@/components/Card";

const items = [
    {
        icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_11661_17255" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_11661_17255)">
                <path
                    d="M3 21V16.75L16.2 3.575C16.4 3.39167 16.6208 3.25 16.8625 3.15C17.1042 3.05 17.3583 3 17.625 3C17.8917 3 18.15 3.05 18.4 3.15C18.65 3.25 18.8667 3.4 19.05 3.6L20.425 5C20.625 5.18333 20.7708 5.4 20.8625 5.65C20.9542 5.9 21 6.15 21 6.4C21 6.66667 20.9542 6.92083 20.8625 7.1625C20.7708 7.40417 20.625 7.625 20.425 7.825L7.25 21H3ZM17.6 7.8L19 6.4L17.6 5L16.2 6.4L17.6 7.8Z"
                    fill="#2DD4BF"/>
            </g>
        </svg>,
        title: 'Trading Journal',
        subtitle: 'Take down comprehensive notes about any trade'
    },
    {
        icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_11661_17260" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_11661_17260)">
                <path
                    d="M5 21C4.45 21 3.97917 20.8042 3.5875 20.4125C3.19583 20.0208 3 19.55 3 19V5C3 4.45 3.19583 3.97917 3.5875 3.5875C3.97917 3.19583 4.45 3 5 3H11V21H5ZM13 21V12H21V19C21 19.55 20.8042 20.0208 20.4125 20.4125C20.0208 20.8042 19.55 21 19 21H13ZM13 10V3H19C19.55 3 20.0208 3.19583 20.4125 3.5875C20.8042 3.97917 21 4.45 21 5V10H13Z"
                    fill="#2DD4BF"/>
            </g>
        </svg>,
        title: 'Mobile Friendly',
        subtitle: 'Get the best experience on all devices from phone to desktop'
    },
    {
        icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_11661_17265" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_11661_17265)">
                <path
                    d="M4 19V17H6V10C6 8.61667 6.41667 7.3875 7.25 6.3125C8.08333 5.2375 9.16667 4.53333 10.5 4.2V3.5C10.5 3.08333 10.6458 2.72917 10.9375 2.4375C11.2292 2.14583 11.5833 2 12 2C12.4167 2 12.7708 2.14583 13.0625 2.4375C13.3542 2.72917 13.5 3.08333 13.5 3.5V4.2C14.8333 4.53333 15.9167 5.2375 16.75 6.3125C17.5833 7.3875 18 8.61667 18 10V17H20V19H4ZM12 22C11.45 22 10.9792 21.8042 10.5875 21.4125C10.1958 21.0208 10 20.55 10 20H14C14 20.55 13.8042 21.0208 13.4125 21.4125C13.0208 21.8042 12.55 22 12 22Z"
                    fill="#2DD4BF"/>
            </g>
        </svg>
        ,
        title: 'Price Alerts',
        subtitle: 'Notifications on important price alerts when it matters'
    },
    {
        icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_11661_17270" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_11661_17270)">
                <path
                    d="M5 22C4.45 22 3.97917 21.8042 3.5875 21.4125C3.19583 21.0208 3 20.55 3 20V6C3 5.45 3.19583 4.97917 3.5875 4.5875C3.97917 4.19583 4.45 4 5 4H6V2H8V4H16V2H18V4H19C19.55 4 20.0208 4.19583 20.4125 4.5875C20.8042 4.97917 21 5.45 21 6V20C21 20.55 20.8042 21.0208 20.4125 21.4125C20.0208 21.8042 19.55 22 19 22H5ZM5 20H19V10H5V20Z"
                    fill="#2DD4BF"/>
            </g>
        </svg>,
        title: 'Economic Calendar',
        subtitle: 'Know first what\'s happening with our economic calendar and red folder.'
    },
];


const HeroSection = ({className = ''}: { className?: string }) => (
    <section id="hero-section" className={className}>
        <div className="py-12 space-y-12">
            <div className="flex justify-center">
                <Image
                    src="/assets/images/megatraderX.svg"
                    width={250}
                    height={41}
                    alt={'logo megatrader x'}
                />
            </div>
            <div className="space-y-4">
                <div
                    className="self-stretch text-center justify-start text-white text-6xl font-light uppercase leading-[72px]">
                    Start Your Futures Journey
                </div>
                <div
                    className="w-full max-w-[612px] mx-auto text-center justify-start text-stone-400 text-xl font-medium leading-8">
                    Empowering traders with innovative solutions, unmatched reliability, and tools designed to elevate
                    your trading journey to new heights.
                </div>
            </div>
            <div className="space-y-4 md:space-y-0 md:flex md:justify-center md:gap-4">
                <Link href={'https://subscriptions.megatrader.io/'}
                      className='btn-yellow-link rounded-xl h-12 px-4 py-3'>
                    Start trading
                </Link>
                <Link href={'https://discord.com/invite/megatrader'}
                      target={'_blank'}
                      className='btn-dark-link rounded-xl h-12 px-4 py-3'>
                    Join Discord
                </Link>
            </div>
            <div className="grid grid-cols-1 space-y-4 lg:space-y-0 lg:flex lg:gap-4 justify-center">
                <Link href={'https://megatrader.io'} target={'_blank'}
                      className="pl-3 pr-4 py-3 rounded-xl outline outline-2 outline-offset-[-2px] outline-neutral-700 inline-flex justify-center items-center gap-2">
                    <div className="w-[30px] h-[30px] relative">
                        <Image
                            src="/assets/images/try-on-web.svg"
                            width={30}
                            height={30}
                            alt={'logo web x'}
                        />
                    </div>
                    <div className="inline-flex flex-col justify-center items-start">
                        <div
                            className="self-stretch justify-start text-stone-400 text-sm font-medium leading-tight">Try
                            the
                        </div>
                        <div
                            className="self-stretch justify-start text-white text-xl font-medium leading-8">WEB
                            APP
                        </div>
                    </div>
                </Link>
                <Link href={'https://megatrader.io'} target={'_blank'}
                      className="pl-3 pr-4 py-3 rounded-xl outline outline-2 outline-offset-[-2px] outline-neutral-700 inline-flex justify-center items-center gap-2">
                    <div className="w-[30px] h-[30px] relative overflow-hidden">
                        <Image
                            src="/assets/images/try-on-apple.svg"
                            width={30}
                            height={30}
                            alt={'logo web x'}
                        />
                    </div>
                    <div className="inline-flex flex-col justify-center items-start">
                        <div
                            className="self-stretch justify-start text-stone-400 text-sm font-medium leading-tight">Downloaded
                            on the
                        </div>
                        <div
                            className="self-stretch justify-start text-white text-xl font-medium leading-8">APP
                            STORE
                        </div>
                    </div>
                </Link>
                <Link href={'https://megatrader.io'} target={'_blank'}
                      className="pl-3 pr-4 py-3 rounded-xl outline outline-2 outline-offset-[-2px] outline-neutral-700 inline-flex justify-center items-center gap-2">
                    <div className="w-[30px] h-[32.14px] relative">
                        <Image
                            src="/assets/images/try-on-google-play.svg"
                            width={30}
                            height={30}
                            alt={'logo web x'}
                        />
                    </div>
                    <div className="inline-flex flex-col justify-center items-start">
                        <div
                            className="self-stretch justify-start text-stone-400 text-sm font-medium leading-tight">Get
                            it on
                        </div>
                        <div
                            className="self-stretch justify-start text-white text-xl font-medium leading-8">GOOGLE
                            PLAY
                        </div>
                    </div>
                </Link>
            </div>
            <div
                className="mx-auto flex justify-center items-center border-t-8 border-b-8  md:border-8 lg:rounded-lg border-[#3C383A] w-fit bg-[#3C383A]">
                <Image
                    src="/assets/images/metrics2.jpg"
                    alt="window tablet"
                    className="lg:rounded-lg"
                    width={1200}
                    height={670}
                    quality={100}
                />
            </div>
            <div>
                <div className="grid md:justify-center md:grid-cols-2 lg:grid-cols-4 gap-8">
                    {items.map((item, index) => (
                        <Card className={'w-full group p-8'} key={index}>
                            <div className="flex items-center gap-2">
                                {item.icon}
                                <div
                                    className="self-stretch justify-start text-teal-400 group-[.active]:text-[#131210] text-xl font-bold leading-loose">
                                    {item.title}
                                </div>
                            </div>
                            <div
                                className="self-stretch justify-start text-stone-400 group-[.active]:text-[#131210] text-base font-medium leading-normal">
                                {item.subtitle}
                            </div>
                        </Card>
                    ))}
                </div>
            </div>
        </div>
    </section>
);

export default HeroSection;