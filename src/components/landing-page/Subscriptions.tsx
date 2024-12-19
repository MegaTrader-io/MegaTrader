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
        color: 'text-teal-400',
        colorItem: 'text-stone-400',
        buttonColor: 'bg-teal-500 !text-black'
    },
    {
        id: 2,
        level: 'PREMIUM',
        total_peer_year: '$100k',
        total_peer_month: '$149.99/MO',
        max_loss_limit: '$3,000',
        max_position_size: '10 Contracts',
        profit_target: '$6,000',
        color: 'text-white',
        colorItem: 'text-stone-400',
        buttonColor: 'bg-white !text-black'
    },
    {
        id: 3,
        level: 'UNLIMITED',
        total_peer_year: '$150k',
        total_peer_month: '$199.99/MO',
        max_loss_limit: '$4,500',
        max_position_size: '15 Contracts',
        profit_target: '$9,000',
        color: 'text-[#ffb34a]',
        colorItem: 'text-stone-400',
        buttonColor: 'bg-[#ffb34a] !text-black'
    }
]

const Subscriptions = () => {
    return <section className="my-8">
        <h2 className="text-5xl text-white text-center mb-8 font-light leading-[60px]">
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