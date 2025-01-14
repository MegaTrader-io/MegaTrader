import Button from './BaseButton';
import {ChevronRightIcon} from "@heroicons/react/16/solid";
import React, {FC} from "react";

export const IconButton: FC = () => {
    return (
        <Button
            variant="primary"
            styleType="filled"
            size="md"
            icon={<ChevronRightIcon />}
            iconPosition="right">
            OPEN AN ACCOUNT
        </Button>
    );
};