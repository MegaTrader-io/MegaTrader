import Card from "@/components/card";
import Image from "next/image";

const BenefitsSection = () => {
    return <section className="my-10">
        <div className="grid grid-cols-3 gap-5">
            <Card
                className="flex-col justify-start items-start gap-2.5 inline-flex">
                <h2 className="text-white text-[32px]">
                    ONE-STEP EVALUATION
                </h2>
                <div className="text-stone-400 text-base font-normal leading-normal">
                    Simplify your path to a funded trading account with a streamlined one-step evaluation
                    process. Prove your consistency and trading skills quickly and efficiently, with clear
                    profit targets and defined trading rules.
                </div>
            </Card>
            <Card
                className="flex-col justify-start items-start gap-2.5 inline-flex">
                <h2 className="text-white text-[32px]">
                    INSTANT PAYOUTS
                </h2>
                <div className="text-stone-400 text-base font-normal leading-normal">
                    Enjoy the flexibility of accessing your earnings with instant payout options. Withdraw
                    your profits quickly and efficiently, ensuring you have complete control over your
                    trading income whenever you need it.
                </div>
            </Card>
            <Card
                className="p-4 bg-[#1e1e1e]/70 border border-neutral-700 flex-col justify-start items-start gap-2.5 inline-flex">
                <h2 className="text-white text-[32px]">
                    ONE-STEP EVALUATION
                </h2>
                <div className="text-stone-400 text-base font-normal leading-normal mb-4">
                    Simplify your path to a funded trading account with a streamlined one-step evaluation
                    process. Prove your consistency and trading skills quickly and efficiently, with clear
                    profit targets and defined trading rules.
                </div>

                <Image
                    src="/assets/images/people.svg"
                    alt="Trading Platform Interface"
                    width={411}
                    height={240}
                />
            </Card>
        </div>
    </section>
}

export default BenefitsSection;