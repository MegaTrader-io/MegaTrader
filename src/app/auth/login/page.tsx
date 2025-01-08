import InputText from "@/components/ui/input-text";
import {InputCheckbox} from "@/components/ui/input-checkbox";
import Link from "next/link";
import Badge from "@/components/ui/badge";
import Image from "next/image";

export default async function Login() {

    return <div className="flex flex-1">
        <div className="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div className="mx-auto w-full max-w-sm lg:w-96">
                <div className="mt-10">
                    <div>

                        <div className="w-full mx-auto">
                            <h1 className="text-white text-5xl font-light uppercase leading-[60px] mb-2">
                                SIGN IN
                            </h1>
                            <h2
                                className="text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide">Welcome
                                back! Please enter your details.
                            </h2>

                            <form action="#" method="POST" className="space-y-4 my-8">
                                <div>
                                    <InputText placeholder={'Email'} name="email" type="email"/>
                                </div>
                                <div>
                                    <InputText placeholder={'Password'} name="password" type="password"/>
                                </div>

                                <div className="flex items-center justify-between">
                                    <InputCheckbox label="Remember me"/>
                                    <Link href="#" className="text-sm text-orange-400 hover:text-orange-300">
                                        Forgot Password?
                                    </Link>
                                </div>

                                <button
                                    className="h-12 w-full px-4 py-3 bg-[#ffb34a] rounded-xl border-2 border-[#ffb34a] justify-center items-center gap-2 inline-flex">
                                    <div
                                        className="text-slate-950 text-base font-normal uppercase leading-normal">Sign
                                        In
                                    </div>
                                </button>
                            </form>

                            <div className="text-center space-y-2">
                                <a href="javascript:void(0);"
                                   className="text-center w-full text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide">Don{'\''}t
                                    have an account?
                                </a>

                                <button
                                    className="h-12 w-full  px-4 py-3 bg-stone-800 rounded-xl border border-neutral-700 justify-center items-center gap-2 inline-flex">
                                    <div
                                        className="text-neutral-50 text-base font-normal uppercase leading-normal">Create
                                        account
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div className="relative hidden w-0 flex-1 lg:block bg-[#1e1e1e] pl-[120px] overflow-hidden">
            <Badge className="mt-[91px] mb-[17px]">
                Start earning up to 90% profit
            </Badge>
            <p className="text-stone-400 text-base font-normal leading-normal max-w-[434px]">No minimum trading
                days on your
                evaluation. Unlocking opportunities and maximizing potential in the
                dynamic world of trading.
            </p>

            <div
                className="h-full w-full mt-[111px] overflow-hidden relative bg-[#151211] rounded-tl-[36px] shadow-[0px_30px_35px_32px_rgba(0,0,0,0.20)] border-l-8 border-t-8 border-[#474b54]"
            >
                <Image
                    className="max-w-screen-xl mx-auto"
                    src="/assets/images/Account_Overview.png"
                    alt="Trading Platform Interface"
                    layout="fill"
                    objectFit="contain" // Mantiene las proporciones sin recortar
                    quality={100}
                />
            </div>
        </div>
    </div>
}