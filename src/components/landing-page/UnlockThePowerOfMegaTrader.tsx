import Image from "next/image";
import Link from "@/components/link";
import clsx from "clsx";

const UnlockThePowerOfMegaTrader = ({className = ''}: { className?: string }) => {
    return <>
        <section id="features" className={clsx('gap-8 grid mb-8 lg:grid-cols-[511px_1fr] lg:gap-4', className)}>
            <div>
                <div className="px-4">
                    <h2 className="w-[328px] text-left text-[32px] leading-10 lg:w-full lg:text-5xl font-light text-white mb-8 lg:mb-10 lg:leading-[60px]">
                        UNLOCK THE POWER OF MEGATRADER
                    </h2>
                </div>

                <div className="space-y-8 mb-8 lg:mb-[76px] px-4">
                    <Image
                        src="/assets/images/plus.svg"
                        alt="Plus icons"
                        width={88}
                        height={24}
                        className="relative"
                    />
                    <p className="text-stone-400 text-xl font-normal">
                        Experience the full potential of your trading platform with our intuitive and advanced user
                        interface.
                    </p>
                    <Link as={"button"}
                          className="!bg-mgt-primary px-4 group-[.isPremium]:!bg-stone-800 text-slate-950 group-[.isPremium]:text-white">
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
            <div className="px-4">
                <Image
                    src="/assets/images/mac.svg"
                    alt="Plus icons"
                    width={720}
                    height={900}
                    className="relative"
                />
            </div>
        </section>
    </>
}

export default UnlockThePowerOfMegaTrader;