import {NextResponse} from 'next/server'
import {sign} from 'jsonwebtoken';

const SECRET = process.env.INTERCOM_SECRET_KEY || 'fallbackSecret';

export async function GET() {
    const userId = 123;
    const email = 'test@megatrader.io';
    const name = 'foo bar';

    const user = {
        user_id: userId,
        name: name,
        email: email,
        intercomUserJwt: sign({
            user_id: userId,
            email: email
        }, SECRET)
    }

    return NextResponse.json(user, {status: 200});
}