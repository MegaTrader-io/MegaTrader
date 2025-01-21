import React from "react";

type GaugeSVGProps = {
    value: number;
    minValue: string;
    maxValue: string;
    centerValue: string;
};

const GaugeSVG: React.FC<GaugeSVGProps> = ({value, minValue, maxValue, centerValue}) => {
    const calculateNeedleRotation = (val: number): number => {
        const minAngle = -135;
        const maxAngle = 135;
        const clampedValue = Math.max(0, Math.min(100, val));
        return (clampedValue / 100) * (maxAngle - minAngle) + minAngle;
    };

    const needleRotation = calculateNeedleRotation(value);

    const calculateClipAngle = (val: number): number => {
        const minAngle = -135;
        const maxAngle = 135;
        const clampedValue = Math.max(0, Math.min(100, val));
        return (clampedValue / 100) * (maxAngle - minAngle) + minAngle;
    };

    const clipAngle = calculateClipAngle(value);

    const polarToCartesian = (centerX: number, centerY: number, radius: number, angleInDegrees: number) => {
        const angleInRadians = ((angleInDegrees - 90) * Math.PI) / 180.0;
        return {
            x: centerX + radius * Math.cos(angleInRadians),
            y: centerY + radius * Math.sin(angleInRadians),
        };
    };

    const start = polarToCartesian(144, 144, 144, -135);
    const end = polarToCartesian(144, 144, 144, clipAngle);

    const largeArcFlag = clipAngle - -135 <= 180 ? "0" : "1";

    const pathData = [
        `M ${start.x} ${start.y}`,
        `A 144 144 0 ${largeArcFlag} 1 ${end.x} ${end.y}`,
        `L 144 144`,
        `Z`,
    ].join(" ");

    return (
        <svg
            width="288"
            height="288"
            viewBox="0 0 288 288"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <g clipPath="url(#outerClip)">
                <path
                    d="M235.641 235.641C241.265 241.265 250.446 241.306 255.48 235.149C270.367 216.942 280.616 195.303 285.233 172.093C290.789 144.16 287.938 115.206 277.039 88.8936C266.14 62.581 247.683 40.0913 224.002 24.2684C200.321 8.44545 172.481 0 144 0C115.52 0 87.6786 8.44545 63.9979 24.2684C40.3172 40.0913 21.8604 62.581 10.9614 88.8936C0.0623299 115.206 -2.78935 144.16 2.76692 172.093C7.38361 195.303 17.6328 216.942 32.5196 235.149C37.5536 241.306 46.7354 241.265 52.359 235.641C57.9825 230.017 57.8993 220.948 53.0207 214.667C42.0808 200.583 34.5183 184.094 31.0135 166.474C26.5685 144.128 28.8499 120.965 37.5691 99.9149C46.2883 78.8648 61.0538 60.873 79.9983 48.2147C98.9429 35.5564 121.216 28.8 144 28.8C166.784 28.8 189.057 35.5564 208.002 48.2147C226.946 60.873 241.712 78.8648 250.431 99.9149C259.15 120.965 261.431 144.128 256.986 166.474C253.482 184.094 245.919 200.583 234.979 214.667C230.101 220.948 230.017 230.018 235.641 235.641Z"
                    fill="#292524"
                />
                <path d={pathData} fill="url(#paint0_linear)"/>
            </g>

            <g transform={`rotate(${needleRotation} 144 144)`}>
                <path
                    fillRule="evenodd"
                    clipRule="evenodd"
                    d="M155.721 142.411L144 33.9994L132.28 142.411C132.097 143.245 132 144.111 132 144.999C132 151.627 137.373 156.999 144 156.999C150.628 156.999 156 151.627 156 144.999C156 144.111 155.904 143.245 155.721 142.411Z"
                    fill="#FFB34A"
                />
            </g>

            <text
                x="144"
                y="200"
                textAnchor="middle"
                fontSize="20"
                fill="#FFB34A"
                fontWeight="300"
            >
                {centerValue}
            </text>
            <text
                x="45"
                y="270"
                textAnchor="middle"
                fontSize="16"
                fill="#A8A29E"
                fontWeight="400"
            >
                {minValue}
            </text>
            <text
                x="235"
                y="270"
                textAnchor="middle"
                fontSize="16"
                fill="#A8A29E"
                fontWeight="400"
            >
                {maxValue}
            </text>

            <defs>
                <clipPath id="outerClip">
                    <path
                        d="M235.641 235.641C241.265 241.265 250.446 241.306 255.48 235.149C270.367 216.942 280.616 195.303 285.233 172.093C290.789 144.16 287.938 115.206 277.039 88.8936C266.14 62.581 247.683 40.0913 224.002 24.2684C200.321 8.44545 172.481 0 144 0C115.52 0 87.6786 8.44545 63.9979 24.2684C40.3172 40.0913 21.8604 62.581 10.9614 88.8936C0.0623299 115.206 -2.78935 144.16 2.76692 172.093C7.38361 195.303 17.6328 216.942 32.5196 235.149C37.5536 241.306 46.7354 241.265 52.359 235.641C57.9825 230.017 57.8993 220.948 53.0207 214.667C42.0808 200.583 34.5183 184.094 31.0135 166.474C26.5685 144.128 28.8499 120.965 37.5691 99.9149C46.2883 78.8648 61.0538 60.873 79.9983 48.2147C98.9429 35.5564 121.216 28.8 144 28.8C166.784 28.8 189.057 35.5564 208.002 48.2147C226.946 60.873 241.712 78.8648 250.431 99.9149C259.15 120.965 261.431 144.128 256.986 166.474C253.482 184.094 245.919 200.583 234.979 214.667C230.101 220.948 230.017 230.018 235.641 235.641Z"
                    />
                </clipPath>
                <linearGradient
                    id="paint0_linear"
                    x1="144"
                    y1="0"
                    x2="144"
                    y2="288"
                    gradientUnits="userSpaceOnUse"
                >
                    <stop offset="0.15" stopColor="#FFB34A"/>
                    <stop offset="1" stopColor="#2DD4BF"/>
                </linearGradient>
            </defs>
        </svg>
    );
};

export default GaugeSVG;
