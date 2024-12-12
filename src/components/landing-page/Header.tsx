import Image from 'next/image'
import Navbar from "@/components/navbar";

export default function Header() {
    return (
        <header>
            <div
                className="mx-auto max-w-7xl w-full px-8 lg:px-0 py-6 flex items-center justify-between flex-nowrap relative">
                <div className="w-auto">
                    <Image
                        src="../assets/images/megatrader.svg"
                        alt="Logo"
                        height={42}
                        width={360}
                    />
                </div>
                <Navbar/>
            </div>
        </header>
    );
}
