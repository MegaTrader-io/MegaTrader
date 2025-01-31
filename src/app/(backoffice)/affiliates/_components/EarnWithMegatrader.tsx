import React from 'react';
import Card, {CardTitle} from "@/components/Card";
import Image from "next/image";

function EarnWithMegatrader() {
    return (
        <Card className="lg:col-span-7 w-full p-4 text-white">
            <CardTitle className="mb-2">
                EARN WITH MEGATRADER
            </CardTitle>
            <p className="text-stone-400 text-base font-normal  leading-normal">Invite your
                Invite friends to Megatrader, if they sign up, you and your friend will get 2 premium features from
                free!
            </p>
            <div className="mt-4 md:mt-9 grid md:grid-cols-3 gap-6">
                {/** panel 1 **/}
                <div className="text-center space-y-2">
                    <div className="h-14 p-4 bg-stone-800 rounded-[64px] justify-start items-center inline-flex">
                        <Image src={'/assets/images/message.svg'} alt={'Send Invitation'} width={24} height={24}/>
                    </div>
                    <h2
                        className="text-center text-white text-base font-bold leading-normal">
                        Send Invitation
                    </h2>
                    <p
                        className="text-center text-stone-400 text-base font-normal leading-normal">
                        Send your referral link to friends and tell them how useful Megatrader is!
                    </p>
                </div>
                {/** panel 2 **/}
                <div className="text-center space-y-2">
                    <div className="h-14 p-4 bg-stone-800 rounded-[64px] justify-start items-center inline-flex">
                        <Image src={'/assets/images/register.svg'} alt={'Registration'} width={24} height={24}/>
                    </div>
                    <h2
                        className="text-center text-white text-base font-bold leading-normal">
                        Registration
                    </h2>
                    <p
                        className="text-center text-stone-400 text-base font-normal leading-normal">
                        Let your friends register to our services using your personal referral code!
                    </p>
                </div>
                {/** panel 3 **/}
                <div className="text-center space-y-2">
                    <div className="h-14 p-4 bg-stone-800 rounded-[64px] justify-start items-center inline-flex">
                        <Image src={'/assets/images/circle-check.svg'} alt={'Use Megatrader for free!'} width={24}
                               height={24}/>
                    </div>
                    <h2
                        className="text-center text-white text-base font-bold leading-normal">
                        Use Megatrader for free!
                    </h2>
                    <p
                        className="text-center text-stone-400 text-base font-normal leading-normal">
                        You and your friends get 2 premium Megatrader features for free!
                    </p>
                </div>
            </div>
        </Card>
    );
}

export default EarnWithMegatrader;