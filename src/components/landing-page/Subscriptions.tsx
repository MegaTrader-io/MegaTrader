import SubscriptionCard from "@/components/landing-page/SubscriptionCard";
import clsx from "clsx";

const PLANS = [
    {
        id: 1,
        level: 'BASIC PLAN',
        total_peer_year: '$50k',
        total_peer_month: '$89.99/MO',
        max_loss_limit: '$2,000',
        max_position_size: '5 Contracts',
        profit_target: '$3,000',
        color: 'text-teal-400',
        colorItem: 'text-stone-400',
    },
    {
        id: 2,
        level: 'PRO PLAN',
        total_peer_year: '$100k',
        total_peer_month: '$149.99/MO',
        max_loss_limit: '$3,000',
        max_position_size: '10 Contracts',
        profit_target: '$6,000',
        color: 'text-white',
        colorItem: 'text-stone-400',
    },
    {
        id: 3,
        level: 'PREMIUM PLAN',
        total_peer_year: '$150k',
        total_peer_month: '$199.99/MO',
        max_loss_limit: '$4,500',
        max_position_size: '15 Contracts',
        profit_target: '$9,000',
        color: 'text-mgt-primary',
        colorItem: 'text-stone-400',
    }
]

const Subscriptions = ({className = ''}: { className?: string }) => {
    return <section id="pricing" className={clsx('mb-8', className)}>
        <h2 className="text-[32px] max-w-[328px] mx-auto lg:max-w-full leading-10 lg:text-5xl text-white text-center mb-8 font-light lg:leading-[60px]">
            CHOOSE YOUR ACCOUNT SIZE
        </h2>

        <div className="space-y-4 lg:space-y-0 lg:grid lg:grid-cols-3 lg:gap-4">
            {PLANS.map(plan => (
                <SubscriptionCard key={plan.id} plan={plan}/>
            ))}
        </div>
    </section>
}

export default Subscriptions;