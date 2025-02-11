import React from 'react';
import Image from "next/image";
import {NotificationStatus} from "@/commons/interfaces";

interface Props {
    status: NotificationStatus
}

function NotificationIconStatus({status}: Props) {
    return (
        <Image src={`/assets/images/notifications/${status}.svg`} alt={status} width={32} height={32}/>
    );
}

export default NotificationIconStatus;