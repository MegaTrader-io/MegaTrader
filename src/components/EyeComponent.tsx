import React, {useState} from 'react';
import {EyeIcon, EyeSlashIcon} from "@heroicons/react/16/solid";
import clsx from "clsx";

type EyeType = 'password' | 'text';

interface EyeProps {
    type: EyeType,
    onChange?: (type: EyeType) => void,
    className?: string
}

function EyeComponent({type = 'text', className, onChange}: EyeProps) {
    const [toggle, setToggle] = useState(type || 'text');
    const Icon = toggle === 'password' && type === 'password' ? EyeSlashIcon : EyeIcon;

    return (
        <Icon className={clsx('h-6 w-6 text-[#A8A29E] select-none cursor-pointer', className)}
              onClick={() => {
                  const newType = toggle === 'password' ? 'text' : 'password';

                  setToggle(newType);

                  if (onChange) {
                      onChange(newType)
                  }
              }}/>
    );
}

export default EyeComponent;