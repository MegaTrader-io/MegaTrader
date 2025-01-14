'use client'

import Footer from "@/components/landing-page/Footer";
import Header from "@/components/ui/Header";
import Card from "@/components/card";
import {Button} from "@/components/ui/Button";
import React from "react";
import Link from "next/link";
import {PlusIcon} from "@heroicons/react/16/solid";

export default function Dashboard() {
    return <>
        <Header/>
        <main className="w-full max-w-7xl mx-auto px-4 my-5 flex items-center justify-between">
            <Card className="w-full grid grid-cols-[256px_1fr_auto] items-center justify-between gap-4">
                <div className="h-12 w-[256px] bg-stone-800 rounded-xl border border-neutral-700">

                </div>
                <div className="w-full">
                    <Link href="#" className="text-base btn-link">
                        Manage Subscription
                    </Link>
                </div>
                <div className="flex gap-2">
                    <Button variant="dark">
                        RESET
                    </Button>
                    <Button variant="dark"
                            icon={<PlusIcon/>}
                            iconPosition="left">
                        CREATE NEW
                    </Button>
                </div>
            </Card>


        </main>
        <Footer/>
    </>
}