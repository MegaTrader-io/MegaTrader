import { NextConfig } from 'next';

const nextConfig: NextConfig = {
    images: {
        remotePatterns: [
            {
                protocol: 'https',
                hostname: 'megatrader.io',
                pathname: '/assets/images/**', // Ajusta a la ruta de tus imágenes
            },
        ],
    },
    async headers() {
        return [
            {
                source: '/:path*',
                headers: [
                    {
                        key: 'Content-Security-Policy',
                        value: `script-src 'self' 'unsafe-inline' https://cdn.livechatinc.com; object-src 'none'; frame-ancestors 'self';`,
                    },
                ],
            },
        ];
    },
};

export default nextConfig;
