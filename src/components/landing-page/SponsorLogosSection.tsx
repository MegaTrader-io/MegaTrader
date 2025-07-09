import Image from "next/image";
import clsx from "clsx";

const SponsorLogosSection = ({className = ""}: { className?: string }) => (
    <section id="sponsor" className={clsx("space-y-4", className)}>
        <div className="self-stretch text-center text-white text-[40px] font-light uppercase leading-[48px]">
            Trusted Platforms
        </div>

        <div
            className="mx-auto max-w-[760px] text-center text-xl leading-8 font-medium text-stone-400 md:w-[760px]">
            Trade with confidence on industry-leading platforms trusted by
            professionals for their speed, reliability, and advanced trading
            capabilities.
        </div>

        <div
            className="my-8 flex flex-col items-center gap-16 py-12 md:grid md:grid-cols-2 md:gap-12 lg:my-auto lg:flex lg:flex-row lg:justify-between lg:gap-8 lg:py-12 xl:gap-16">
            <div className="contents md:flex md:justify-self-end">
                <Image
                    src="/assets/images/mega-trader-x.svg"
                    alt="ProjectX Sponsor Logo"
                    width={224}
                    height={54}
                />
            </div>
            <div className="contents md:flex md:justify-self-start">
                <Image
                    src="/assets/images/ninjatrader.svg"
                    alt="NinjaTrader Sponsor Logo"
                    width={281}
                    height={36}
                />
            </div>
            <div className="contents md:flex md:justify-self-end">
                <Image
                    src="/assets/images/tradovate.svg"
                    alt="Tradovate Sponsor Logo"
                    width={185}
                    height={56}
                />
            </div>
            <div className="contents md:flex md:justify-self-start">
                <Image
                    src="/assets/images/quantower.svg"
                    alt="Quantower Sponsor Logo"
                    width={235}
                    height={52}
                />
            </div>
        </div>
    </section>
);

export default SponsorLogosSection;
