import Card from "@/components/card";
import Link from "@/components/link";
import Image from "next/image";

const FeatureHighlightSection = () => (
    <section className="flex justify-evenly w-full gap-8">
        <Card
            className="w-[605px] h-[182px] grow shrink p-4 bg-[#1e1e1e]/70 border border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex"
        >
            <div className="grid grid-cols-[1fr_auto]">
                <div>
                    <div className="bg-teal-500 rounded-lg h-7 px-3 py-0.5 inline-flex">
                        <div className="text-xs text-[#131210] font-medium leading-6 break-words">NEW FEATURE</div>
                    </div>
                    <h2 className="text-white text-xl mt-4 leading-6">MEGAX - TRADE SMARTER, TRADE FASTER</h2>
                    <p className="text-stone-400 mt-2.5 leading-6">
                        MegaX delivers advanced tools and lightning-fast execution for seamless futures trading. Elevate your
                        strategies with our proprietary platform.
                    </p>
                </div>
                <div className="flex justify-end flex-col">
                    <div className="py-3 flex items-center justify-center">
                        <svg
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <mask
                                id="mask0_3193_453"
                                style={{ maskType: "alpha" }}
                                maskUnits="userSpaceOnUse"
                                x="-1"
                                y="0"
                                width="25"
                                height="24"
                            >
                                <rect x="-0.5" width="24" height="24" fill="#D9D9D9" />
                            </mask>
                            <g mask="url(#mask0_3193_453)">
                                <path
                                    d="M12.1 12L7.5 7.4L8.9 6L14.9 12L8.9 18L7.5 16.6L12.1 12Z"
                                    fill="#2DD4BF"
                                />
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
                    MegaTrader offers a seamless, powerful platform designed to empower traders with expert tools and reliable
                    support.
                </p>
                <div className="flex items-center gap-3">
                    <Link className="!bg-[#ffb34a] text-slate-950">OPEN AN ACCOUNT</Link>
                    <Link>JOIN DISCORD</Link>
                    <Link className="!px-3">
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
                                <rect width="24" height="24" fill="#D9D9D9" />
                            </mask>
                            <g mask="url(#mask0_3161_757)">
                                <path d="M8 19V5L19 12L8 19Z" fill="white" />
                            </g>
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </section>
);

export default FeatureHighlightSection;
