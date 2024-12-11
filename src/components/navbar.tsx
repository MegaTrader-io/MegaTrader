import Image from 'next/image';
import Link from "@/components/link";

export default function Navbar() {
    return (
        <nav className="flex justify-start items-center flex-row gap-3.5">
            <Link href="#">
                Login
            </Link>
            <Link className="py-[15px] px-[18px] leading-none" href="#">
                <Image
                    src="/assets/images/menu-more.svg"
                    alt="Menu More"
                    aria-label="Menu More"
                    width={16}
                    height={4}
                />
            </Link>
        </nav>
    );
}
