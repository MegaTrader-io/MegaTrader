import {NextRequest, NextResponse} from 'next/server'
import {
    EmailValidationResponse,
    IpGeolocationResponse,
    KlaviyoLocation
} from '@/app/api/subscribe/interfaces'
import {getClientIp} from '@/app/api/subscribe/util'

const ABSTRACT_EMAIL_API_KEY = '6251ad73244b4a23926998a839393c28'
const ABSTRACT_GEO_API_KEY = '5be85e728309415cb70f0976e9b0d363'
const KLAVIYO_API_KEY = 'pk_5a92a736289822ee50fdb33a5087b2b776'
const KLAVIYO_LIST_ID = 'WZVM7e'

export async function POST(request: NextRequest) {
    try {
        const {email} = await request.json()

        if (!email || typeof email !== 'string') {
            return NextResponse.json({success: false, message: 'The email is required'}, {status: 400})
        }

        const emailValidation = await validateEmail(email)
        if (!emailValidation.success) {
            return NextResponse.json(
                {success: false, message: 'Invalid email', isValidEmail: false, detail: emailValidation.detail},
                {status: 422}
            )
        }

        const ip = getClientIp(request)
        const location = ip ? await getLocation(ip) : undefined

        const {profileId, duplicated} = await createKlaviyoProfile(email, location)
        if (duplicated && profileId) {
            return NextResponse.json(
                {success: false, message: 'Email already exists', profile_id: profileId},
                {status: 200}
            )
        }

        if (!profileId) {
            return NextResponse.json(
                {success: false, message: 'Klaviyo profile creation failed'},
                {status: 500}
            )
        }

        const addedToList = await addProfileToList(profileId)
        if (!addedToList.success) {
            return NextResponse.json(
                {
                    success: false,
                    profile_id: profileId,
                    message: 'Failed to add profile to list',
                    status: addedToList.status,
                    detail: addedToList.detail
                },
                {status: addedToList.status}
            )
        }

        return NextResponse.json(
            {success: true, profile_id: profileId, klaviyo: addedToList.klaviyo},
            {status: 201}
        )
    } catch (error: any) {
        console.error('Fatal error:', error)
        return NextResponse.json(
            {success: false, error: 'Internal server error', detail: error.message},
            {status: 500}
        )
    }
}

async function validateEmail(email: string): Promise<{ success: boolean; detail?: string }> {
    const url =
        `https://emailvalidation.abstractapi.com/v1/?api_key=${ABSTRACT_EMAIL_API_KEY}&email=${encodeURIComponent(email)}`
    const res = await fetch(url)
    if (!res.ok) return {success: false, detail: await res.text()}
    const data: EmailValidationResponse = await res.json()
    return {success: data.deliverability === 'DELIVERABLE'}
}

async function getLocation(ip: string): Promise<KlaviyoLocation | undefined> {
    try {
        const url =
            `https://ipgeolocation.abstractapi.com/v1/?api_key=${ABSTRACT_GEO_API_KEY}&ip_address=${encodeURIComponent(ip)}`
        const res = await fetch(url)

        if (res.ok) {
            const data: IpGeolocationResponse = await res.json()
            const location = {
                ip,
                city: data.city,
                region: data.region,
                zip: data.postal_code ?? undefined,
                country: data.country,
                longitude: data.longitude,
                latitude: data.latitude,
                timezone: data.timezone.name
            }

            console.info(url, res.status, location);

            return location;
        }

        if (res.status === 400) {
            const errBody = await res.json()

            console.info(url, res.status, errBody);

            if (
                errBody?.error?.code === 'validation_error' &&
                errBody?.error?.details?.ip_address?.[0] === 'Invalid IP Address.'
            ) {
                return undefined
            }
        }
        throw new Error(`Geo API error: ${await res.text()}`)
    } catch (err) {
        console.warn('Geo location skipped:', err)
        return undefined
    }
}

async function createKlaviyoProfile(
    email: string,
    location?: KlaviyoLocation
): Promise<{ profileId: string | null; duplicated: boolean }> {
    const payload = {
        data: {
            type: 'profile',
            attributes: {email, locale: 'en-US', properties: {}, ...(location ? {location} : {})}
        }
    }

    const url = 'https://a.klaviyo.com/api/profiles?additional-fields[profile]=subscriptions';
    const res = await fetch(
        url,
        {
            method: 'POST',
            headers: {
                Authorization: `Klaviyo-API-Key ${KLAVIYO_API_KEY}`,
                accept: 'application/vnd.api+json',
                'content-type': 'application/vnd.api+json',
                revision: '2025-04-15'
            },
            body: JSON.stringify(payload)
        }
    )
    const text = await res.text()

    console.info(url, res.status, text);

    if (res.status === 409) {
        const data = JSON.parse(text)
        return {profileId: data?.errors?.[0]?.meta?.duplicate_profile_id || null, duplicated: true}
    }
    if (res.status !== 201) return {profileId: null, duplicated: false}
    const data = JSON.parse(text)
    return {profileId: data?.data?.id || null, duplicated: false}
}

async function addProfileToList(
    profileId: string
): Promise<{ success: boolean; status: number; detail?: string; klaviyo?: any }> {

    const body = {data: [{type: 'profile', id: profileId}]};
    const url = `https://a.klaviyo.com/api/lists/${KLAVIYO_LIST_ID}/relationships/profiles`;
    const res = await fetch(
        url,
        {
            method: 'POST',
            headers: {
                Authorization: `Klaviyo-API-Key ${KLAVIYO_API_KEY}`,
                accept: 'application/vnd.api+json',
                'content-type': 'application/vnd.api+json',
                revision: '2025-04-15'
            },
            body: JSON.stringify(body)
        }
    )

    if (res.status === 204) {
        console.info(url, res.status);

        return {success: true, status: 204}
    }

    const detail = await res.text()

    console.info(url, res.status, detail);

    return {success: false, status: res.status, detail}
}
