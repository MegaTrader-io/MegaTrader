import Card from "@/components/card";
import Image from "next/image";

const TradingStepsSection = () => {
    return <>
        <section className="mt-8 grid grid-cols-3 gap-4 px-4">
            <div>
                <h2 className="text-white text-5xl mb-[68px] leading-[60px]">
                    UNLOCK<br/>THE POWER<br/>OF TRADING
                </h2>
                <div className="space-y-[15px] mb-[68px]">
                    <Card
                        className="w-full">

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
                        className="w-full">
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
                        className=" w-full">
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
                        className=" w-full">
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
                        className=" w-full">
                        <Image
                            src="/assets/images/frame_execute.svg"
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
                        className=" w-full">
                        <Image
                            src="/assets/images/star.svg"
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
    </>
}

export default TradingStepsSection;