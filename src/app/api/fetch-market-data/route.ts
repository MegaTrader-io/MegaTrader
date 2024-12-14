// import NodeCache from "node-cache";
//
// const cache = new NodeCache({stdTTL: 600});

export async function GET() {
    return new Response(JSON.stringify({megatrader: 123}), {status: 200});
}