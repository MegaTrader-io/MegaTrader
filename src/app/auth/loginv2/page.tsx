import Badge from "@/components/ui/badge";
import Image from "next/image";
import InputText from "@/components/ui/input-text";
import Link from "next/link";
import {InputCheckbox} from "@/components/ui/input-checkbox";

export default async function Login() {
    return (
        <div className="mx-auto max-w-screen-3xl w-full ">
            <div className="grid grid-cols-[1fr_924px] min-h-full">
                <div className="text-white w-[676px] flex items-center ">
                    <div className="max-w-[406px] mx-auto">
                        <h1 className="text-white text-5xl font-light uppercase leading-[60px] mb-2">
                            SIGN IN
                        </h1>
                        <h2
                            className="text-stone-400 text-base font-normal font-roboto leading-normal tracking-wide">Welcome
                            back! Please enter your details.
                        </h2>

                        <form className="space-y-4 my-8">
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
                <div className="h-dvh relative bg-[#1e1e1e] px-[120px] w-[924px]">
                    <Badge className="mt-[91px] mb-[17px]">
                        Start earning up to 90% profit
                    </Badge>
                    <p className="text-stone-400 text-base font-normal leading-normal max-w-[434px]">No minimum trading
                        days on your
                        evaluation. Unlocking opportunities and maximizing potential in the
                        dynamic world of trading.
                    </p>


                    <div
                        style={{
                            position: 'relative',
                            width: '100%', // O el tamaño específico que desees
                            height: '70vh', // El 70% del viewport height
                        }}
                    >
                        <Image
                            className="absolute bottom-0 right-0"
                            src="/assets/images/img_1.png"
                            alt="Trading Platform Interface"
                            layout="fill"
                            objectFit="contain" // Mantiene las proporciones sin recortar
                            quality={100}
                        />
                    </div>
                </div>
            </div>
        </div>
    )
}