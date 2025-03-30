export async function POST(request: Request) {
    try {
        const body = await request.json() as { walletAddress?: string, network?: string };

        if (!body.walletAddress || !body.network) {
            return new Response(
                JSON.stringify({success: false, error: "walletAddress and network are required"}),
                {status: 400, headers: {"Content-Type": "application/json"}}
            );
        }

        const {walletAddress, network} = body;

        const API_KEY = process.env.CHECK_CRYPTO_API_KEY;
        if (!API_KEY) {
            return new Response(
                JSON.stringify({success: false, error: "API Key is missing on the server"}),
                {status: 500, headers: {"Content-Type": "application/json"}}
            );
        }

        const headers = new Headers({
            "X-Api-Key": API_KEY,
            "Content-Type": "application/json",
        });

        const response = await fetch("https://api.checkcryptoaddress.com/wallet-checks", {
            method: "POST",
            headers,
            body: JSON.stringify({address: walletAddress, network}),
        });

        if (!response.ok) {
            return new Response(
                JSON.stringify({success: false, error: "External API request failed", status: response.status}),
                {status: response.status, headers: {"Content-Type": "application/json"}}
            );
        }

        const data = await response.json();

        return new Response(JSON.stringify({success: true, data}), {
            status: 200,
            headers: {"Content-Type": "application/json"},
        });

    } catch (e) {
        console.error("API error:", e);

        return new Response(
            JSON.stringify({success: false, error: e instanceof Error ? e.message : "Unknown error occurred"}),
            {status: 500, headers: {"Content-Type": "application/json"}}
        );
    }
}
