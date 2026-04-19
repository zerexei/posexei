from fastapi import FastAPI

app = FastAPI()


@app.get("/users")
def get_users():
    return [{"id": 1, "name": "Alice"}]


@app.get("/users/{user_id}")
def get_user(user_id: int):
    return {"id": user_id, "name": "Some User"}


@app.post("/users")
def create_user(user: dict):
    return {"message": "User created", "user": user}
