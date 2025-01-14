import Card from "@/components/Card";
import Image from "next/image";

interface Item {
    title: string;
    content: string;
    image: string;
}

const ITEMS: Item[] = [
    {
        title: '1. CREATE YOUR MEGATRADER ACCOUNT',
        content: 'Sign up and complete the onboarding process',
        image: '/assets/images/frame_monitor.svg'
    },
    {
        title: '2. SET YOUR ACCOUNT SIZE',
        content: 'Choose your account size to match your trading strategy and risk level',
        image: '/assets/images/frame_account.svg'
    },
    {
        title: '3. SELECT YOUR TRADING PLATFORM',
        content: 'Pick MegaX or another platform that suits your trading preferences',
        image: '/assets/images/frame_trading.svg'
    },
    {
        title: '4. PLAN YOUR TRADES',
        content: 'Utilize advanced tools to analyze the market and strategize your trades',
        image: '/assets/images/frame_plan.svg'
    },
    {
        title: '5. EXECUTE YOUR TRADES',
        content: 'Benefit from fast and reliable trade execution for a seamless experience',
        image: '/assets/images/frame_execute.svg'
    },
    {
        title: '6. MONITOR AND OPTIMIZE',
        content: 'Review your trades and fine-tune your strategies for continuous improvement',
        image: '/assets/images/star.svg'
    },
];

const TradingStepsSection = ({className = ''}: { className?: string }) => {
    return <>
        <section id="how-it-works" className={className}>
            <div className="grid mb-8 gap-8 md:grid-cols-2 lg:grid-cols-3 lg:px-4 lg:gap-4">
                <div>
                    <h2 className="text-white text-[32px] leading-10 font-light lg:text-5xl mb-8 lg:mb-[68px] lg:leading-[60px]">
                        UNLOCK<br/>THE POWER<br/>OF TRADING
                    </h2>
                    <div className="space-y-8 lg:space-y-[15px]">
                        {[ITEMS[0], ITEMS[1]].map((item, index) => (
                            <Card
                                key={index}
                                className="w-full">

                                <Image
                                    src={item.image}
                                    alt="Trading Platform Interface"
                                    width={120}
                                    height={120}
                                    className="mb-16"
                                />

                                <div className="text-white text-xl leading-6 font-medium py-2.5">{item.title}
                                </div>
                                <div
                                    className="max-w-80 w-full font-normal text-base leading-6 text-stone-400">
                                    {item.content}
                                </div>
                            </Card>
                        ))}
                    </div>
                </div>
                <div>
                    <div className="lg:h-[122px]">
                    </div>
                    <div className="space-y-8 lg:space-y-[15px]">
                        {[ITEMS[2], ITEMS[3]].map((item, index) => (
                            <Card
                                key={index}
                                className="w-full">

                                <Image
                                    src={item.image}
                                    alt="Trading Platform Interface"
                                    width={120}
                                    height={120}
                                    className="mb-16"
                                />

                                <div className="text-white text-xl leading-6 font-medium py-2.5">{item.title}
                                </div>
                                <div
                                    className="max-w-80 w-full font-normal text-base leading-6 text-stone-400">
                                    {item.content}
                                </div>
                            </Card>
                        ))}
                    </div>
                </div>
                <div>
                    <div className="space-y-8 lg:space-y-[15px]">
                        {[ITEMS[4], ITEMS[5]].map((item, index) => (
                            <Card
                                key={index}
                                className="w-full">

                                <Image
                                    src={item.image}
                                    alt="Trading Platform Interface"
                                    width={120}
                                    height={120}
                                    className="mb-16"
                                />

                                <div className="text-white text-xl leading-6 font-medium py-2.5">{item.title}
                                </div>
                                <div
                                    className="max-w-80 w-full font-normal text-base leading-6 text-stone-400">
                                    {item.content}
                                </div>
                            </Card>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    </>
}

export default TradingStepsSection;