import Card from "@/components/Card";
import {Button} from "@/components/Button";
import React from "react";
import Link from "next/link";
import {PlusIcon} from "@heroicons/react/16/solid";
import Dropdown from "@/components/Dropdown";

export default function AccountOverView() {
    return <>
        <Card className="w-full grid grid-cols-[auto_1fr_auto] items-center justify-between gap-4">
            <Dropdown />
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
    </>
}