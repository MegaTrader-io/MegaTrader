import React, {useState} from 'react';
import Card from "@/components/Card";
import Image from "next/image";
import Badge from "@/components/Badge";
import clsx from "clsx";

const platforms = [
    {
        name: 'ProjectX',
        image: 'projectxIcon',
        description: 'Experience precision and speed with MegaX, a platform designed for traders who demand reliability and performance. Built for the futures market, MegaX combines cutting-edge tools and seamless execution to empower your trading success.'
    },
    {
        name: 'Ninjatrader',
        image: 'ninjatraderIcon',
        description: 'Empower your trading with advanced analytics, dynamic charting tools, and seamless execution designed for futures traders worldwide. NinjaTrader provides a powerful platform for those seeking precision and reliability. Elevate your strategies and achieve exceptional performance.'
    },
    {
        name: 'Tradovate',
        image: 'TradovateIcon',
        description: 'Streamline your trading experience with Tradovate\'s cloud-based platform, built for speed, simplicity, and modern efficiency. Enjoy a user-friendly interface, powerful tools, and seamless connectivity to ensure success in the futures market.'
    },
    {
        name: 'Quantower',
        image: 'QuantowerIcon',
        description: 'Quantower delivers unmatched versatility for traders with advanced tools, intuitive features, and comprehensive strategy support. Tailored for adaptability, this platform is designed to enhance your trading journey and optimize your performance.'
    }
]

const features = [
    {
        icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_4890_4470" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_4890_4470)">
                <path
                    d="M2 22V4C2 3.45 2.19583 2.97917 2.5875 2.5875C2.97917 2.19583 3.45 2 4 2H20C20.55 2 21.0208 2.19583 21.4125 2.5875C21.8042 2.97917 22 3.45 22 4V16C22 16.55 21.8042 17.0208 21.4125 17.4125C21.0208 17.8042 20.55 18 20 18H6L2 22ZM6 14H14V12H6V14ZM6 11H18V9H6V11ZM6 8H18V6H6V8Z"
                    fill="#2DD4BF"/>
            </g>
        </svg>,
        title: 'Email and web support'
    },
    {
        icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_4890_4515" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_4890_4515)">
                <path
                    d="M5 22C4.45 22 3.97917 21.8042 3.5875 21.4125C3.19583 21.0208 3 20.55 3 20V6C3 5.45 3.19583 4.97917 3.5875 4.5875C3.97917 4.19583 4.45 4 5 4H6V2H8V4H16V2H18V4H19C19.55 4 20.0208 4.19583 20.4125 4.5875C20.8042 4.97917 21 5.45 21 6V20C21 20.55 20.8042 21.0208 20.4125 21.4125C20.0208 21.8042 19.55 22 19 22H5ZM5 20H19V10H5V20Z"
                    fill="#2DD4BF"/>
            </g>
        </svg>,
        title: 'Create plans based on your needs'
    },
    {
        icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_4890_4520" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_4890_4520)">
                <path
                    d="M6 22C5.45 22 4.97917 21.8042 4.5875 21.4125C4.19583 21.0208 4 20.55 4 20V4C4 3.45 4.19583 2.97917 4.5875 2.5875C4.97917 2.19583 5.45 2 6 2H14L20 8V20C20 20.55 19.8042 21.0208 19.4125 21.4125C19.0208 21.8042 18.55 22 18 22H6ZM13 9H18L13 4V9Z"
                    fill="#2DD4BF"/>
            </g>
        </svg>,
        title: 'Your data in detailed summary'
    },
    {
        icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_4890_4525" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_4890_4525)">
                <path
                    d="M6 22C5.45 22 4.97917 21.8042 4.5875 21.4125C4.19583 21.0208 4 20.55 4 20V10C4 9.45 4.19583 8.97917 4.5875 8.5875C4.97917 8.19583 5.45 8 6 8H7V6C7 4.61667 7.4875 3.4375 8.4625 2.4625C9.4375 1.4875 10.6167 1 12 1C13.3833 1 14.5625 1.4875 15.5375 2.4625C16.5125 3.4375 17 4.61667 17 6V8H18C18.55 8 19.0208 8.19583 19.4125 8.5875C19.8042 8.97917 20 9.45 20 10V20C20 20.55 19.8042 21.0208 19.4125 21.4125C19.0208 21.8042 18.55 22 18 22H6ZM12 17C12.55 17 13.0208 16.8042 13.4125 16.4125C13.8042 16.0208 14 15.55 14 15C14 14.45 13.8042 13.9792 13.4125 13.5875C13.0208 13.1958 12.55 13 12 13C11.45 13 10.9792 13.1958 10.5875 13.5875C10.1958 13.9792 10 14.45 10 15C10 15.55 10.1958 16.0208 10.5875 16.4125C10.9792 16.8042 11.45 17 12 17ZM9 8H15V6C15 5.16667 14.7083 4.45833 14.125 3.875C13.5417 3.29167 12.8333 3 12 3C11.1667 3 10.4583 3.29167 9.875 3.875C9.29167 4.45833 9 5.16667 9 6V8Z"
                    fill="#2DD4BF"/>
            </g>
        </svg>,
        title: 'Secure and fast'
    },
    {
        icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_4890_5001" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect width="24" height="24" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_4890_5001)">
                <path
                    d="M4 19V17H6V10C6 8.61667 6.41667 7.3875 7.25 6.3125C8.08333 5.2375 9.16667 4.53333 10.5 4.2V3.5C10.5 3.08333 10.6458 2.72917 10.9375 2.4375C11.2292 2.14583 11.5833 2 12 2C12.4167 2 12.7708 2.14583 13.0625 2.4375C13.3542 2.72917 13.5 3.08333 13.5 3.5V4.2C14.8333 4.53333 15.9167 5.2375 16.75 6.3125C17.5833 7.3875 18 8.61667 18 10V17H20V19H4ZM12 22C11.45 22 10.9792 21.8042 10.5875 21.4125C10.1958 21.0208 10 20.55 10 20H14C14 20.55 13.8042 21.0208 13.4125 21.4125C13.0208 21.8042 12.55 22 12 22Z"
                    fill="#2DD4BF"/>
            </g>
        </svg>,
        title: 'Create custom alerts'
    }
]

const relativePath = '/assets/images/discover-platform-icons/'

function DiscoverThePlatformsPoweringYourTrades() {
    const [currentSelection, setCurrentSelection] = useState<number>(0);

    const body = platforms.at(currentSelection || 0)!;

    function changeOption(index: number) {
        setCurrentSelection(index);
    }

    return (
        <section className="lg:flex lg:gap-12 space-y-12 lg:space-y-0 px-4 py-12">
            <div className="w-full space-y-12">
                <div
                    className="justify-start text-white text-[40px] font-light uppercase leading-[48px]">
                    Discover the Platforms Powering Your Trades
                </div>
                <div
                    className="justify-start text-stone-400 text-xl font-medium leading-8">Explore
                    the industry{'\''}s leading trading platforms designed to empower your strategies and enhance your
                    trading experience.
                </div>

                <div className="space-y-2">
                    {features.map((feature, index) => (
                        <div
                            key={index}
                            className="px-3 py-2 bg-[#1e1e1e] rounded-[64px] inline-flex justify-start items-center gap-2 mr-2">
                            <div className="w-6 h-6 relative">
                                {feature.icon}
                            </div>
                            <div
                                className="justify-start text-teal-400 text-base font-medium font-['Roboto'] leading-normal">
                                {feature.title}
                            </div>
                        </div>
                    ))}
                </div>
            </div>
            <Card className="w-full py-8">
                <div className="flex">
                    {platforms.map((platform, index) => (
                        <div key={index} onClick={() => changeOption(index)}
                             className="flex flex-col w-full items-center p-4 space-y-2 cursor-pointer">
                            <Image
                                src={relativePath + (currentSelection === index ? `${platform.image}-on.png` : `${platform.image}-off.png`)}
                                width={40}
                                height={40} alt={platform.name}/>

                            <div
                                className={clsx('self-stretch text-center justify-start  text-base text-white font-bold leading-6', {
                                    'opacity-30': currentSelection !== index
                                })}>
                                {platform.name}
                            </div>

                            {currentSelection === index && (
                                <Badge size={'sm'} className='tracking-tight hidden text-nowrap lg:flex'>
                                    COMING SOON
                                </Badge>
                            )}
                        </div>
                    ))}
                </div>
                <div className="px-4 space-y-4 mt-8">
                    <div
                        className="justify-start text-white text-xl font-light uppercase leading-6">
                        {body.name}
                    </div>
                    <div
                        className="self-stretch justify-start text-stone-400 text-base font-medium  leading-normal">Experience
                        precision and speed with MegaX, a platform designed for traders who demand reliability and
                        performance. Built for the futures market, MegaX combines cutting-edge tools and seamless
                        execution to empower your trading success.
                    </div>
                </div>
            </Card>
        </section>
    );
}

export default DiscoverThePlatformsPoweringYourTrades;