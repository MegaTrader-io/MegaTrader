'use client';

import React from 'react';
import Alert from "@/components/Alert";
import {Button} from "@/components/Button";

function Page() {
    return (
        <div className="space-y-8">
            <Alert className="w-full" type={'error'} message={'Your account is not protected with two-factor authentication'} />
            <Button>
                SET 2FA
            </Button>
        </div>
    );
}

export default Page;