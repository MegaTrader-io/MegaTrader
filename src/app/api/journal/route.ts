import journalData from './journalData.json';

export async function GET(request: Request) {
    const {searchParams} = new URL(request.url);
    const page = parseInt(searchParams.get('page') || '1', 10);
    const perPage = parseInt(searchParams.get('per_page') || '10', 10);

    const total = journalData.length;
    const from = (page - 1) * perPage;
    const to = Math.min(from + perPage, total);
    const currentPageData = journalData.slice(from, to);

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
            first: `/api/journal?page=1&per_page=${perPage}`,
            last: `/api/journal?page=${Math.ceil(total / perPage)}&per_page=${perPage}`,
            prev: page > 1 ? `/api/journal?page=${page - 1}&per_page=${perPage}` : null,
            next: page < Math.ceil(total / perPage) ? `/api/journal?page=${page + 1}&per_page=${perPage}` : null,
        },
    };

    return new Response(JSON.stringify(response), {
        status: 200,
        headers: {"Content-Type": "application/json"},
    });
}
