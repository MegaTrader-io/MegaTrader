import React from 'react';
import Image from "next/image";
import clsx from "clsx";

function LogoExpanded({className, logoType = 'original'}: { className?: string, logoType?: 'original' | 'black' }) {
    return (
        <div className={clsx('flex items-center gap-4', className)}>
            <Image src={'/assets/images/logo-mt.svg'}
                   width={60} height={60} alt={'Logo Megatrader'}/>
            <Image
                src={`../assets/images/megatrader-${logoType}.svg`}
                alt="Logo"
                width={200}
                height={45}
            />
        </div>
    );
}

export default LogoExpanded;