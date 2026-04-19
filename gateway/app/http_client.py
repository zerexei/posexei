import httpx

client = httpx.AsyncClient(timeout=10.0)

async def fetch(method: str, url: str, **kwargs):
    resp = await client.request(method, url, **kwargs)
    resp.raise_for_status()
    return resp.json()