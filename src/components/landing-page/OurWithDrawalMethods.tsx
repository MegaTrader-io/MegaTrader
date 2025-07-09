import React from 'react';
import Image from "next/image";

function OurWithDrawalMethods() {
    return (
        <section id="feature-our-with-drawal-methods" className="px-4 py-12">
            <div className="pb-4 self-stretch text-center text-white text-[40px] font-light uppercase leading-[48px]">
                Our withdrawal Methods
            </div>

            <div
                className="mx-auto  max-w-[612px] text-center text-xl leading-8 font-medium text-stone-400 md:max-w-[860px]">
                Withdraw profits securely using RiseWorks, Bitcoin, or Ethereum, with a 90% split on all earnings and
                fast, reliable processing to support your trading success.
            </div>

            <div className="mt-12 space-y-4 md:space-y-0 md:flex gap-4">
                <div className="px-4 w-full space-y-2 py-8 bg-[#1e1e1e] rounded-2xl">
                    <div
                        className="self-stretch text-center justify-start text-white text-xl font-medium font-['Roboto'] uppercase leading-6">RISEWORKS
                    </div>

                    <div className="flex justify-center">
                        <Image src={'/assets/images/rise.png'} alt={'rise'} width={48} height={48}/>
                    </div>
                </div>
                <div className="px-4 w-full space-y-2 py-8 bg-[#1e1e1e] rounded-2xl">
                    <div
                        className="self-stretch text-center justify-start text-white text-xl font-medium font-['Roboto'] uppercase leading-6">Bitcoin
                    </div>

                    <div className="flex justify-center">
                        <Image src={'/assets/images/bitcoin.png'} alt={'rise'} width={48} height={48}/>
                    </div>
                </div>
                <div className="px-4 w-full space-y-2 py-8 bg-[#1e1e1e] rounded-2xl">
                    <div
                        className="self-stretch text-center justify-start text-white text-xl font-medium font-['Roboto'] uppercase leading-6">Etherum
                    </div>

                    <div className="flex justify-center">
                        <Image src={'/assets/images/etherum.png'} alt={'rise'} width={48} height={48}/>
                    </div>
                </div>
            </div>

        </section>
    );
}

export default OurWithDrawalMethods;