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
                            <circle cx="29" cy="29" r="29" fill="#0062FF"/>
                            <path
                                d="M26.5126 21.3002L23.1296 17H15L22.4607 26.4376L26.5126 21.3002ZM30.4617 36.5603L33.9733 41H42L34.5007 31.4609L30.4617 36.5603ZM33.8704 17.0127L15 41H23.0267L42 17.0127H33.8704Z"
                                fill="white"/>
                        </svg>
                        <div
                            className="justify-start text-white text-xl font-bold leading-8">
                            Megatrader X
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
                            <circle cx="29" cy="29" r="29" fill="#0062FF"/>
                            <path
                                d="M26.5126 21.3002L23.1296 17H15L22.4607 26.4376L26.5126 21.3002ZM30.4617 36.5603L33.9733 41H42L34.5007 31.4609L30.4617 36.5603ZM33.8704 17.0127L15 41H23.0267L42 17.0127H33.8704Z"
                                fill="white"/>
                        </svg>
                        <div
                            className="justify-start text-white text-xl font-bold leading-8">
                            Megatrader X
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
                    <svg width="58" height="58" viewBox="0 0 58 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="29" cy="29" r="29" fill="#0062FF"/>
                        <path
                            d="M26.5126 21.3002L23.1296 17H15L22.4607 26.4376L26.5126 21.3002ZM30.4617 36.5603L33.9733 41H42L34.5007 31.4609L30.4617 36.5603ZM33.8704 17.0127L15 41H23.0267L42 17.0127H33.8704Z"
                            fill="white"/>
                    </svg>

                    <div
                        className="justify-start text-white text-xl font-bold leading-8">
                        Megatrader X
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