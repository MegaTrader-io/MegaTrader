import {NextRequest} from "next/server";

export function getClientIp(req: NextRequest): string | null {
    const forwarded = req.headers.get('forwarded')
    if (forwarded) {
        const match = forwarded.match(/for="?([^;"]+)"?/)
        if (match) return match[1]
    }

    const xForwardedFor = req.headers.get('x-forwarded-for')
    if (xForwardedFor) {
        return xForwardedFor.split(',')[0].trim()
    }

    const xRealIp = req.headers.get('x-real-ip')
    if (xRealIp) return xRealIp

    return null;
}