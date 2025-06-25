import Image from "next/image";
import clsx from "clsx";

const SponsorLogosSection = ({className = ''}: { className?: string }) => (
    <section
        className={clsx('space-y-4', className)}>
        <div
            className="self-stretch text-center justify-start text-white text-[40px] font-light uppercase leading-[48px]">Trusted
            Platforms
        </div>
        <div
            className="w-[760px] mx-auto max-w-[760px] text-center justify-start text-stone-400 text-xl font-medium leading-loose">Trade
            with confidence on industry-leading platforms trusted by professionals for their speed, reliability, and
            advanced trading capabilities.
        </div>
        <div
            className="flex flex-col gap-12 my-8 lg:my-auto lg:flex-row lg:flex lg:justify-between lg:py-12 opacity-30 items-center lg:gap-16">
            <Image
                src="/assets/images/projectx.svg"
                alt="MegaX Sponsor Logo"
                width={224}
                height={54}
                className="relative"
            />
            <Image
                src="/assets/images/ninjatrader.svg"
                alt="NinjaTrader Sponsor Logo"
                width={281}
                height={36}
                className="relative"
            />
            <Image
                src="/assets/images/tradovate.svg"
                alt="Tradovate Sponsor Logo"
                width={185}
                height={56}
                className="relative"
            />
            <Image
                src="/assets/images/quantower.svg"
                alt="Quantower Sponsor Logo"
                width={235}
                height={52}
                className="relative"
            />
        </div>
    </section>
);

export default SponsorLogosSection;
