FROM python:3.12-slim

WORKDIR /app

RUN pip install --no-cache-dir uv

COPY pyproject.toml uv.lock ./

RUN uv sync --frozen

COPY . .

EXPOSE 3001

CMD ["uv", "run", "uvicorn", "main:app", "--host", "0.0.0.0", "--port", "3001"]