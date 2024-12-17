import Image from 'next/image'
import Navbar from "@/components/navbar";

export default function Header() {
    return (
        <div
            className="w-full flex items-center justify-between flex-nowrap relative">
            <div className="w-auto">
                <Image
                    src="../assets/images/megatrader2.svg"
                    alt="Logo"
                    height={83}
                    width={338}
                />
            </div>
            <Navbar/>
        </div>
    );
}
