from fastapi import FastAPI
from routes.v1.identity import identity_router

app = FastAPI()

app.include_router(identity_router)


@app.get("/")
def read_root():
    return {"Hello": "Gateway3"}


@app.get("/health")
def read_health():
    return {"status": "ok"}
