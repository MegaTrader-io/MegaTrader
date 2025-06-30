import React, {useState} from 'react';
import clsx from "clsx";

export type ACCOUNT_SIZE_PLAN = 'elite_plan' | 'growth_plan' | 'funded_plan';


function ButtonsAccountSize({className, changePlan, defaultPlan}: {
    className: string,
    defaultPlan: ACCOUNT_SIZE_PLAN,
    changePlan: (plan: ACCOUNT_SIZE_PLAN) => void
}) {

    const [selectPlan, setSelectPlan] = useState<ACCOUNT_SIZE_PLAN>(defaultPlan);

    const changeOption = (plan: ACCOUNT_SIZE_PLAN) => {
        setSelectPlan(plan)
        changePlan(plan)
    }

    return (
        <div className={clsx('space-y-2 md:space-y-0 md:flex gap-2', className)}>
            <button
                onClick={() => changeOption('elite_plan')}
                className={clsx(
                    'group relative w-full rounded-2xl p-6 text-left',
                    [
                        selectPlan === 'elite_plan'
                            ? 'plan-selected bg-[#ffb34a]'
                            : 'bg-transparent  outline outline-2 outline-offset-[-2px] outline-neutral-700'
                    ],
                )}>
                <div className="inline-flex justify-start items-start gap-4">
                    <div className="text-primary group-[.plan-selected]:text-black mt-1">
                        <svg width="36" height="36" viewBox="0 0 36 36"
                             fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_11380_2769" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="36" height="36">
                                <rect width="36" height="36" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_11380_2769)">
                                <path
                                    d="M13.8 12.375L17.775 4.5H18.225L22.2 12.375H13.8ZM16.875 30.15L3.9375 14.625H16.875V30.15ZM19.125 30.15V14.625H32.0625L19.125 30.15ZM24.675 12.375L20.775 4.5H28.5L32.4375 12.375H24.675ZM3.5625 12.375L7.5 4.5H15.225L11.325 12.375H3.5625Z"
                                    fill="currentColor"/>
                            </g>
                        </svg>
                    </div>
                    <div className="flex-1 inline-flex flex-col justify-center items-start gap-2">
                        <div className="self-stretch inline-flex justify-start items-start gap-1">
                            <div
                                className="justify-start text-white group-[.plan-selected]:text-black text-xl font-bold leading-loose">
                                Elite Plan
                            </div>
                        </div>
                        <div
                            className="self-stretch justify-start text-stone-400 text-base font-bold leading-normal group-[.plan-selected]:opacity-60 group-[.plan-selected]:text-black">For
                            experienced traders seeking premium tools and insights.
                        </div>
                    </div>
                    {selectPlan === 'elite_plan' && <CheckSelection/>}
                </div>
            </button>
            <button
                onClick={() => changeOption('growth_plan')}
                className={clsx(
                    'group relative w-full rounded-2xl p-6 text-left',
                    [
                        selectPlan === 'growth_plan'
                            ? 'plan-selected bg-[#ffb34a]'
                            : 'bg-transparent  outline outline-2 outline-offset-[-2px] outline-neutral-700'
                    ],
                )}>
                <div className="inline-flex justify-start items-start gap-4">
                    <div className="text-primary group-[.plan-selected]:text-black mt-1">
                        <svg width="37" height="36" viewBox="0 0 37 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_11380_2722" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="37" height="36">
                                <rect x="0.666656" width="36" height="36" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_11380_2722)">
                                <path
                                    d="M4.34166 15.8624L10.6417 9.56243C10.9917 9.21243 11.4042 8.96243 11.8792 8.81243C12.3542 8.66243 12.8417 8.63743 13.3417 8.73743L15.2917 9.14993C13.9417 10.7499 12.8792 12.1999 12.1042 13.4999C11.3292 14.7999 10.5792 16.3749 9.85416 18.2249L4.34166 15.8624ZM12.0292 19.2749C12.6042 17.4749 13.3854 15.7749 14.3729 14.1749C15.3604 12.5749 16.5542 11.0749 17.9542 9.67493C20.1542 7.47493 22.6667 5.83118 25.4917 4.74368C28.3167 3.65618 30.9542 3.32493 33.4042 3.74993C33.8292 6.19993 33.5042 8.83743 32.4292 11.6624C31.3542 14.4874 29.7167 16.9999 27.5167 19.1999C26.1417 20.5749 24.6417 21.7687 23.0167 22.7812C21.3917 23.7937 19.6792 24.5874 17.8792 25.1624L12.0292 19.2749ZM22.3792 14.7749C22.9542 15.3499 23.6604 15.6374 24.4979 15.6374C25.3354 15.6374 26.0417 15.3499 26.6167 14.7749C27.1917 14.1999 27.4792 13.4937 27.4792 12.6562C27.4792 11.8187 27.1917 11.1124 26.6167 10.5374C26.0417 9.96243 25.3354 9.67493 24.4979 9.67493C23.6604 9.67493 22.9542 9.96243 22.3792 10.5374C21.8042 11.1124 21.5167 11.8187 21.5167 12.6562C21.5167 13.4937 21.8042 14.1999 22.3792 14.7749ZM21.3292 32.8124L18.9292 27.2999C20.7792 26.5749 22.3604 25.8249 23.6729 25.0499C24.9854 24.2749 26.4417 23.2124 28.0417 21.8624L28.4167 23.8124C28.5167 24.3124 28.4917 24.8062 28.3417 25.2937C28.1917 25.7812 27.9417 26.1999 27.5917 26.5499L21.3292 32.8124ZM6.74166 24.0749C7.61666 23.1999 8.67916 22.7562 9.92916 22.7437C11.1792 22.7312 12.2417 23.1624 13.1167 24.0374C13.9917 24.9124 14.4292 25.9749 14.4292 27.2249C14.4292 28.4749 13.9917 29.5374 13.1167 30.4124C12.4917 31.0374 11.4479 31.5749 9.98541 32.0249C8.52291 32.4749 6.50416 32.8749 3.92916 33.2249C4.27916 30.6499 4.67916 28.6374 5.12916 27.1874C5.57916 25.7374 6.11666 24.6999 6.74166 24.0749Z"
                                    fill="currentColor"/>
                            </g>
                        </svg>
                    </div>
                    <div className="flex-1 inline-flex flex-col justify-center items-start gap-2">
                        <div className="self-stretch inline-flex justify-start items-start gap-1">
                            <div
                                className="justify-start text-white group-[.plan-selected]:text-black text-xl font-bold leading-loose">
                                Growth Plan
                            </div>
                        </div>
                        <div
                            className="self-stretch justify-start text-stone-400 text-base font-bold leading-normal group-[.plan-selected]:opacity-60 group-[.plan-selected]:text-black">
                            For growing traders focused on skill and portfolio development.
                        </div>
                    </div>
                    {selectPlan === 'growth_plan' && <CheckSelection/>}
                </div>
            </button>
            <button
                onClick={() => changeOption('funded_plan')}
                className={clsx(
                    'group relative w-full rounded-2xl p-6 text-left',
                    [
                        selectPlan === 'funded_plan'
                            ? 'plan-selected bg-[#ffb34a]'
                            : 'bg-transparent  outline outline-2 outline-offset-[-2px] outline-neutral-700'
                    ],
                )}>
                <div className="inline-flex justify-start items-start gap-4">
                    <div className="text-primary group-[.plan-selected]:text-black mt-1">
                        <svg width="37" height="36" viewBox="0 0 37 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <mask id="mask0_11380_1095" maskUnits="userSpaceOnUse" x="0"
                                  y="0"
                                  width="37" height="36">
                                <rect x="0.333374" width="36" height="36" fill="#D9D9D9"/>
                            </mask>
                            <g mask="url(#mask0_11380_1095)">
                                <path
                                    d="M7.83337 25.5V15H10.8334V25.5H7.83337ZM16.8334 25.5V15H19.8334V25.5H16.8334ZM3.33337 31.5V28.5H33.3334V31.5H3.33337ZM25.8334 25.5V15H28.8334V25.5H25.8334ZM3.33337 12V9L18.3334 1.5L33.3334 9V12H3.33337Z"
                                    fill="currentColor"/>
                            </g>
                        </svg>
                    </div>
                    <div className="flex-1 inline-flex flex-col justify-center items-start gap-2">
                        <div className="self-stretch inline-flex justify-start items-start gap-1">
                            <div
                                className="justify-start text-white group-[.plan-selected]:text-black text-xl font-bold leading-loose">
                                Funded Plan
                            </div>
                        </div>
                        <div
                            className="self-stretch justify-start text-stone-400 text-base font-bold leading-normal group-[.plan-selected]:opacity-60 group-[.plan-selected]:text-black">
                            Jump straight into a simulated funded account.
                        </div>
                    </div>
                    {selectPlan === 'funded_plan' && <CheckSelection/>}
                </div>
            </button>
        </div>
    );
}

function CheckSelection() {
    return <div className="w-[30px] h-[30px] right-[8px] top-[8px] absolute">
        <svg width="31" height="30" viewBox="0 0 31 30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_11266_797" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0"
                  y="0"
                  width="31" height="30">
                <rect x="0.5" width="30" height="30" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_11266_797)">
                <path
                    d="M13.75 20.75L22.5625 11.9375L20.8125 10.1875L13.75 17.25L10.1875 13.6875L8.4375 15.4375L13.75 20.75ZM15.5 27.5C13.7708 27.5 12.1458 27.1719 10.625 26.5156C9.10417 25.8594 7.78125 24.9688 6.65625 23.8438C5.53125 22.7188 4.64063 21.3958 3.98438 19.875C3.32812 18.3542 3 16.7292 3 15C3 13.2708 3.32812 11.6458 3.98438 10.125C4.64063 8.60417 5.53125 7.28125 6.65625 6.15625C7.78125 5.03125 9.10417 4.14063 10.625 3.48438C12.1458 2.82812 13.7708 2.5 15.5 2.5C17.2292 2.5 18.8542 2.82812 20.375 3.48438C21.8958 4.14063 23.2188 5.03125 24.3438 6.15625C25.4688 7.28125 26.3594 8.60417 27.0156 10.125C27.6719 11.6458 28 13.2708 28 15C28 16.7292 27.6719 18.3542 27.0156 19.875C26.3594 21.3958 25.4688 22.7188 24.3438 23.8438C23.2188 24.9688 21.8958 25.8594 20.375 26.5156C18.8542 27.1719 17.2292 27.5 15.5 27.5Z"
                    fill="#131210"/>
            </g>
        </svg>
    </div>
}

export default ButtonsAccountSize;