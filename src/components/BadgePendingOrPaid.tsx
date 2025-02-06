import React from 'react';
import Badge from "@/components/Badge";

interface Props {
    status: 'pending' | 'paid'
}

const BadgePendingOrPaid: React.FC<Props> = ({status}) => {
    return (
        <Badge shape={'pill'}
               variant={status === 'paid'
                   ? 'secondary'
                   : 'primary'}>
            {status}
        </Badge>
    );
}

export default BadgePendingOrPaid;