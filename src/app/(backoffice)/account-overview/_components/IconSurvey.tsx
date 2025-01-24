import React, {useState} from 'react';
import Image from "next/image";

function IconSurvey({canEdit}: { canEdit: boolean }) {
    const pencilIcon = '/assets/images/pencil.svg';
    const eyeIcon = '/assets/images/eye.svg';
    const [url] = useState(canEdit ? eyeIcon  : pencilIcon);

    return (
        <Image src={url}
               alt={'pencil'}
               width={50}
               height={28}/>
    )
}

export default IconSurvey;