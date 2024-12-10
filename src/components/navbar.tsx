import Image from 'next/image';
import Link from "@/components/link";

export default function Navbar() {
    return (
        <nav className="flex justify-start items-center flex-row gap-2">
            <Link href="#">
                Login
            </Link>
            <Link className="px-3" href="#">
                <Image
                    src="/assets/images/menu-more.svg"
                    alt="Menu More"
                    aria-label="Menu More"
                    width={24}
                    height={24}
                />
            </Link>
        </nav>
    );
}
