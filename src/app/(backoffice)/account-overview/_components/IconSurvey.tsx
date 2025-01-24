import React, {useEffect, useState} from 'react';
import Image from "next/image";
import {SurveyState} from "@/commons/interfaces";

function IconSurvey({survey}: { survey?: SurveyState | null }) {
    const pencilIcon = '/assets/images/pencil.svg';
    const eyeIcon = '/assets/images/eye.svg';
    const [url, setUrl] = useState(pencilIcon);

    useEffect(() => {
        setUrl(survey && survey.id !== undefined ? eyeIcon : pencilIcon);
    }, [survey]);

    return (
        <Image src={url}
               alt={'pencil'}
               width={50}
               height={28}/>
    )
}

export default IconSurvey;