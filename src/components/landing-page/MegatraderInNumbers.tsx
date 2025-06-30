import clsx from "clsx";
import Card from "@/components/Card";
import {NextPage, GetStaticProps} from "next";

const Items = [
    {
        title: '+5,000',
        subtitle: 'Active Users',
        detail: 'A thriving trader community<br/> growing every week.'
    },
    {
        title: '72%',
        subtitle: 'Conversion Rate',
        detail: 'Most challenge users<br/> eventually get funded.'
    },
    {
        title: '+800',
        subtitle: 'Payouts Per Week',
        detail: 'Real traders paid regularly<br/> proven success.'
    },
    {
        title: '90%',
        subtitle: 'Profit Split',
        detail: 'Keep most profits with<br/> top-tier splits.'
    },
    {
        title: '4.8',
        subtitle: 'User Rating',
        detail: 'Highly rated by real users<br/> on trusted reviews.'
    },
];

const MegatraderInNumbers: NextPage = () => (
    <section className="space-y-4 px-4 pb-12">
        <div className="self-stretch text-center text-white text-[40px] font-light uppercase leading-[48px]">
            Megatrader in numbers
        </div>

        <div
            className="mx-auto max-w-[760px] text-center text-xl leading-loose font-medium text-stone-400 md:w-[760px]">
            See how our commitment to excellence delivers real payouts, consistent performance, and trader success.
        </div>

        <Card
            className="md:grid md:grid-cols-12 lg:grid lg:grid-cols-5 !mt-12 lg:items-start lg:justify-between gap-3 px-4 py-8">
            {Items.map((item, index) => (
                <div key={index} className={clsx(
                    'flex-col justify-start items-center gap-2 lg:col-auto',
                    index <= 2 ? 'md:col-span-4' : 'md:col-span-6',
                    index === 3 ? 'md:col-start-4 md:col-end-7' : '',
                    index === 4 ? 'md:col-start-8 md:col-end-11' : ''
                )}>
                    <h3 className="self-stretch text-center justify-start text-[#ffb34a] text-6xl font-light uppercase leading-[72px]">
                        {item.title}
                    </h3>
                    <div
                        className="self-stretch text-center justify-start text-white text-xl font-medium leading-loose">
                        {item.subtitle}
                    </div>
                    <div
                        className="self-stretch text-center justify-start text-stone-400 text-base font-medium leading-normal">
                        {item.detail.split('<br/>').map((line, i) => (
                            <p key={i}>{line}</p>
                        ))}
                    </div>
                </div>
            ))}
        </Card>
    </section>
);

export const getStaticProps: GetStaticProps = async () => {
    return {
        props: {},
    };
};

export default MegatraderInNumbers;
