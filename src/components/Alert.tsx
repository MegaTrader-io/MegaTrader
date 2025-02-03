import {CheckIcon, XMarkIcon} from "@heroicons/react/16/solid";
import React from "react";
import clsx from "clsx";

// Definir tipos correctamente
type AlertType = 'success' | 'error';

interface Prop {
    className?: string;
    type: AlertType;
    message: string;
}

const AlertIcon: React.FC<{ type: AlertType }> = ({type}) => {
    if (type === 'success') {
        return <CheckIcon className="w-5 h-5"/>;
    }

    return <XMarkIcon className="w-5 h-5"/>;
};

const Alert: React.FC<Prop> = ({className, type, message}) => {
    const bgColor = type === 'success' ? 'bg-teal-400' : 'bg-red-400';
    const textColor = type === 'success' ? 'text-teal-400' : 'text-red-400';

    return (
        <div
            className={clsx('p-4 bg-[#1e1e1e] rounded-lg justify-start items-start gap-4 inline-flex overflow-hidden', className)}>
            <div className={`w-6 h-6 flex items-center justify-center rounded-full ${bgColor}`}>
                <AlertIcon type={type}/>
            </div>
            <div
                className={`grow shrink basis-0 self-stretch ${textColor} text-base font-normal leading-normal`}>
                {message}
            </div>
        </div>
    );
};

export default Alert;
