import {NextResponse} from 'next/server'
import {sign} from 'jsonwebtoken';
import dayjs from "dayjs";
import utc from 'dayjs/plugin/utc';

dayjs.extend(utc);

const SECRET = process.env.INTERCOM_SECRET_KEY || 'fallbackSecret';

export async function GET() {
    const userId = 123;
    const email = 'test@megatrader.io';
    const name = 'foo bar';
    const phone = '+50760036763'//'+13054310620';
    const createdAt = dayjs().utc().unix()

    const user = {
        user_id: userId,
        name: name,
        email: email,
        phone,
        createdAt,
        intercomUserJwt: sign({
            user_id: userId,
            email: email
        }, SECRET)
    }

    return NextResponse.json(user, {status: 200});
}