import Image from "next/image";
import clsx from "clsx";

const SponsorLogosSection = ({className = ''}: { className?: string }) => (
    <section
        className={clsx('flex flex-col gap-12 my-8 lg:my-auto lg:flex-row lg:flex lg:justify-between lg:py-12 opacity-30 items-center lg:gap-16', className)}>
        <Image
            src="/assets/images/mega-x.svg"
            alt="MegaX Sponsor Logo"
            width={151}
            height={56}
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
    </section>
);

export default SponsorLogosSection;
