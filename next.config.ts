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
};

export default nextConfig;
