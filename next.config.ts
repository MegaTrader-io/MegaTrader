import {NextConfig} from 'next';


const isProduction = process.env.NEXT_PUBLIC_ENVIRONMENT === "production";


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
        if (isProduction) {
            return [
                {
                    source: '/:path*',
                    headers: [
                        {
                            key: 'Content-Security-Policy',
                            value: `script-src 'self' 'unsafe-inline' https://cdn.livechatinc.com https://api.livechatinc.com; object-src 'none'; frame-ancestors 'self'; connect-src 'self' https://api.livechatinc.com;`,
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
