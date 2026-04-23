FROM python:3.12-slim

WORKDIR /app
RUN pip install --no-cache-dir uv

COPY services/identity-service/pyproject.toml services/identity-service/uv.lock /app/services/identity-service/
COPY services/shared/ /app/services/shared/

WORKDIR /app/services/identity-service
RUN uv sync --frozen

COPY services/identity-service/ /app/services/identity-service/

EXPOSE 3001

CMD ["uv", "run", "opentelemetry-instrument", \
     "--traces_exporter=otlp_proto_grpc", \
     "--metrics_exporter=otlp_proto_grpc", \
     "--logs_exporter=otlp_proto_grpc", \
     "uvicorn", "main:app", "--host", "0.0.0.0", "--port", "3001"]