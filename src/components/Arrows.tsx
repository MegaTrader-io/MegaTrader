const ArrowUpWithCircle = <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                               xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0_3531_727" maskUnits="userSpaceOnUse" x="0" y="0" width="24"
          height="24">
        <rect width="24" height="24" fill="#D9D9D9"/>
    </mask>
    <g mask="url(#mask0_3531_727)">
        <path
            d="M11 16H13V11.8L14.6 13.4L16 12L12 8L8 12L9.4 13.4L11 11.8V16ZM12 22C10.6167 22 9.31667 21.7375 8.1 21.2125C6.88333 20.6875 5.825 19.975 4.925 19.075C4.025 18.175 3.3125 17.1167 2.7875 15.9C2.2625 14.6833 2 13.3833 2 12C2 10.6167 2.2625 9.31667 2.7875 8.1C3.3125 6.88333 4.025 5.825 4.925 4.925C5.825 4.025 6.88333 3.3125 8.1 2.7875C9.31667 2.2625 10.6167 2 12 2C13.3833 2 14.6833 2.2625 15.9 2.7875C17.1167 3.3125 18.175 4.025 19.075 4.925C19.975 5.825 20.6875 6.88333 21.2125 8.1C21.7375 9.31667 22 10.6167 22 12C22 13.3833 21.7375 14.6833 21.2125 15.9C20.6875 17.1167 19.975 18.175 19.075 19.075C18.175 19.975 17.1167 20.6875 15.9 21.2125C14.6833 21.7375 13.3833 22 12 22Z"
            fill="#14B8A6"/>
    </g>
</svg>

const ArrowUpWithoutCircle = (className?: string) => (
    <svg className={className} width="24" height="24" viewBox="0 0 24 24" fill="none"
         xmlns="http://www.w3.org/2000/svg">
        <mask id="mask0_2604_1333" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
              height="24">
            <rect width="24" height="24" fill="#D9D9D9"/>
        </mask>
        <g mask="url(#mask0_2604_1333)">
            <path d="M11 18V8.8L7.4 12.4L6 11L12 5L18 11L16.6 12.4L13 8.8V18H11Z" fill="currentColor"/>
        </g>
    </svg>)

export function ArrowUp({circle = true, className}: { circle?: boolean, className?: string | undefined }) {
    return circle ? ArrowUpWithCircle : ArrowUpWithoutCircle(className)
}

export function ArrowDown({circle = true, className}: { circle?: boolean, className?: string | undefined }) {
    if (circle) {
        return <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <mask id="mask0_3531_831" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
                  height="24">
                <rect x="24" y="24" width="24" height="24" transform="rotate(180 24 24)" fill="#D9D9D9"/>
            </mask>
            <g mask="url(#mask0_3531_831)">
                <path
                    d="M13 8L11 8L11 12.2L9.4 10.6L8 12L12 16L16 12L14.6 10.6L13 12.2L13 8ZM12 2C13.3833 2 14.6833 2.2625 15.9 2.7875C17.1167 3.3125 18.175 4.025 19.075 4.925C19.975 5.825 20.6875 6.88333 21.2125 8.1C21.7375 9.31667 22 10.6167 22 12C22 13.3833 21.7375 14.6833 21.2125 15.9C20.6875 17.1167 19.975 18.175 19.075 19.075C18.175 19.975 17.1167 20.6875 15.9 21.2125C14.6833 21.7375 13.3833 22 12 22C10.6167 22 9.31667 21.7375 8.1 21.2125C6.88333 20.6875 5.825 19.975 4.925 19.075C4.025 18.175 3.3125 17.1167 2.7875 15.9C2.2625 14.6833 2 13.3833 2 12C2 10.6167 2.2625 9.31667 2.7875 8.1C3.3125 6.88333 4.025 5.825 4.925 4.925C5.825 4.025 6.88333 3.3125 8.1 2.7875C9.31666 2.2625 10.6167 2 12 2Z"
                    fill="#F43F5E"/>
            </g>
        </svg>
    }

    return <svg className={className} width="24" height="24" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg">
        <mask id="mask0_2604_1323" style={{maskType: 'alpha'}} maskUnits="userSpaceOnUse" x="0" y="0" width="24"
              height="24">
            <rect width="24" height="24" fill="currentColor"/>
        </mask>
        <g mask="url(#mask0_2604_1323)">
            <path d="M12 18L6 12L7.4 10.6L11 14.2V5H13V14.2L16.6 10.6L18 12L12 18Z" fill="currentColor"/>
        </g>
    </svg>
}