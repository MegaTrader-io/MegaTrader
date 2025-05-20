import {NextRequest} from "next/server";

export function getClientIp(req: NextRequest): string | null {
    const forwarded = req.headers.get('forwarded')
    console.info('forwarded', forwarded);

    if (forwarded) {
        const match = forwarded.match(/for="?([^;"]+)"?/)
        if (match) return match[1]
    }

    const xForwardedFor = req.headers.get('x-forwarded-for')
    console.info('xForwardedFor', xForwardedFor);

    if (xForwardedFor) {
        return xForwardedFor.split(',')[0].trim()
    }

    const xRealIp = req.headers.get('x-real-ip')
    console.info('xRealIp', xRealIp);

    if (xRealIp) return xRealIp

    return null;
}