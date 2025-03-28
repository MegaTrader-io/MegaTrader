import {CheckIcon, ExclamationCircleIcon, XMarkIcon} from "@heroicons/react/16/solid";
import React from "react";
import clsx from "clsx";

type AlertType = 'success' | 'error' | 'info';

interface Prop {
    className?: string;
    type: AlertType;
    message: string;
}

const AlertIcon: React.FC<{ type: AlertType }> = ({type}) => {
    if (type === 'success') {
        return <CheckIcon className="w-5 h-5 text-black"/>
    }
    if (type === 'info') {
        return <ExclamationCircleIcon className="w-6 h-6 rotate-180 fill-blue-400"/>
    }

    return <XMarkIcon className="w-5 h-5"/>;
};

const Alert: React.FC<Prop> = ({className, type, message}) => {
    const bgColor = {
        'success': 'bg-teal-400',
        'error': 'bg-red-400',
        'info': 'bg-black',
    } [type];

    const textColor = {
        'success': 'text-teal-400',
        'error': 'text-red-400',
        'info': 'text-blue-400',
    } [type];

    return (
        <div
            className={clsx('p-4 bg-[#1e1e1e] rounded-lg justify-start items-start gap-4 flex overflow-hidden', className)}>
            <div className={`w-6 h-6 flex items-center justify-center rounded-full ${bgColor}`}>
                <AlertIcon type={type}/>
            </div>
            <div
                className={`grow shrink basis-0 self-stretch ${textColor} text-base font-medium leading-normal`}>
                {message}
            </div>
        </div>
    );
};

export default Alert;
