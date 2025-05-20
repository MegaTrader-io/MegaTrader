import {NextRequest, NextResponse} from 'next/server'
import {EmailReputationResponse} from "@/app/api/subscribe/interfaces";

const ABSTRACT_API_KEY = 'd481b3b5fcc7438f9970f43bfd0522c3'

export async function POST(request: NextRequest) {
    try {
        const {email} = await request.json()

        if (!email || typeof email !== 'string') {
            return NextResponse.json(
                {error: 'Email inválido o no proporcionado'},
                {status: 400}
            )
        }

        const apiUrl = `https://emailreputation.abstractapi.com/v1/?api_key=${ABSTRACT_API_KEY}&email=${encodeURIComponent(email)}`

        const response = await fetch(apiUrl, {method: 'GET', redirect: 'follow'})

        if (!response.ok) {
            const errorText = await response.text()
            return NextResponse.json(
                {error: 'Internal error', status: response.status, detail: errorText},
                {status: response.status}
            )
        }

        const result: EmailReputationResponse = await response.json()

        if (result.email_deliverability.status_detail === 'invalid_mailbox') {
            return NextResponse.json({success: false, message: 'Invalid email'})
        }

        return NextResponse.json({success: true, data: result})
    } catch (error: any) {
        console.error('Error al verificar email:', error)
        return NextResponse.json(
            {error: 'Error interno del servidor', detail: error.message},
            {status: 500}
        )
    }
}
