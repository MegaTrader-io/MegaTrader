import React, {useState} from 'react';
import {EyeIcon, EyeSlashIcon} from "@heroicons/react/16/solid";
import clsx from "clsx";

function EyeComponent({type = 'text', className}: { type: 'password' | 'text', className?: string }) {
    const [toggle, setToggle] = useState(type || 'text');
    const Icon = toggle === 'password' && type === 'password' ? EyeSlashIcon : EyeIcon;

    // absolute right-4
    return (
        <Icon className={clsx('h-6 w-6 text-[#A8A29E] select-none cursor-pointer', className)}
              onClick={() => {
                  setToggle(prev => prev === 'password' ? 'text' : 'password');
              }}/>
    );
}

export default EyeComponent;