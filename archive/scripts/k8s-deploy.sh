#!/bin/bash
set -e

# Helper script to deploy observability stack via Helm
NAMESPACE="posexei"

echo "Adding Grafana Helm repo..."
helm repo add grafana https://grafana.github.io/helm-charts
helm repo add prometheus-community https://prometheus-community.github.io/helm-charts
helm repo update

echo "Deploying Prometheus..."
helm upgrade --install prometheus prometheus-community/prometheus \
    --namespace $NAMESPACE \
    -f k8s/observability/prometheus-values.yaml

echo "Deploying Loki..."
helm upgrade --install loki grafana/loki-distributed \
    --namespace $NAMESPACE \
    -f k8s/observability/loki-values.yaml

echo "Deploying Tempo..."
helm upgrade --install tempo grafana/tempo-distributed \
    --namespace $NAMESPACE \
    -f k8s/observability/tempo-values.yaml

echo "Deploying Grafana..."
helm upgrade --install grafana grafana/grafana \
    --namespace $NAMESPACE \
    -f k8s/observability/grafana-values.yaml

echo "Deploying OpenTelemetry Collector..."
kubectl apply -f k8s/observability/otel-collector.yaml
