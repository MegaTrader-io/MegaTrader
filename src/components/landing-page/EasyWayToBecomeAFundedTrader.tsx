import {NextPage, GetStaticProps} from "next";
import PlanVerticalList from "@/components/landing-page/PlanVerticalList";

const EasyWayToBecomeAFundedTrader: NextPage = () => (
    <section className="px-4">
        <div className="pb-4 self-stretch text-center text-white text-[40px] font-light uppercase leading-[48px]">
            Easy way to become a funded trader
        </div>

        <div
            className="mx-auto pb-8 max-w-[760px] text-center text-xl leading-loose font-medium text-stone-400 md:w-[760px]">
            Guiding traders through a simple, step-by-step process to secure funding, prove their skills, and start
            earning with confidence and clarity.
        </div>

        <div className="grid grid-cols-[585px_1fr] gap-16">
            <div className="flex justify-center">
                <PlanVerticalList/>
            </div>
            <div className="flex items-center">
                <div className="max-w-[585px]">
                    <div
                        className="justify-start text-white text-[32px] font-medium uppercase leading-10">
                        01
                    </div>
                    <div
                        className="justify-start text-[#ffb34a] text-xl font-medium leading-loose">Select
                        Your Plan & Account Size
                    </div>
                    <div
                        className="justify-start text-stone-400 text-base font-medium leading-normal">Choose
                        the right plan that fits your trading style and goals. Pick your preferred account size and take
                        on
                        the challenge to become a funded trader.
                    </div>
                </div>
            </div>
        </div>
    </section>
);

export const getStaticProps: GetStaticProps = async () => {
    return {
        props: {},
    };
};

export default EasyWayToBecomeAFundedTrader;
