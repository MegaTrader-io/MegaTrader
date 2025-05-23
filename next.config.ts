import {NextConfig} from 'next';

const isProduction = process.env.NEXT_PUBLIC_ENVIRONMENT === "production";

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
        if (isProduction) {
            return [
                {
                    source: '/:path*',
                    headers: [
                        {
                            key: 'Content-Security-Policy',
                            value: `
                                script-src 'self' 'unsafe-inline' https://cdn.livechatinc.com https://api.livechatinc.com https://widget.intercom.io https://js.intercomcdn.com https://www.googletagmanager.com;
                                object-src 'none';
                                frame-src 'self' https://www.googletagmanager.com;
                                frame-ancestors 'self';
                                connect-src 'self' https://api.livechatinc.com https://api-iam.intercom.io https://js.intercomcdn.com wss://nexus-websocket-a.intercom.io;
                            `.replace(/\s{2,}/g, ' ').trim(),
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
                    ],
                },
            ];
        }

        return [];
    },
};

export default nextConfig;
