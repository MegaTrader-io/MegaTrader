import React, {useState} from 'react';
import {Button} from "@/components/Button";
import Card from "@/components/Card";

const defaultOption = 'evaluation'
type OPTIONS = 'evaluation' | 'get_funded' | 'trader_tools';

const items = [
    {
        option: 'evaluation',
        title: 'Pass the Challenge',
        description: 'Begin your journey by proving your trading discipline. Meet a set profit target while respecting daily and overall loss limits. This phase is designed to assess your risk management and consistency before granting access to funded capital.',
        features: [
            {
                icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_5167_2825" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                          width="24"
                          height="24">
                        <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_5167_2825)">
                        <path
                            d="M12 22C10.6167 22 9.31667 21.7375 8.1 21.2125C6.88333 20.6875 5.825 19.975 4.925 19.075C4.025 18.175 3.3125 17.1167 2.7875 15.9C2.2625 14.6833 2 13.3833 2 12C2 10.6167 2.2625 9.31667 2.7875 8.1C3.3125 6.88333 4.025 5.825 4.925 4.925C5.825 4.025 6.88333 3.3125 8.1 2.7875C9.31667 2.2625 10.6167 2 12 2C13.3833 2 14.6833 2.2625 15.9 2.7875C17.1167 3.3125 18.175 4.025 19.075 4.925C19.975 5.825 20.6875 6.88333 21.2125 8.1C21.7375 9.31667 22 10.6167 22 12C22 13.3833 21.7375 14.6833 21.2125 15.9C20.6875 17.1167 19.975 18.175 19.075 19.075C18.175 19.975 17.1167 20.6875 15.9 21.2125C14.6833 21.7375 13.3833 22 12 22ZM12 20C14.2333 20 16.125 19.225 17.675 17.675C19.225 16.125 20 14.2333 20 12C20 9.76667 19.225 7.875 17.675 6.325C16.125 4.775 14.2333 4 12 4C9.76667 4 7.875 4.775 6.325 6.325C4.775 7.875 4 9.76667 4 12C4 14.2333 4.775 16.125 6.325 17.675C7.875 19.225 9.76667 20 12 20ZM12 18C10.3333 18 8.91667 17.4167 7.75 16.25C6.58333 15.0833 6 13.6667 6 12C6 10.3333 6.58333 8.91667 7.75 7.75C8.91667 6.58333 10.3333 6 12 6C13.6667 6 15.0833 6.58333 16.25 7.75C17.4167 8.91667 18 10.3333 18 12C18 13.6667 17.4167 15.0833 16.25 16.25C15.0833 17.4167 13.6667 18 12 18ZM12 16C13.1 16 14.0417 15.6083 14.825 14.825C15.6083 14.0417 16 13.1 16 12C16 10.9 15.6083 9.95833 14.825 9.175C14.0417 8.39167 13.1 8 12 8C10.9 8 9.95833 8.39167 9.175 9.175C8.39167 9.95833 8 10.9 8 12C8 13.1 8.39167 14.0417 9.175 14.825C9.95833 15.6083 10.9 16 12 16ZM12 14C11.45 14 10.9792 13.8042 10.5875 13.4125C10.1958 13.0208 10 12.55 10 12C10 11.45 10.1958 10.9792 10.5875 10.5875C10.9792 10.1958 11.45 10 12 10C12.55 10 13.0208 10.1958 13.4125 10.5875C13.8042 10.9792 14 11.45 14 12C14 12.55 13.8042 13.0208 13.4125 13.4125C13.0208 13.8042 12.55 14 12 14Z"
                            fill="black"/>
                    </g>
                </svg>,
                title: 'Profit Target',
                description: 'Reach the required profit target within the challenge period while following all trading rules and maintaining consistency.'
            },
            {
                icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_5167_2832" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                          width="24"
                          height="24">
                        <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_5167_2832)">
                        <path
                            d="M12 22.0251C11.7333 22.0251 11.4791 21.9751 11.2375 21.8751C10.9958 21.7751 10.775 21.6334 10.575 21.4501L2.54998 13.4251C2.36664 13.2251 2.22498 13.0043 2.12498 12.7626C2.02498 12.5209 1.97498 12.2668 1.97498 12.0001C1.97498 11.7334 2.02498 11.4751 2.12498 11.2251C2.22498 10.9751 2.36664 10.7584 2.54998 10.5751L10.575 2.5501C10.775 2.3501 10.9958 2.20426 11.2375 2.1126C11.4791 2.02093 11.7333 1.9751 12 1.9751C12.2666 1.9751 12.525 2.02093 12.775 2.1126C13.025 2.20426 13.2416 2.3501 13.425 2.5501L21.45 10.5751C21.65 10.7584 21.7958 10.9751 21.8875 11.2251C21.9791 11.4751 22.025 11.7334 22.025 12.0001C22.025 12.2668 21.9791 12.5209 21.8875 12.7626C21.7958 13.0043 21.65 13.2251 21.45 13.4251L13.425 21.4501C13.2416 21.6334 13.025 21.7751 12.775 21.8751C12.525 21.9751 12.2666 22.0251 12 22.0251ZM11 13.0001H13V7.0001H11V13.0001ZM12 16.0001C12.2833 16.0001 12.5208 15.9043 12.7125 15.7126C12.9041 15.5209 13 15.2834 13 15.0001C13 14.7168 12.9041 14.4793 12.7125 14.2876C12.5208 14.0959 12.2833 14.0001 12 14.0001C11.7166 14.0001 11.4791 14.0959 11.2875 14.2876C11.0958 14.4793 11 14.7168 11 15.0001C11 15.2834 11.0958 15.5209 11.2875 15.7126C11.4791 15.9043 11.7166 16.0001 12 16.0001Z"
                            fill="black"/>
                    </g>
                </svg>,
                title: 'Risk Limits',
                description: 'Stay within daily and overall drawdown thresholds to remain eligible and avoid violating your evaluation account.'
            }
        ]
    },
    {
        option: 'get_funded',
        title: 'Trade With Firm Capital',
        description: 'After successfully completing the evaluation, you’ll be granted a funded trading account. You’ll trade with zero personal risk while keeping up to 90% of profits, giving you the freedom to grow without the pressure of risking your own money.',
        features: [
            {
                icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_8220_15132" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                          width="24" height="24">
                        <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_8220_15132)">
                        <path
                            d="M4 20C3.45 20 2.97917 19.8042 2.5875 19.4125C2.19583 19.0208 2 18.55 2 18V6C2 5.45 2.19583 4.97917 2.5875 4.5875C2.97917 4.19583 3.45 4 4 4H9V20H4ZM11 22V2H13V4H20C20.55 4 21.0208 4.19583 21.4125 4.5875C21.8042 4.97917 22 5.45 22 6V18C22 18.55 21.8042 19.0208 21.4125 19.4125C21.0208 19.8042 20.55 20 20 20H13V22H11Z"
                            fill="black"/>
                    </g>
                </svg>,
                title: 'Profit Split',
                description: 'Receive up to 90% of your trading profits with no capital risk, allowing you to scale your earnings without limitations.'
            },
            {
                icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_8220_15139" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                          width="24" height="24">
                        <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_8220_15139)">
                        <path
                            d="M11.1 19H12.85V17.75C13.6833 17.6 14.4 17.275 15 16.775C15.6 16.275 15.9 15.5333 15.9 14.55C15.9 13.85 15.7 13.2083 15.3 12.625C14.9 12.0417 14.1 11.5333 12.9 11.1C11.9 10.7667 11.2083 10.475 10.825 10.225C10.4417 9.975 10.25 9.63333 10.25 9.2C10.25 8.76667 10.4042 8.425 10.7125 8.175C11.0208 7.925 11.4667 7.8 12.05 7.8C12.5833 7.8 13 7.92917 13.3 8.1875C13.6 8.44583 13.8167 8.76667 13.95 9.15L15.55 8.5C15.3667 7.91667 15.0292 7.40833 14.5375 6.975C14.0458 6.54167 13.5 6.3 12.9 6.25V5H11.15V6.25C10.3167 6.43333 9.66667 6.8 9.2 7.35C8.73333 7.9 8.5 8.51667 8.5 9.2C8.5 9.98333 8.72917 10.6167 9.1875 11.1C9.64583 11.5833 10.3667 12 11.35 12.35C12.4 12.7333 13.1292 13.075 13.5375 13.375C13.9458 13.675 14.15 14.0667 14.15 14.55C14.15 15.1 13.9542 15.5042 13.5625 15.7625C13.1708 16.0208 12.7 16.15 12.15 16.15C11.6 16.15 11.1125 15.9792 10.6875 15.6375C10.2625 15.2958 9.95 14.7833 9.75 14.1L8.1 14.75C8.33333 15.55 8.69583 16.1958 9.1875 16.6875C9.67917 17.1792 10.3167 17.5167 11.1 17.7V19ZM12 22C10.6167 22 9.31667 21.7375 8.1 21.2125C6.88333 20.6875 5.825 19.975 4.925 19.075C4.025 18.175 3.3125 17.1167 2.7875 15.9C2.2625 14.6833 2 13.3833 2 12C2 10.6167 2.2625 9.31667 2.7875 8.1C3.3125 6.88333 4.025 5.825 4.925 4.925C5.825 4.025 6.88333 3.3125 8.1 2.7875C9.31667 2.2625 10.6167 2 12 2C13.3833 2 14.6833 2.2625 15.9 2.7875C17.1167 3.3125 18.175 4.025 19.075 4.925C19.975 5.825 20.6875 6.88333 21.2125 8.1C21.7375 9.31667 22 10.6167 22 12C22 13.3833 21.7375 14.6833 21.2125 15.9C20.6875 17.1167 19.975 18.175 19.075 19.075C18.175 19.975 17.1167 20.6875 15.9 21.2125C14.6833 21.7375 13.3833 22 12 22Z"
                            fill="black"/>
                    </g>
                </svg>,
                title: 'Real-Time Payouts',
                description: 'Submit a payout request anytime after qualifying, with fast and consistent processing each week via your preferred method.'
            }
        ]
    },
    {
        option: 'trader_tools',
        title: 'Use Smart Trading Tools',
        description: 'Access a suite of tools built for performance. Monitor your metrics, set risk limits, get instant feedback on your trades, and customize your workspace to fit your trading style. Everything is designed to help you trade smarter, not harder.',
        features: [
            {
                icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_8220_14947" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                          width="24" height="24">
                        <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_8220_14947)">
                        <path
                            d="M5 21C4.45 21 3.97917 20.8042 3.5875 20.4125C3.19583 20.0208 3 19.55 3 19V5C3 4.45 3.19583 3.97917 3.5875 3.5875C3.97917 3.19583 4.45 3 5 3H11V21H5ZM13 21V12H21V19C21 19.55 20.8042 20.0208 20.4125 20.4125C20.0208 20.8042 19.55 21 19 21H13ZM13 10V3H19C19.55 3 20.0208 3.19583 20.4125 3.5875C20.8042 3.97917 21 4.45 21 5V10H13Z"
                            fill="black"/>
                    </g>
                </svg>,
                title: 'Performance Dashboard',
                description: 'Visualize your trades, profits, win rate, and more—all in one place to track progress and stay focused on your goals.'
            },
            {
                icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <mask id="mask0_8220_14954" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0"
                          width="24" height="24">
                        <rect width="24" height="24" fill="#D9D9D9"/>
                    </mask>
                    <g mask="url(#mask0_8220_14954)">
                        <path
                            d="M2 19V17H12V19H2ZM2 14V12H7V14H2ZM2 9V7H7V9H2ZM20.6 19L16.75 15.15C16.35 15.4333 15.9125 15.6458 15.4375 15.7875C14.9625 15.9292 14.4833 16 14 16C12.6167 16 11.4375 15.5125 10.4625 14.5375C9.4875 13.5625 9 12.3833 9 11C9 9.61667 9.4875 8.4375 10.4625 7.4625C11.4375 6.4875 12.6167 6 14 6C15.3833 6 16.5625 6.4875 17.5375 7.4625C18.5125 8.4375 19 9.61667 19 11C19 11.4833 18.9292 11.9625 18.7875 12.4375C18.6458 12.9125 18.4333 13.35 18.15 13.75L22 17.6L20.6 19ZM14 14C14.8333 14 15.5417 13.7083 16.125 13.125C16.7083 12.5417 17 11.8333 17 11C17 10.1667 16.7083 9.45833 16.125 8.875C15.5417 8.29167 14.8333 8 14 8C13.1667 8 12.4583 8.29167 11.875 8.875C11.2917 9.45833 11 10.1667 11 11C11 11.8333 11.2917 12.5417 11.875 13.125C12.4583 13.7083 13.1667 14 14 14Z"
                            fill="black"/>
                    </g>
                </svg>,
                title: 'Risk Management Alerts',
                description: 'Stay protected with automated alerts that warn you when you’re approaching your drawdown or overtrading limits.'
            }
        ]
    },
]

function YourPathToProfitable() {
    const [option, setOption] = useState<OPTIONS>(defaultOption);

    const currentItem = items.find(item => item.option === option)!;

    return (
        <section className="px-4">
            <div className="pb-4 self-stretch text-center text-white text-[40px] font-light uppercase leading-[48px]">
                Your Path to Profitable Trading Starts Here
            </div>

            <div
                className="mx-auto max-w-[612px] text-center text-xl leading-loose font-medium text-stone-400 md:max-w-[780px]">
                From your first trade to your first payout, every step is built to guide you toward consistent success
                with powerful tools, clear rules, and real rewards.
            </div>

            <div className="mx-auto flex py-12">
                <div
                    className="w-fit mx-auto p-1 bg-[#1e1e1e] rounded-xl outline outline-1 outline-offset-[-1px] outline-neutral-700 inline-flex justify-start items-center gap-1">
                    <Button onClick={() => setOption('evaluation')} variant={'primary'}
                            styleType={option === 'evaluation' ? 'filled' : 'text'}>
                        EVALUATION
                    </Button>
                    <Button onClick={() => setOption('get_funded')} variant={'primary'}
                            styleType={option === 'get_funded' ? 'filled' : 'text'}>
                        GET FUNDED
                    </Button>
                    <Button onClick={() => setOption('trader_tools')} variant={'primary'}
                            styleType={option === 'trader_tools' ? 'filled' : 'text'}>
                        TRADER TOOLS
                    </Button>
                </div>
            </div>

            <div className="flex gap-12">
                <div className="w-full space-y-12">
                    <div
                        className="self-stretch justify-start text-white text-[40px] font-light uppercase leading-[48px]">
                        {currentItem.title}
                    </div>
                    <div
                        className="self-stretch justify-start text-stone-400 text-xl font-medium leading-loose">
                        {currentItem.description}
                    </div>

                    <div className="space-y-4">
                        {currentItem.features.map((feature, index) => (
                            <Card key={index} className="space-y-2 border-transparent border-0">
                                <div className="grid grid-cols-[40px_1fr] gap-4">
                                    <div
                                        className="w-10 h-10 bg-teal-400 rounded-full flex justify-center items-center">
                                        {feature.icon}
                                    </div>
                                    <div
                                        className="self-stretch justify-start text-teal-400 text-xl font-bold leading-loose">
                                        {feature.title}
                                    </div>
                                </div>
                                <div className="grid grid-cols-[40px_1fr] gap-4">
                                    <div
                                        className="col-start-2 justify-start text-stone-400 text-base font-medium leading-normal">
                                        {feature.description}
                                    </div>
                                </div>
                            </Card>
                        ))}
                    </div>
                </div>
                <Card className="w-full max-w-[558px]">
                    ..
                </Card>
            </div>
        </section>
    );
}

export default YourPathToProfitable;