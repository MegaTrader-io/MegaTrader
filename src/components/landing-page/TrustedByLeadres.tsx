import React from 'react';
import Card from "@/components/Card";
import Image from "next/image";

function TrustedByLeadres() {
    return (
        <section id="feature-trusted-by-leadres" className="space-y-12 pb-12 px-4">
            <div className="w-full space-y-4">
                <div
                    className="justify-start text-center text-white text-[40px] font-light uppercase leading-[48px]">
                    Trusted by leadres
                </div>
                <div
                    className="mx-auto max-w-[612px] text-center text-xl leading-8 font-medium text-stone-400 md:max-w-[860px]">
                    Hear from top traders and industry leaders who trust our platform to elevate their performance,
                    streamline th eir workflows, and unlock new opportunities in the trading world.
                </div>
            </div>

            <div className="space-y-12 md:flex md:gap-12 md:space-y-0">
                <Card className="space-y-8 p-8">
                    <div className="flex items-center gap-4">
                        <svg width="58" height="58" viewBox="0 0 58 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M0 29C0 12.9837 12.9837 0 29 0C45.0163 0 58 12.9837 58 29C58 45.0163 45.0163 58 29 58C12.9837 58 0 45.0163 0 29Z"
                                fill="#257FFF"/>
                            <path fillRule="evenodd" clipRule="evenodd"
                                  d="M34.5869 40.8682V46.5L21.9291 40.8682L34.5869 35.2364V40.8682ZM21.4131 34.6841V40.3159L34.0709 34.6841L21.4131 29.0523V34.6841ZM20.6604 22.3159V27.9477L8 22.3159L20.6578 16.6841V22.3159H20.6604ZM34.5895 28.5V34.1318L21.9318 28.5L34.5895 22.8682V28.5ZM21.4158 22.3159V27.9477L34.0735 22.3159L21.4158 16.6841V22.3159ZM34.5895 16.1318V21.7636L21.9318 16.1318L34.5869 10.5V16.1318H34.5895ZM35.3422 16.1318V21.7636L48 16.1318L35.3396 10.5V16.1318H35.3422Z"
                                  fill="white"/>
                        </svg>

                        <div
                            className="justify-start text-white text-xl font-bold leading-8">
                            Tradovate
                        </div>
                    </div>

                    <div
                        className="justify-start text-stone-400 text-base font-medium leading-6">
                        Tradovate made the transition into funded trading seamless. The user-friendly interface and
                        powerful
                        analytics helped me reach my goals faster than I imagined.
                    </div>

                    <div className="flex items-center gap-4">
                        <Image src={'/assets/images/avatar-angela.png'}
                               alt={'avatar Angela Kim, United States'} height={64} width={64}/>

                        <div>
                            <div
                                className="self-stretch justify-start text-white text-base font-medium leading-6">
                                Angela
                                Kim, United States
                            </div>
                            <div
                                className="self-stretch justify-start text-stone-400 text-base font-medium leading-6">United
                                State
                            </div>
                        </div>
                    </div>
                </Card>
                <Card className="space-y-8 p-8">
                    <div className="flex items-center gap-4">
                        <svg width="58" height="58" viewBox="0 0 58 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M0 29C0 12.9837 12.9837 0 29 0C45.0163 0 58 12.9837 58 29C58 45.0163 45.0163 58 29 58C12.9837 58 0 45.0163 0 29Z"
                                fill="#00566C"/>
                            <path
                                d="M29.766 9.1582C18.9225 9.1582 10.1755 18.0184 10.1755 29.0022C10.1755 39.986 18.9225 48.8461 29.766 48.8461C40.6095 48.8461 49.3565 39.986 49.3565 29.0022C49.3197 18.0184 40.5727 9.1582 29.766 9.1582ZM40.9743 42.0312V32.8724L34.8359 32.126V45.4909C34.2096 45.6775 33.5869 45.8268 32.9237 45.9761V29.8942L40.9706 31.1967V26.2441L29.7624 22.7806L18.5541 26.2441V31.234L26.6047 29.9315V46.0134C25.9415 45.9015 25.3188 45.7149 24.6925 45.5282V32.1633L18.5541 32.906V42.0648C14.9507 38.8999 12.6736 34.2086 12.6736 28.9984C12.6736 19.4329 20.319 11.6886 29.766 11.6886C39.2131 11.6886 46.8548 19.4329 46.8548 28.9984C46.8548 34.2086 44.5777 38.8626 40.9743 42.0275V42.0312ZM18.5173 22.5231V24.9789L29.7624 21.1086L40.9706 24.9789V22.5231L29.7624 17.7945L18.5173 22.5231ZM37.4077 19.6195V17.3503L29.7624 13.4054L22.0802 17.3503V19.6195L29.7624 16.156L37.4077 19.6195Z"
                                fill="white"/>
                        </svg>
                        <div
                            className="justify-start text-white text-xl font-bold leading-8">
                            Quantower
                        </div>
                    </div>

                    <div
                        className="justify-start text-stone-400 text-base font-medium leading-6">
                        Precision and flexibility are key in my trading style—and Quantower delivers both. From
                        multi-asset support to advanced charting, it’s my go-to platform every day
                    </div>

                    <div className="flex items-center gap-4">
                        <Image src={'/assets/images/avatar-daniel.png'}
                               alt={'avatar Daniel Reyes'} height={64} width={64}/>

                        <div>
                            <div
                                className="self-stretch justify-start text-white text-base font-medium leading-6">
                                Daniel Reyes
                            </div>
                            <div
                                className="self-stretch justify-start text-stone-400 text-base font-medium leading-6">United
                                State
                            </div>
                        </div>
                    </div>
                </Card>
            </div>

            <Card className="space-y-8 p-8">
                <div className="flex items-center gap-4">
                    <Image src={'/assets/images/ninjatraderIcon.png'}
                           alt={'avatar Daniel Reyes'} height={58} width={58}/>
                    <div
                        className="justify-start text-white text-xl font-bold leading-8">
                        Ninjatrader
                    </div>
                </div>

                <div
                    className="self-stretch justify-start text-white text-xl font-medium leading-8">The
                    tools, reliability, and execution speed on this platform are second to none. It gave me the
                    confidence to scale my strategy and focus on what really matters—performance.
                </div>

                <div className="flex items-center gap-4">
                    <Image src={'/assets/images/avatar-michael.png'}
                           alt={'avatar Daniel Reyes'} height={64} width={64}/>

                    <div>
                        <div
                            className="self-stretch justify-start text-white text-base font-medium leading-6">
                            Michael Carter, United States
                        </div>
                        <div
                            className="self-stretch justify-start text-stone-400 text-base font-medium leading-6">United
                            State
                        </div>
                    </div>
                </div>
            </Card>
        </section>
    );
}

export default TrustedByLeadres;