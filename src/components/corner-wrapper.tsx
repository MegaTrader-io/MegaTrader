import '@/app/corner-wrapper.css';

type CornerWrapperProps = React.HTMLAttributes<HTMLDivElement> & {
    children: React.ReactNode;
};

export default function CornerWrapper({className = '', children, ...props}: CornerWrapperProps) {
    return (
        <div {...props}
             className={`relative font-light text-5xl leading-[60px] uppercase text-white ${className}`}>
            <div className="relative inline-block px-2.5">
                <div className="corner left-top"></div>
                <div className="corner right-top"></div>
                {children}
                <div className="corner left-bottom"></div>
                <div className="corner right-bottom"></div>
            </div>
        </div>
    );
}