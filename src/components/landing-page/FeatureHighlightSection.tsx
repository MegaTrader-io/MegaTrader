import Card from "@/components/card";
import Link from "@/components/link";
import Image from "next/image";
import clsx from "clsx";

const FeatureHighlightSection = ({className = ''}: { className?: string }) => (
    <section className={clsx('xl:flex lg:mt-[15px] justify-evenly w-full gap-16', className)}>
        <Card
            className="xl:w-[605px] xl:h-[182px] my-8 lg:my-0 grow shrink flex-col justify-start items-start gap-2.5 inline-flex"
        >
            <div>
                <div className="bg-teal-500 rounded-lg h-7 px-3 py-0.5 inline-flex">
                    <div className="text-xs text-[#131210] font-medium leading-6 break-words">NEW FEATURE</div>
                </div>
                <h2 className="text-white text-xl mt-4 leading-6">MEGAX - TRADE SMARTER, TRADE FASTER</h2>
                <p className="text-stone-400 mt-2.5 leading-6">
                    Empowers traders with advanced tools and lightning-fast execution for a seamless futures trading
                    experience. Elevate your strategies with our cutting-edge proprietary platform.
                </p>
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
                    Join our platform today and take your trading to the next level. Simple, reliable, and designed for
                    traders like you.
                </p>
                <div className="grid grid-cols-2 gap-4 md:flex lg:items-center lg:gap-3">
                    <Link className="!bg-mgt-primary text-slate-950">OPEN AN ACCOUNT</Link>
                    <Link>JOIN DISCORD</Link>
                    <Link className="hidden md:block !px-3">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <mask
                                id="mask0_3161_757"
                                maskUnits="userSpaceOnUse"
                                x="0"
                                y="0"
                                width="24"
                                height="24"
                            >
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
);

export default FeatureHighlightSection;
