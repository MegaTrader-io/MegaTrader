import data from './payoutsData.json';

export async function GET(request: Request) {
    try {
        const {searchParams} = new URL(request.url);
        const payoutStatus = searchParams.get('payoutStatus');
        const payoutsData = data.filter(p => p.status === payoutStatus);
        const sortBy = searchParams.get('sortBy') || 'dateOfRequest';
        const direction = searchParams.get('direction') === 'asc' ? 'asc' : 'desc';
        const page = parseInt(searchParams.get('page') || '1', 10);
        const perPage = parseInt(searchParams.get('per_page') || '10', 10);

        const sortedData = [...payoutsData].sort((a, b) => {
            const valA = a[sortBy as keyof typeof a];
            const valB = b[sortBy as keyof typeof b];

            if (typeof valA === 'number' && typeof valB === 'number') {
                return direction === 'asc' ? valA - valB : valB - valA;
            }
            if (typeof valA === 'string' && typeof valB === 'string') {
                return direction === 'asc' ? valA.localeCompare(valB) : valB.localeCompare(valA);
            }
            return 0;
        });

        const total = sortedData.length;
        const from = (page - 1) * perPage;
        const to = Math.min(from + perPage, total);
        const currentPageData = sortedData.slice(from, to);

        const response = {
            data: currentPageData,
            meta: {
                current_page: page,
                from: from + 1,
                last_page: Math.ceil(total / perPage),
                per_page: perPage,
                to: to,
                total: total,
            },
            links: {
                first: `/api/payouts?page=1&per_page=${perPage}&sortBy=${sortBy}&direction=${direction}&payoutStatus=${payoutStatus}`,
                last: `/api/payouts?page=${Math.ceil(total / perPage)}&per_page=${perPage}&sortBy=${sortBy}&direction=${direction}&payoutStatus=${payoutStatus}`,
                prev: page > 1 ? `/api/payouts?page=${page - 1}&per_page=${perPage}&sortBy=${sortBy}&direction=${direction}&payoutStatus=${payoutStatus}` : null,
                next: page < Math.ceil(total / perPage) ? `/api/payouts?page=${page + 1}&per_page=${perPage}&sortBy=${sortBy}&direction=${direction}&payoutStatus=${payoutStatus}` : null,
            },
        };

        return new Response(JSON.stringify(response), {
            status: 200,
            headers: {"Content-Type": "application/json"},
        });

    } catch (error) {
        console.error("Unable to process the request:", error);
        return new Response(JSON.stringify({error: "Internal Server Error"}), {
            status: 500,
            headers: {"Content-Type": "application/json"},
        });
    }
}
