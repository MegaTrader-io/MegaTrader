import Link from "next/link";

const HeroSection = ({className = ''}: { className?: string }) => (
    <section className={className}>
        <div className="py-12 space-y-12">
            <div className="space-y-4">
                <div
                    className="self-stretch text-center justify-start text-white text-6xl font-light uppercase leading-[72px]">
                    Start Your Futures Journey
                </div>
                <div
                    className="w-full max-w-[612px] mx-auto text-center justify-start text-stone-400 text-xl font-medium leading-loose">Empowering
                    traders with innovative solutions, unmatched reliability, and tools designed to elevate your trading
                    journey
                    to new heights.
                </div>
            </div>
            <div className="space-y-4 md:space-y-0 md:flex md:justify-center md:gap-4">
                <Link href={'#'} className='btn-yellow-link rounded-xl h-12 px-4 py-3'>
                    Start trading
                </Link>
                <Link href={'#'} className='btn-dark-link rounded-xl h-12 px-4 py-3'>
                    Join Discord
                </Link>
            </div>
        </div>

    </section>
);

export default HeroSection;