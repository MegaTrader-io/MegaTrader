import {NextResponse} from 'next/server'
import {sign} from 'jsonwebtoken';

const SECRET = process.env.INTERCOM_SECRET_KEY || 'fallbackSecret';

export async function GET() {
    const user = {
        user_id: 123,
        name: 'foo bar',
        email: 'test@megatrader.io',
        intercom_user_jwt: ''
    }

    user.intercom_user_jwt = sign({
        user_id: user.user_id,
        email: user.email
    }, SECRET)

    return NextResponse.json(user, {status: 200});
}