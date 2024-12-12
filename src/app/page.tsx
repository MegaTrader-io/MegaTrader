import Image from 'next/image';
import Header from "@/components/header";
import Link from "@/components/link";
import Card from "@/components/card";

interface PlanInterface {
    id: number
    level: string,
    total_peer_year: string,
    total_peer_month: string,
    max_loss_limit: string,
    max_position_size: string,
    profit_target: string,
}

function CardPlan({plan}: { plan: PlanInterface }) {
    const isPremium = plan.level === 'PREMIUM';
    const bgColorYellow = '!bg-[#ffb34a]';
    const bgColorBlackLight = 'bg-[#1e1e1e]/70';

    return <Card
        key={plan.id}
        className={`group ${isPremium ? `isPremium ${bgColorYellow}` : bgColorBlackLight}  p-4  rounded-2xl border border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex`}>
        <div className="text-white group-[.isPremium]:text-[#131210] text-xl font-medium">
            {plan.level}
        </div>
        <div className="grid grid-cols-2 text-white group text-[32px] font-light leading-10">
            <div className="text-white group-[.isPremium]:text-[#131210]">
                {plan.total_peer_year}
            </div>
            <div className="text-[#ffb34a] group-[.isPremium]:text-[#131210]">
                {plan.total_peer_month}
            </div>
        </div>
        <div className="mt-5 mb-2 w-full">
            <div
                className="text-stone-400 group-[.isPremium]:text-[#131210] text-base font-normal leading-normal">Maximum
                Loss
                Limit
            </div>
            <div
                className=" text-stone-400 group-[.isPremium]:text-[#131210] text-base font-bold leading-normal">{plan.max_loss_limit}</div>
        </div>
        <div className="my-2">
            <div
                className="text-stone-400 group-[.isPremium]:text-[#131210] text-base font-normal leading-normal">Maximum
                Position
                Size
            </div>
            <div
                className=" text-stone-400 group-[.isPremium]:text-[#131210] text-base font-bold leading-normal">{plan.max_position_size}</div>
        </div>
        <div className="my-2">
            <div
                className="text-stone-400 group-[.isPremium]:text-[#131210] text-base font-normal leading-normal">Profit
                Target
            </div>
            <div
                className=" text-stone-400 group-[.isPremium]:text-[#131210] text-base font-bold leading-normal">{plan.profit_target}</div>
        </div>

        <Link as={"button"}
              className="!bg-[#ffb34a] group-[.isPremium]:!bg-stone-800 text-slate-950 group-[.isPremium]:text-white">
            GET PLAN
        </Link>
    </Card>
}

function AccountSize() {
    const PLANS = [
        {
            id: 1,
            level: 'BASIC',
            total_peer_year: '$50k',
            total_peer_month: '$89.99MO',
            max_loss_limit: '$2,000',
            max_position_size: '5 Contracts',
            profit_target: '$3,000',
        },
        {
            id: 2,
            level: 'PREMIUM',
            total_peer_year: '$100k',
            total_peer_month: '$89.99MO',
            max_loss_limit: '$2,000',
            max_position_size: '10 Contracts',
            profit_target: '$6,000',
        },
        {
            id: 3,
            level: 'UNLIMITED',
            total_peer_year: '$150k',
            total_peer_month: '$199.99MO',
            max_loss_limit: '$4,500',
            max_position_size: '15 Contracts',
            profit_target: '$9,000',
        }
    ]

    return <section className="mt-8 mb-10">
        <h2 className="text-5xl text-white text-center mb-10 font-light leading-[60px]">
            CHOOSE YOUR ACCOUNT SIZE
        </h2>

        <div className="grid grid-cols-3 gap-8">
            {PLANS.map(plan => (
                <CardPlan key={plan.id} plan={plan}/>
            ))}
        </div>
    </section>
}

function UnlockThePowerOfMegatrader() {
    return <>
        <section className="my-8 py-4 grid grid-cols-2">
            <div>
                <h2 className="text-left text-5xl text-white mb-10 font-light leading-[60px]">
                    UNLOCK THE POWER OF MEGATRADER
                </h2>
                <div className="space-y-8 mb-[76px]">
                    <Image
                        src="/assets/images/plus.svg"
                        alt="Plus icons"
                        width={88}
                        height={24}
                        className="relative"
                    />
                    <p className="text-stone-400 text-xl font-normal">
                        Experience the full potential of your trading platform with our intuitive and advanced user interface.
                    </p>
                    <Link as={"button"}
                          className="!bg-[#ffb34a] group-[.isPremium]:!bg-stone-800 text-slate-950 group-[.isPremium]:text-white">
                        OPEN AN ACCOUNT
                    </Link>
                </div>

                <Image
                    src="/assets/images/metrics2.svg"
                    alt="Trading Platform Interface"
                    width={494}
                    height={447}
                />
            </div>
            <div></div>
        </section>
    </>
}

const Home = () =>
    (
        <>
            <Header/>
            <main className="mx-auto max-w-7xl mt-[59px] px-4">
                <section className="grid grid-cols-1 lg:grid-cols-[1fr_auto]">
                    <div>
                        <div className="max-h-[331px] grid grid-rows-[1fr_1fr]">
                            <h1 className="relative mb-0">
                                <span className="text-[#FFB34A] text-7xl font-bold">FUEL YOUR</span><br/>
                                <span className="text-white text-5xl font-bold">TRADING SUCCESS</span><br/>
                                <div className="text-stone-400 text-xl font-light max-w-[588px] mt-2">We empower you to
                                    trade futures confidently, build smarter strategies, and grow with a supportive
                                    community.
                                </div>
                            </h1>
                            <div className="flex items-center">
                                <svg width="300" height="50" viewBox="0 0 300 50" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.5" y="0.5" width="299" height="49" rx="9.5" fill="#1E1E1E"
                                          fillOpacity="0.7"
                                          stroke="#404040"/>
                                    <path
                                        d="M163.875 39.9993C161.909 39.9993 160.354 39.6279 159.209 38.8858C158.063 38.1423 157.243 37.0756 156.745 35.6829C156.248 34.2903 156 32.6315 156 30.7045V20.2255C156 18.2761 156.248 16.6173 156.745 15.2478C157.243 13.8783 158.063 12.8279 159.209 12.0973C160.354 11.3653 161.909 11 163.875 11C165.863 11 167.435 11.3653 168.591 12.0973C169.746 12.8279 170.573 13.8783 171.071 15.2478C171.567 16.6173 171.816 18.2761 171.816 20.2255V30.7045C171.816 32.6315 171.567 34.2903 171.071 35.6829C170.573 37.0756 169.748 38.1423 168.591 38.8858C167.435 39.6279 165.863 39.9993 163.875 39.9993ZM163.875 35.5784C164.481 35.5784 164.923 35.4161 165.203 35.0915C165.484 34.7656 165.669 34.348 165.755 33.8374C165.841 33.3275 165.885 32.8053 165.885 32.271V18.6944C165.885 18.1369 165.841 17.6094 165.755 17.1096C165.669 16.6106 165.484 16.1991 165.203 15.8745C164.923 15.5493 164.48 15.387 163.875 15.387C163.314 15.387 162.892 15.5493 162.611 15.8745C162.33 16.1991 162.147 16.6119 162.059 17.1096C161.973 17.6094 161.93 18.1369 161.93 18.6944V32.271C161.93 32.8053 161.968 33.3275 162.045 33.8374C162.12 34.3494 162.293 34.7656 162.562 35.0915C162.832 35.4161 163.271 35.5784 163.875 35.5784ZM175.348 39.5817V11.383H179.432L184.812 24.9595V11.383H189.608V39.5817H185.687L180.308 24.9602V39.5817H175.348ZM193.53 39.5817V11.383H205.326V15.6294H199.33V22.6272H203.899V26.9436H199.33V35.3686H205.39V39.5804H193.528L193.53 39.5817ZM216.313 39.5817V11.383H223.54C225.311 11.383 226.845 11.5914 228.141 12.009C229.439 12.4266 230.449 13.1755 231.172 14.2551C231.896 15.334 232.258 16.8598 232.258 18.8336C232.258 19.9947 232.172 21.0268 231.999 21.9312C231.827 22.837 231.508 23.6199 231.043 24.2805C230.578 24.9432 229.915 25.4939 229.05 25.9346L232.647 39.5804H226.651L223.767 26.9436H222.114V39.5804H216.313V39.5817ZM222.114 23.3591H223.734C224.556 23.3591 225.192 23.2023 225.647 22.8893C226.1 22.5762 226.419 22.124 226.602 21.5326C226.786 20.9398 226.877 20.2262 226.877 19.3917C226.877 18.1845 226.672 17.2447 226.262 16.5712C225.852 15.8983 225.095 15.5622 223.994 15.5622H222.114V23.3591ZM243.148 40C241.137 40 239.566 39.6469 238.431 38.9381C237.297 38.2292 236.503 37.1971 236.048 35.8391C235.595 34.4825 235.368 32.8162 235.368 30.8437V11.383H241.105V31.7841C241.105 32.364 241.149 32.9439 241.234 33.5244C241.32 34.1043 241.51 34.5809 241.802 34.951C242.093 35.3224 242.543 35.5091 243.147 35.5091C243.773 35.5091 244.228 35.3231 244.507 34.951C244.788 34.5802 244.967 34.1043 245.043 33.5244C245.118 32.9445 245.156 32.3647 245.156 31.7841V11.383H250.924V30.8437C250.924 32.8162 250.692 34.4811 250.228 35.8391C249.764 37.1971 248.97 38.2292 247.845 38.9381C246.721 39.6469 245.157 40 243.148 40ZM254.619 39.5831V11.3836H260.422V35.3706H266.449V39.5831H254.619ZM268.977 39.5831V11.3836H280.773V15.6308H274.778V22.6285H279.347V26.9449H274.778V35.3699H280.837V39.5817H268.976L268.977 39.5831ZM283.01 39.5831V34.2217H288V39.5831H283.01Z"
                                        fill="white"/>
                                    <path
                                        d="M21.4447 40C19.3363 40 17.669 39.6285 16.4402 38.886C15.2115 38.1436 14.3318 37.0761 13.7986 35.6835C13.266 34.2909 13 32.6318 13 30.7049V20.2267C13 18.2767 13.266 16.6176 13.7986 15.2481C14.3318 13.8787 15.2115 12.8288 16.4402 12.0973C17.669 11.3668 19.3363 11 21.4447 11C23.5764 11 25.2617 11.3657 26.502 12.0973C27.7417 12.8288 28.6285 13.8787 29.1605 15.2481C29.6943 16.6176 29.9603 18.2767 29.9603 20.2267V30.7049C29.9603 32.6318 29.6943 34.2909 29.1605 35.6835C28.6285 37.0761 27.7417 38.1436 26.502 38.886C25.263 39.6285 23.5776 40 21.4447 40ZM21.4447 35.5789C22.0939 35.5789 22.5691 35.4158 22.8705 35.0919C23.1719 34.7669 23.3683 34.3493 23.4611 33.8382C23.5532 33.3281 23.5995 32.8048 23.5995 32.2716V18.6942C23.5995 18.1379 23.5532 17.6091 23.4611 17.1111C23.3683 16.612 23.1719 16.2 22.8705 15.8761C22.5691 15.5511 22.0939 15.3881 21.4447 15.3881C20.8419 15.3881 20.3912 15.55 20.0898 15.8761C19.7884 16.2 19.5913 16.6131 19.4992 17.1111C19.4078 17.6102 19.3608 18.1379 19.3608 18.6942V32.2716C19.3608 32.8048 19.4013 33.3281 19.4825 33.8382C19.5643 34.3482 19.7497 34.7669 20.0395 35.0919C20.3293 35.4158 20.7982 35.5789 21.4473 35.5789H21.4447ZM33.7483 39.5827V11.3845H38.1268L43.897 24.9618V11.3845H49.0406V39.5827H44.8353L39.0664 24.9618V39.5827H33.7483ZM53.2466 39.5827V11.3845H65.8973V15.6327H59.4676V22.6295H64.3684V26.9459H59.4676V35.3706H65.9668V39.5833L53.2466 39.5827ZM85.1521 40C83.3914 40 81.9199 39.6872 80.7382 39.0602C79.5558 38.4332 78.6651 37.4639 78.0623 36.1528C77.4596 34.8418 77.1228 33.1419 77.0545 31.0531L82.3726 30.2532C82.3958 31.4596 82.5046 32.4346 82.703 33.1772C82.8994 33.9197 83.1783 34.454 83.5363 34.779C83.8963 35.104 84.3304 35.2671 84.8398 35.2671C85.4889 35.2671 85.9114 35.0468 86.1091 34.6061C86.3062 34.1643 86.404 33.7016 86.404 33.2135C86.404 32.0523 86.1258 31.0729 85.5701 30.272C85.0136 29.471 84.2602 28.6646 83.3109 27.8527L80.8792 25.7286C79.8134 24.8241 78.915 23.7962 78.1847 22.6482C77.4551 21.4992 77.0899 20.078 77.0899 18.3836C77.0899 15.9929 77.797 14.1652 79.2093 12.9004C80.6229 11.6357 82.5452 11.0033 84.9795 11.0033C86.4852 11.0033 87.6965 11.2589 88.6117 11.769C89.5268 12.2802 90.2216 12.9467 90.6969 13.7708C91.1709 14.5948 91.4955 15.4652 91.6693 16.3818C91.8432 17.2995 91.9418 18.1754 91.9649 19.0104L86.6127 19.6714C86.5901 18.8363 86.5373 18.1103 86.4556 17.4956C86.3751 16.8808 86.207 16.4038 85.952 16.0689C85.6976 15.7318 85.3028 15.5633 84.7709 15.5633C84.1919 15.5633 83.7688 15.8067 83.5015 16.2947C83.2349 16.7828 83.1023 17.2697 83.1023 17.7567C83.1023 18.8011 83.3509 19.6538 83.8487 20.3149C84.3471 20.9758 85.0014 21.6666 85.8135 22.386L88.1415 24.4407C89.3703 25.4851 90.4065 26.6683 91.252 27.9904C92.0983 29.3135 92.5214 30.9495 92.5214 32.8995C92.5214 34.2226 92.22 35.4235 91.6185 36.502C91.0157 37.5806 90.1643 38.4345 89.0637 39.0602C87.9625 39.6872 86.6578 40 85.1521 40ZM98.0803 39.5827V16.0149H93.8403V11.3845H108.541V16.0149H104.337V39.5827H98.0803ZM111.01 39.5827V11.3845H123.661V15.6327H117.231V22.6295H122.132V26.9459H117.231V35.3706H123.73V39.5833L111.01 39.5827ZM126.546 39.5827V11.3845H135.998C137.69 11.3845 139.08 11.7029 140.169 12.3419C141.258 12.9798 142.069 13.9085 142.602 15.1269C143.134 16.3454 143.401 17.825 143.401 19.5657C143.401 21.7471 143.042 23.4128 142.323 24.5608C141.605 25.7098 140.632 26.5041 139.404 26.9459C138.175 27.3877 136.797 27.607 135.267 27.607H132.765V39.5827H126.544H126.546ZM132.767 23.2553H134.853C135.617 23.2553 136.197 23.1165 136.59 22.8377C136.984 22.559 137.244 22.1414 137.373 21.5851C137.5 21.0276 137.564 20.3203 137.564 19.461C137.564 18.7416 137.512 18.1093 137.407 17.5639C137.303 17.0174 137.054 16.5823 136.661 16.2584C136.265 15.9334 135.652 15.7704 134.818 15.7704H132.767V23.2553ZM139.648 39.5827V34.2215H145V39.5827H139.648Z"
                                        fill="#F1A035"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div className="px-4 w-full h-[331px]">
                        <Image
                            src="/assets/images/shape-1.svg"
                            alt="Trading Platform Interface"
                            width={391}
                            height={331}
                            className="relative animate-rotate-animation"
                        />
                    </div>
                </section>
                <section className="flex justify-evenly w-full gap-8">
                    <Card
                        className="w-[605px] h-[182px] grow shrink p-4 bg-[#1e1e1e]/70 rounded-2xl border border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex">
                        <div className="grid grid-cols-[1fr_auto]">
                            <div>
                                <div className="bg-teal-500 rounded-lg h-7 px-3 py-0.5 inline-flex">
                                    <div
                                        className="text-xs text-[#131210] font-medium leading-6 break-words">NEW FEATURE
                                    </div>
                                </div>
                                <h2 className="text-white text-xl mt-4 leading-6">MEGAX - TRADE SMARTER, TRADE
                                    FASTER</h2>
                                <p className="text-stone-400 mt-2.5 leading-6">
                                    MegaX delivers advanced tools and lightning-fast execution for seamless futures
                                    trading.
                                    Elevate your strategies with our proprietary platform.
                                </p>
                            </div>
                            <div className="flex justify-end flex-col">
                                <div className="py-3 flex items-center justify-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <mask id="mask0_3193_453" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse"
                                              x="-1"
                                              y="0" width="25" height="24">
                                            <rect x="-0.5" width="24" height="24" fill="#D9D9D9"/>
                                        </mask>
                                        <g mask="url(#mask0_3193_453)">
                                            <path d="M12.1 12L7.5 7.4L8.9 6L14.9 12L8.9 18L7.5 16.6L12.1 12Z"
                                                  fill="#2DD4BF"/>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </Card>

                    <div className="w-full max-w-[590px] grid content-end">
                        <div className="flex flex-col h-fit">
                            <Image
                                src="/assets/images/plus.svg"
                                alt="Plus icons"
                                width={88}
                                height={24}
                                className="relative"
                            />
                            <p className="text-mgt-gray-light text-xl py-4 leading-8">
                                MegaTrader offers a seamless, powerful platform designed to empower traders with expert
                                tools and reliable support.
                            </p>
                            <div className="flex items-center gap-3">
                                <Link className="!bg-[#ffb34a] text-slate-950">
                                    OPEN AN ACCOUNT
                                </Link>
                                <Link>
                                    JOIN DISCORD
                                </Link>
                                <Link className="!px-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <mask id="mask0_3161_757" maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                                              height="24">
                                            <rect width="24" height="24" fill="#D9D9D9"/>
                                        </mask>
                                        <g mask="url(#mask0_3161_757)">
                                            <path d="M8 19V5L19 12L8 19Z" fill="white"/>
                                        </g>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="my-10 flex justify-between py-12 opacity-30 items-center gap-16">
                    <Image
                        src="/assets/images/mega-x.svg"
                        alt="Trading Platform Interface"
                        width={151}
                        height={56}
                        className="relative"
                    />

                    <Image
                        src="/assets/images/ninjatrader.svg"
                        alt="Trading Platform Interface"
                        width={281}
                        height={36}
                        className="relative"
                    />

                    <Image
                        src="/assets/images/tradovate.svg"
                        alt="Trading Platform Interface"
                        width={185}
                        height={56}
                        className="relative"
                    />

                    <Image
                        src="/assets/images/quantower.svg"
                        alt="Trading Platform Interface"
                        width={235}
                        height={52}
                        className="relative"
                    />
                </section>
                <section className="my-8 grid grid-cols-3 gap-4 px-4 py-8">
                    <div>
                        <h2 className="text-white text-5xl mb-[68px] leading-[60px]">
                            UNLOCK<br/>THE POWER<br/>OF TRADING
                        </h2>
                        <div className="space-y-[15px]">
                            <Card
                                className=" p-4 bg-[#1e1e1e]/70 rounded-2xl border border-neutral-700 w-full">
                                <Image
                                    src="/assets/images/frame_monitor.svg"
                                    alt="Trading Platform Interface"
                                    width={120}
                                    height={120}
                                    className="mb-16"
                                />
                                <div className="text-white text-xl leading-6 font-medium py-2.5">1. CREATE YOUR
                                    MEGATRADER
                                    ACCOUNT
                                </div>
                                <div
                                    className="max-w-[325px] w-full font-normal text-base leading-6 text-stone-400">Sign
                                    up and
                                    complete
                                    the
                                    onboarding process
                                </div>
                            </Card>
                            <Card
                                className=" p-4 bg-[#1e1e1e]/70 rounded-2xl border border-neutral-700 w-full">
                                <Image
                                    src="/assets/images/frame_account.svg"
                                    alt="Trading Platform Interface"
                                    width={120}
                                    height={120}
                                    className="mb-16"
                                />
                                <div className="text-white text-xl leading-6 font-medium py-2.5">2. SET YOUR ACCOUNT
                                    SIZE
                                </div>
                                <div className="w-full font-normal text-base leading-6 text-stone-400">
                                    Choose your account size to match your trading strategy and risk level
                                </div>
                            </Card>
                        </div>
                    </div>
                    <div>
                        <div className="h-[122px]">
                        </div>
                        <div className="space-y-[15px]">
                            <Card
                                className=" p-4 bg-[#1e1e1e]/70 rounded-2xl border border-neutral-700 w-full">
                                <Image
                                    src="/assets/images/frame_trading.svg"
                                    alt="Trading Platform Interface"
                                    width={120}
                                    height={120}
                                    className="mb-16"
                                />
                                <div className="text-white text-xl leading-6 font-medium py-2.5">
                                    3. SELECT YOUR TRADING PLATFORM
                                </div>
                                <div className="w-full font-normal text-base leading-6 text-stone-400">
                                    Pick MegaX or another platform that suits your trading preferences
                                </div>
                            </Card>
                            <Card
                                className=" p-4 bg-[#1e1e1e]/70 rounded-2xl border border-neutral-700 w-full">
                                <Image
                                    src="/assets/images/frame_plan.svg"
                                    alt="Trading Platform Interface"
                                    width={120}
                                    height={120}
                                    className="mb-16"
                                />
                                <div className="text-white text-xl leading-6 font-medium py-2.5">
                                    4. PLAN YOUR TRADES
                                </div>
                                <div className="w-full font-normal text-base leading-6 text-stone-400">
                                    Utilize advanced tools to analyze the market and strategize your trades
                                </div>
                            </Card>
                        </div>
                    </div>
                    <div>
                        <div className="space-y-[15px]">
                            <Card
                                className=" p-4 bg-[#1e1e1e]/70 rounded-2xl border border-neutral-700 w-full">
                                <Image
                                    src="/assets/images/frame_plan.svg"
                                    alt="Trading Platform Interface"
                                    width={120}
                                    height={120}
                                    className="mb-16"
                                />
                                <div className="text-white text-xl leading-6 font-medium py-2.5">
                                    5. EXECUTE YOUR TRADES
                                </div>
                                <div className="max-w-[325px] w-full font-normal text-base leading-6 text-stone-400">
                                    Benefit from fast and reliable trade execution for a seamless experience
                                </div>
                            </Card>
                            <Card
                                className=" p-4 bg-[#1e1e1e]/70 rounded-2xl border border-neutral-700 w-full">
                                <Image
                                    src="/assets/images/frame_monitor.svg"
                                    alt="Trading Platform Interface"
                                    width={120}
                                    height={120}
                                    className="mb-16"
                                />
                                <div className="text-white text-xl leading-6 font-medium py-2.5">
                                    6. MONITOR AND OPTIMIZE
                                </div>
                                <div className="w-full font-normal text-base leading-6 text-stone-400">
                                    Review your trades and fine-tune your strategies for continuous improvement
                                </div>
                            </Card>
                        </div>
                    </div>
                </section>
                <AccountSize/>
                <UnlockThePowerOfMegatrader/>
                <section className="my-10 h-[72px] bg-slate-500 opacity-20">
                </section>
                <section className="my-10 h-[480px]">
                    <div className="grid grid-cols-3 gap-5">
                        <Card
                            className="p-4 bg-[#1e1e1e]/70 rounded-2xl border border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex">
                            <h2 className="text-white text-[32px]">
                                ONE-STEP EVALUATION
                            </h2>
                            <div className="text-stone-400 text-base font-normal leading-normal">
                                Simplify your path to a funded trading account with a streamlined one-step evaluation process. Prove your consistency and trading skills quickly and efficiently, with clear profit targets and defined trading rules.
                            </div>
                        </Card>
                        <Card
                            className="p-4 bg-[#1e1e1e]/70 rounded-2xl border border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex">
                            <h2 className="text-white text-[32px]">
                                INSTANT PAYOUTS
                            </h2>
                            <div className="text-stone-400 text-base font-normal leading-normal">
                                Enjoy the flexibility of accessing your earnings with instant payout options. Withdraw your profits quickly and efficiently, ensuring you have complete control over your trading income whenever you need it.
                            </div>
                        </Card>
                        <Card
                            className="p-4 bg-[#1e1e1e]/70 rounded-2xl border border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex">
                            <h2 className="text-white text-[32px]">
                                ONE-STEP EVALUATION
                            </h2>
                            <div className="text-stone-400 text-base font-normal leading-normal mb-4">
                                Simplify your path to a funded trading account with a streamlined one-step evaluation process. Prove your consistency and trading skills quickly and efficiently, with clear profit targets and defined trading rules.
                            </div>

                            <Image
                                src="/assets/images/people.svg"
                                alt="Trading Platform Interface"
                                width={411}
                                height={240}
                            />
                        </Card>
                    </div>

                </section>
            </main>
            <footer className="pt-8 pb-[18px] bg-[#1e1e1e] w-full">
                <div className="h-[104px]">
                    <svg width="264" height="32" viewBox="0 0 264 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M94.2653 5.67529H96.9501V26.325H94.1532V10.8594L94.2939 11.7682L89.2629 23.4563H87.2663L82.2353 12.0526L82.3761 10.8594V26.325H79.5791V5.67529H82.2639L88.2646 19.3099L94.2653 5.67529Z"
                            fill="white"/>
                        <path
                            d="M101.986 5.67529H104.853V26.325H101.986V5.67529ZM103.377 5.67529H115.113V8.43065H103.377V5.67529ZM103.377 14.6947H113.567V17.45H103.377V14.6947ZM103.377 23.5696H115.113V26.325H103.377V23.5696Z"
                            fill="white"/>
                        <path
                            d="M132.978 15.2056V18.8121C132.978 20.3453 132.684 21.6985 132.092 22.8673C131.503 24.0361 130.665 24.9405 129.583 25.5805C128.502 26.2204 127.248 26.5382 125.823 26.5382C124.399 26.5382 123.114 26.2382 122.022 25.636C120.931 25.0338 120.084 24.185 119.484 23.0873C118.884 21.9896 118.585 20.7164 118.585 19.2676V13.1747C118.585 11.6415 118.879 10.2927 119.471 9.1283C120.06 7.96394 120.898 7.06178 121.98 6.42183C123.062 5.78187 124.315 5.46411 125.74 5.46411C126.91 5.46411 127.991 5.71743 128.979 6.22406C129.968 6.73069 130.795 7.44842 131.459 8.37502C132.125 9.30384 132.574 10.3771 132.809 11.5992H129.704C129.535 10.9082 129.249 10.3149 128.847 9.81714C128.444 9.31939 127.972 8.94165 127.426 8.68167C126.883 8.42168 126.32 8.29058 125.74 8.29058C124.895 8.29058 124.157 8.49501 123.519 8.90165C122.881 9.30829 122.389 9.87936 122.044 10.6126C121.696 11.3459 121.525 12.2014 121.525 13.1769V19.2698C121.525 20.1609 121.703 20.9386 122.059 21.6052C122.415 22.274 122.919 22.7918 123.57 23.1606C124.22 23.5295 124.972 23.7139 125.826 23.7139C126.679 23.7139 127.409 23.5228 128.046 23.1384C128.684 22.7562 129.174 22.1985 129.522 21.4697C129.867 20.7408 130.041 19.8742 130.041 18.8698V17.9743H125.896V15.2056H132.978Z"
                            fill="white"/>
                        <path
                            d="M143.036 5.67529H145.481L153.015 26.325H149.923L144.259 9.66612L138.594 26.325H135.503L143.036 5.67529ZM138.975 19.041H149.725V21.7964H138.975V19.041Z"
                            fill="white"/>
                        <path
                            d="M155.497 5.67529H170.196V8.9284H155.497V5.67529ZM161.175 7.36629H164.519V26.325H161.175V7.36629Z"
                            fill="#F1A035"/>
                        <path
                            d="M173.255 5.66189H176.613V26.3249H173.255V5.66189ZM174.647 14.5524H181.688C182.156 14.5524 182.572 14.4368 182.932 14.2035C183.293 13.9724 183.572 13.6435 183.768 13.2169C183.964 12.7903 184.067 12.2948 184.078 11.7259C184.078 11.1682 183.979 10.6749 183.783 10.2482C183.588 9.8216 183.308 9.49274 182.948 9.26164C182.587 9.03055 182.167 8.91278 181.69 8.91278H174.649V5.65967H181.789C182.941 5.65967 183.953 5.91076 184.823 6.41295C185.694 6.91514 186.371 7.62398 186.855 8.54391C187.337 9.46162 187.579 10.5216 187.579 11.7259C187.579 12.9303 187.337 14.0035 186.855 14.9212C186.371 15.839 185.694 16.55 184.817 17.0522C183.942 17.5544 182.93 17.8055 181.789 17.8055H174.649V14.5524H174.647ZM179.579 17.1655L183.064 16.5122L188.432 26.3249H184.399L179.579 17.1633V17.1655Z"
                            fill="#F1A035"/>
                        <path
                            d="M199.008 5.67529H201.453L209.127 26.325H205.515L200.231 10.9438L194.947 26.325H191.334L199.008 5.67529ZM194.947 19.1543H205.697V22.4075H194.947V19.1543Z"
                            fill="#F1A035"/>
                        <path
                            d="M212.569 5.67749H215.926V26.3271H212.569V5.67749ZM214.508 23.074H219.273C220.658 23.074 221.736 22.7229 222.499 22.023C223.262 21.323 223.644 20.3276 223.644 19.041V12.9636C223.644 11.6771 223.262 10.6816 222.499 9.98162C221.736 9.28167 220.66 8.93059 219.273 8.93059H214.508V5.67749H219.187C220.854 5.67749 222.281 5.96636 223.466 6.54409C224.651 7.12183 225.553 7.964 226.171 9.07281C226.789 10.1816 227.099 11.5149 227.099 13.077V18.9277C227.099 20.452 226.793 21.7675 226.186 22.8763C225.577 23.9829 224.68 24.8361 223.495 25.4317C222.31 26.0272 220.869 26.3271 219.174 26.3271H214.508V23.074Z"
                            fill="#F1A035"/>
                        <path
                            d="M231.518 5.67529H234.876V26.325H231.518V5.67529ZM232.908 5.67529H244.925V8.9284H232.908V5.67529ZM232.908 14.4525H243.379V17.69H232.908V14.4525ZM232.908 23.0741H244.925V26.3272H232.908V23.0741Z"
                            fill="#F1A035"/>
                        <path
                            d="M248.823 5.66189H252.181V26.3249H248.823V5.66189ZM250.213 14.5524H257.254C257.722 14.5524 258.138 14.4368 258.498 14.2035C258.859 13.9724 259.138 13.6435 259.334 13.2169C259.53 12.7903 259.633 12.2948 259.644 11.7259C259.644 11.1682 259.545 10.6749 259.349 10.2482C259.154 9.8216 258.874 9.49274 258.514 9.26164C258.153 9.03055 257.733 8.91278 257.256 8.91278H250.215V5.65967H257.355C258.507 5.65967 259.519 5.91076 260.389 6.41295C261.26 6.91514 261.937 7.62398 262.421 8.54391C262.903 9.46162 263.145 10.5216 263.145 11.7259C263.145 12.9303 262.903 14.0035 262.421 14.9212C261.937 15.839 261.26 16.55 260.383 17.0522C259.508 17.5544 258.496 17.8055 257.355 17.8055H250.215V14.5524H250.213ZM255.147 17.1655L258.633 16.5122L264 26.3249H259.967L255.147 17.1633V17.1655Z"
                            fill="#F1A035"/>
                        <path
                            d="M65.6844 14.6701L63.6021 12.4836C63.5098 12.3903 63.3866 12.3369 63.2613 12.3369H62.0475C61.8804 12.3369 61.7243 12.2636 61.6143 12.1347L59.244 9.35936L58.714 8.7483C57.1968 7.00842 55.5696 5.36854 53.8413 3.84198C53.8413 3.84198 52.9508 3.0087 51.6073 2.13098C50.2616 1.25327 48.4651 0.333334 46.6555 0.153347C46.6555 0.153347 45.8727 -0.00219848 44.6017 2.3583e-05C42.9262 0.00891185 41.2638 0.302226 39.685 0.862186C38.0667 1.4377 34.8981 2.61318 32.8576 3.71977C32.7762 3.76421 32.6905 3.79976 32.6025 3.82865L26.3863 5.85961L15.8999 9.06383C15.7966 9.09494 15.6998 9.15049 15.6251 9.23049C14.6268 10.2393 7.27159 17.3277 2.82329 11.9525C2.82329 11.9525 7.00113 11.7125 6.82742 7.15508C6.82742 7.15508 6.55256 5.48186 7.40352 4.72191L7.43431 4.67748C7.81691 4.13529 7.75754 3.39757 7.29578 2.91982L7.27599 2.90427C6.88019 2.49763 6.2909 2.37097 5.77197 2.5754C3.7688 3.36867 -0.883999 5.69963 0.147268 10.0171C0.178052 10.1504 0.17805 10.2926 0.145067 10.426C-0.0154501 11.077 -0.296904 13.2591 2.55722 15.0367C4.12501 16.01 5.57627 16.2411 6.93077 16.2989C9.55621 16.41 11.6803 15.5478 12.971 14.8523L6.12818 29.7157C6.10619 29.7646 6.093 29.8113 6.093 29.8624V31.3023C6.093 31.4911 6.24472 31.6445 6.43163 31.6445H13.4372C13.8902 31.6445 14.2552 31.2734 14.2552 30.8179V27.4048C14.2552 27.3181 14.2838 27.2381 14.3343 27.1692C15.071 26.1982 15.9989 25.2116 17.1731 24.3272C22.7604 20.1186 29.722 21.3919 31.7142 21.8385C31.7933 21.854 31.8505 21.9252 31.8505 22.0074V24.5761C31.8505 24.7161 31.8879 24.8538 31.9604 24.9716L34.4847 29.0402C34.5441 29.1358 34.5771 29.2469 34.5771 29.3624V31.1378C34.5771 31.6134 34.9597 32 35.4303 32H40.2414C40.6196 32 40.9296 31.6867 40.9296 31.3045V30.3535C40.9296 30.2712 40.9076 30.1868 40.8636 30.1135L39.5993 27.9314C38.557 26.1337 38.9858 23.8339 40.6108 22.5495C42.3391 21.183 45.2724 19.8764 50.156 19.8697C50.4397 19.8697 50.6772 20.0897 50.7212 20.3719C50.8927 21.4007 51.9569 23.9361 58.05 26.2737C58.448 26.4248 58.8899 26.2293 59.0549 25.8338L62.2432 18.0388C62.285 17.9365 62.3531 17.8499 62.4455 17.7921L65.5481 15.8345C65.9527 15.5789 66.0231 15.0168 65.691 14.6723L65.6844 14.6701ZM61.3087 15.5234L59.8552 16.1456C59.7959 16.1722 59.7519 16.2233 59.7343 16.2856C58.9779 19.1942 58.0588 21.1119 57.509 22.094C57.254 22.554 56.6647 22.7051 56.2205 22.4229C55.209 21.7874 54.4482 21.0141 53.8721 20.2319C52.7375 18.6854 52.1394 16.8055 52.1394 14.8834V12.7747C52.1394 12.7147 52.0888 12.6636 52.0251 12.6636H50.2924C50.2352 12.6636 50.1846 12.7058 50.1824 12.7658L49.9582 14.9434C49.9362 15.1612 49.7844 15.339 49.5755 15.3945L44.9293 16.63C44.5445 16.7344 44.1641 16.4455 44.1641 16.0389V14.7679C44.1641 14.7079 44.1136 14.6568 44.0498 14.6634C38.5438 14.9834 36.1449 22.9406 35.6589 24.8094C35.6215 24.9472 35.4368 24.9694 35.3687 24.8449L34.6628 23.565C34.5991 23.4539 34.5683 23.3295 34.5683 23.2051V14.9967C34.5683 14.959 34.5397 14.9301 34.5045 14.9301H33.4974C33.4755 14.9301 33.4535 14.9434 33.4447 14.9612L32.2353 17.0211C32.1979 17.0877 32.1276 17.1299 32.0528 17.1299H25.3221C25.0714 17.1299 24.8691 16.9255 24.8691 16.6766V14.5501C24.8691 14.5012 24.8339 14.4679 24.7899 14.4679H23.2507C23.2156 14.4679 23.187 14.4879 23.176 14.519C21.7907 17.8921 14.154 22.5918 13.8726 22.7651C13.866 22.7673 13.8594 22.7717 13.8572 22.7717L13.7648 22.8006C13.7011 22.8206 13.6439 22.7562 13.6725 22.6962L18.3495 12.2592C18.543 11.8259 18.9146 11.497 19.3697 11.3614L32.3914 7.61282C32.4838 7.58616 32.5739 7.65504 32.5739 7.75059V8.79274C32.5739 8.88829 32.6487 8.96828 32.7476 8.96828H33.614C33.6712 8.96828 33.7283 8.93717 33.7591 8.88829C36.1075 5.28187 40.6811 3.97085 40.6811 3.97085C49.6415 1.34882 54.3823 8.88607 54.3823 8.88607L59.5056 14.6723C59.5364 14.7079 59.5716 14.7323 59.6156 14.7479L61.3087 15.4367C61.3461 15.4523 61.3505 15.5078 61.3087 15.5256V15.5234Z"
                            fill="white"/>
                        <path
                            d="M61.3087 15.5235L59.8552 16.1457C59.7959 16.1723 59.7519 16.2234 59.7343 16.2857C58.9779 19.1943 58.0588 21.112 57.509 22.0941C57.254 22.5541 56.6647 22.7052 56.2205 22.423C55.209 21.7875 54.4482 21.0142 53.8721 20.232C52.7375 18.6855 52.1394 16.8056 52.1394 14.8835V12.7748C52.1394 12.7148 52.0888 12.6637 52.0251 12.6637H50.2924C50.2352 12.6637 50.1846 12.7059 50.1824 12.7659L49.9581 14.9435C49.9362 15.1613 49.7844 15.3391 49.5755 15.3946L44.9293 16.6301C44.5445 16.7345 44.1641 16.4456 44.1641 16.039V14.768C44.1641 14.708 44.1136 14.6569 44.0498 14.6635C38.5438 14.9835 36.1449 22.9407 35.6589 24.8095C35.6215 24.9473 35.4368 24.9695 35.3687 24.845L34.6628 23.5651C34.5991 23.454 34.5683 23.3296 34.5683 23.2052V14.9968C34.5683 14.9591 34.5397 14.9302 34.5045 14.9302H33.4974C33.4754 14.9302 33.4535 14.9435 33.4447 14.9613L32.2353 17.0212C32.1979 17.0878 32.1275 17.13 32.0528 17.13H25.3221C25.0714 17.13 24.8691 16.9256 24.8691 16.6767V14.5502C24.8691 14.5013 24.8339 14.468 24.7899 14.468H23.2507C23.2156 14.468 23.187 14.488 23.176 14.5191C21.7907 17.8922 14.154 22.5919 13.8726 22.7652C13.866 22.7674 13.8594 22.7719 13.8572 22.7719L13.7648 22.8007C13.7011 22.8207 13.6439 22.7563 13.6725 22.6963L18.3495 12.2593C18.543 11.826 18.9146 11.4971 19.3697 11.3616L32.3914 7.61293C32.4838 7.58626 32.5739 7.65515 32.5739 7.75069V8.79284C32.5739 8.88839 32.6487 8.96839 32.7476 8.96839H33.614C33.6712 8.96839 33.7283 8.93728 33.7591 8.88839C36.1075 5.28198 40.6811 3.97096 40.6811 3.97096C49.6415 1.34892 54.3823 8.88617 54.3823 8.88617L59.5056 14.6724C59.5364 14.708 59.5716 14.7324 59.6156 14.748L61.3087 15.4368C61.3461 15.4524 61.3505 15.5079 61.3087 15.5257V15.5235Z"
                            fill="#F1A035"/>
                        <path
                            d="M17.5535 25.2382V31.1444C17.5535 31.3933 17.7536 31.5955 17.9998 31.5955H24.7921C25.0824 31.5955 25.3155 31.3578 25.3155 31.0667V29.8712C25.3155 29.8068 25.3001 29.7445 25.2715 29.6868L24.1633 27.5336C24.0973 27.4069 24.0995 27.2536 24.1655 27.1269L26.8085 22.1562C26.8371 22.1006 26.7953 22.034 26.7337 22.0362C25.8432 22.0806 20.9331 22.4339 17.6744 24.9849C17.5974 25.0449 17.5513 25.1404 17.5513 25.2382H17.5535Z"
                            fill="white"/>
                        <path
                            d="M41.7124 29.7935V31.1934C41.7124 31.6378 42.0686 32 42.5106 32H46.4707C46.7874 32 47.0446 31.7401 47.0446 31.4201V29.5047C47.0446 29.3869 47.005 29.2736 46.9325 29.1825L45.0657 26.8471C44.7336 26.4315 44.7468 25.836 45.0964 25.4361L49.0698 20.8919C49.2039 20.7386 49.0918 20.4964 48.8873 20.5031C47.469 20.5564 44.3378 21.0941 42.5919 22.1207C42.5919 22.1207 40.6679 23.254 39.9423 24.8094C39.729 25.2672 39.729 25.7071 39.729 25.7071C39.729 26.0582 39.8214 26.4049 39.9995 26.7071L41.6442 29.5246C41.6926 29.6046 41.7168 29.698 41.7168 29.7913L41.7124 29.7935Z"
                            fill="white"/>
                    </svg>

                </div>
            </footer>
        </>
    )

export default Home;