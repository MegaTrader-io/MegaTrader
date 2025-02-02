'use client';

import React from 'react';
import {usePathname} from "next/navigation";
import Card from "@/components/Card";

function Page() {
    const currentPath = usePathname();
    return (
        <Card className="w-full text-white">
            {currentPath}
        </Card>
    );
}

export default Page;