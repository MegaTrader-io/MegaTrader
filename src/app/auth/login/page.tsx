'use client';

import React from "react";


export default function Login() {
    return (
        <div className="flex flex-1">
            <div className="flex w-full lg:w-2/5 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none  xl:px-24">
                <div className="mx-auto w-full max-w-[409px]">
                    <div className="w-full mx-auto space-y-8">
                        <div>
                            <h1 className="text-white text-5xl font-light uppercase leading-[60px] mb-2">
                                SIGN IN
                            </h1>
                            <h2
                                className="text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide"
                            >
                                Welcome back! Please enter your details.
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div className="relative hidden w-0 flex-1 lg:block bg-[#1e1e1e] pl-[120px] overflow-hidden">

            </div>
        </div>
    );
}
