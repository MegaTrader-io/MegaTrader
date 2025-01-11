import {NextConfig} from 'next';

const nextConfig: NextConfig = {
    basePath: '',
    images: {
        remotePatterns: [
            {
                protocol: 'https',
                hostname: 'megatrader.io',
                port: '',
                pathname: '/**',
            },
        ],
    },
};

export default nextConfig;
