import SubscriptionCard from "@/components/landing-page/SubscriptionCard";

const PLANS = [
    {
        id: 1,
        level: 'BASIC',
        total_peer_year: '$50k',
        total_peer_month: '$89.99/MO',
        max_loss_limit: '$2,000',
        max_position_size: '5 Contracts',
        profit_target: '$3,000',
        color: 'text-[#00e6c3]',
        buttonColor: '!bg-[#00e6c3] hover:bg-[#00c4a6] !text-black'
    },
    {
        id: 2,
        level: 'PREMIUM',
        total_peer_year: '$100k',
        total_peer_month: '$149.99/MO',
        max_loss_limit: '$3,000',
        max_position_size: '10 Contracts',
        profit_target: '$6,000',
        color: 'text-[#ffa500]',
        buttonColor: 'bg-[#ffa500] hover:bg-[#e69400] !text-black'
    },
    {
        id: 3,
        level: 'UNLIMITED',
        total_peer_year: '$150k',
        total_peer_month: '$199.99/MO',
        max_loss_limit: '$4,500',
        max_position_size: '15 Contracts',
        profit_target: '$9,000',
        color: 'text-[#ffc04d]',
        buttonColor: 'bg-[#ffc04d] hover:bg-[#ffb31a] !text-black'
    }
]

const Subscriptions = () => {
    return <section className="mt-8 mb-10">
        <h2 className="text-5xl text-white text-center mb-10 font-light leading-[60px]">
            CHOOSE YOUR ACCOUNT SIZE
        </h2>

        <div className="grid grid-cols-3 gap-8">
            {PLANS.map(plan => (
                <SubscriptionCard key={plan.id} plan={plan}/>
            ))}
        </div>
    </section>
}

export default Subscriptions;