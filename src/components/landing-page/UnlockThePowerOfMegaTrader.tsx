import Image from "next/image";
import Link from "@/components/link";

const UnlockThePowerOfMegaTrader = () => {
    return <>
        <section className="mb-8 grid grid-cols-2">
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
                        Experience the full potential of your trading platform with our intuitive and advanced user
                        interface.
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

export default UnlockThePowerOfMegaTrader;