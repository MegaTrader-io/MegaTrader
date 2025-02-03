'use client';

import React, {useState} from 'react';
import InputText from "@/components/InputText";
import {Button} from "@/components/Button";
import Alert from "@/components/Alert";

function Page() {
    const [credentials, setCredentials] = useState({
        currentPassword: '',
        newPassword: '',
        confirmPassword: '',
    })

    const [errors, setErrors] = useState<{ [key: string]: string }>({});
    const [updated, setUpdated] = useState<boolean>(false);
    const hasErrors = Object.values(errors).filter(error => error !== '').length > 0;

    function changeFields(ev: React.ChangeEvent<HTMLSelectElement | HTMLInputElement>) {
        const {name, value} = ev.target;
        setCredentials(credentials => ({...credentials, [name]: value}));

        if (errors[name]) {
            setErrors(prevErrors => ({...prevErrors, [name]: ''}));
        }
    }

    function validateFields() {
        const newErrors: { [key: string]: string } = {};

        if (!credentials.currentPassword.trim()) newErrors.currentPassword = "This field is required.";
        if (!credentials.newPassword.trim()) newErrors.newPassword = "This field is required.";
        if (!credentials.confirmPassword.trim()) newErrors.confirmPassword = "This field is required.";

        if (credentials.newPassword.trim() !== credentials.confirmPassword.trim()) {
            newErrors.newPassword = "Passwords do not match."
            newErrors.confirmPassword = "Passwords do not match."
        }

        return newErrors;
    }

    function onSubmit(ev: React.ChangeEvent<HTMLFormElement>) {
        ev.preventDefault();

        const validationErrors = validateFields();

        if (Object.keys(validationErrors).length > 0) {
            setErrors(validationErrors);
            setUpdated(false)
            return;
        }

        console.info('submit stuff', credentials);
        setErrors({});
        setUpdated(true)
    }

    return (
        <div className="w-full space-y-4">
            {hasErrors &&
                <Alert className="w-full" type="error" message='Somethig went wrogn. Please try again later.'/>}
            {updated &&
                <Alert className="w-full" type="success"
                       message='Great! Your personal information have been updated successfully'/>}
            <form onSubmit={onSubmit} className="w-full text-white">
                <div className="space-y-4">
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal w-full">
                            Current password
                            <InputText
                                type="password"
                                name={'currentPassword'}
                                value={credentials.currentPassword}
                                onChange={changeFields}
                                errorMessage={errors.currentPassword}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal w-full">
                            New password
                            <InputText
                                type={'password'}
                                name={'newPassword'}
                                value={credentials.newPassword}
                                onChange={changeFields}
                                errorMessage={errors.newPassword}/>
                        </label>
                    </div>
                    <div>
                        <label className="text-stone-400 text-base font-bold leading-normal w-full">
                            Confirm password
                            <InputText
                                type={'password'}
                                name={'confirmPassword'}
                                value={credentials.confirmPassword}
                                onChange={changeFields}
                                errorMessage={errors.confirmPassword}/>
                        </label>
                    </div>

                    <div>
                        <Button type='submit' variant={'primary'}>
                            Save changes
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    );
}

export default Page;