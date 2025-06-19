import {NextConfig} from 'next';

const nextConfig: NextConfig = {
    images: {
        remotePatterns: [
            {
                protocol: 'https',
                hostname: 'megatrader.io',
                pathname: '/assets/images/**',
            },
        ],
    },
    async headers() {
        const isProduction = process.env.NEXT_PUBLIC_ENVIRONMENT === 'production';

        const scriptSrc = isProduction
            ? `'self' 'unsafe-inline' https://cdn.livechatinc.com https://api.livechatinc.com https://widget.intercom.io https://js.intercomcdn.com https://www.googletagmanager.com https://connect.facebook.net https://googleads.g.doubleclick.net`
            : `'self' 'unsafe-inline' 'unsafe-eval' https://cdn.livechatinc.com https://api.livechatinc.com https://widget.intercom.io https://js.intercomcdn.com https://www.googletagmanager.com https://connect.facebook.net https://googleads.g.doubleclick.net`;

        const cspValue = `
        script-src ${scriptSrc};
        object-src 'none';
        frame-src 'self' https://www.googletagmanager.com https://td.doubleclick.net;
        frame-ancestors 'self';
        connect-src 'self' https://api.livechatinc.com https://api-iam.intercom.io https://js.intercomcdn.com wss://nexus-websocket-a.intercom.io https://www.google.com;
    `.replace(/\s{2,}/g, ' ').trim();

        const commonHeaders = [
            {
                key: 'Content-Security-Policy',
                value: cspValue,
            },
            {
                key: 'Cache-Control',
                value: 'no-store, no-cache, must-revalidate, proxy-revalidate, max-age=0',
            },
            {
                key: 'Pragma',
                value: 'no-cache',
            },
            {
                key: 'Expires',
                value: '0',
            },
        ];

        return [
            {
                source: '/:path*',
                headers: commonHeaders,
            },
        ];
    }
};

export default nextConfig;
