# Posexei Platform: Tools & Documentation Guide

This guide provides an overview of all the tools included in the Posexei platform and how to use them effectively for development, monitoring, and deployment.

## 🛠 1. Development Tools

### Laravel Sail (Docker Environment)
The primary environment for local development.
- **Start**: `./vendor/bin/sail up -d`
- **Stop**: `./vendor/bin/sail down`
- **Artisan**: `./vendor/bin/sail artisan [command]`
- **Composer**: `./vendor/bin/sail composer [command]`

### Traefik (Edge Proxy & Dashboard)
Traefik handles local routing via `[service].localhost`.
- **Dashboard**: [http://localhost:8080](http://localhost:8080)
- **Purpose**: View active routes, services, and middlewares.

### pgAdmin (Database Management)
Interface for managing the PostgreSQL database.
- **URL**: [http://pgadmin.localhost](http://pgadmin.localhost)
- **Login**: `admin@example.com` / `admin`
- **Connection**: Use `pgsql` as the host.

### Mailpit (Email Testing)
Captures all outgoing emails during development.
- **Dashboard**: [http://localhost:8025](http://localhost:8025)

### Laravel Telescope
Debugging assistant for local requests, exceptions, and jobs.
- **URL**: [http://app.localhost/telescope](http://app.localhost/telescope)

### Laravel Pulse
Real-time monitoring for your application's health.
- **URL**: [http://app.localhost/pulse](http://app.localhost/pulse)

---

## 📊 2. Observability Stack (LGTM)

The observability stack is pre-configured and ready to use.

### Grafana (Visualization)
- **URL**: [http://grafana.localhost](http://grafana.localhost)
- **Credentials**: `admin` / `admin`
- **Features**: 
  - Trace exploration via **Tempo**.
  - Log querying via **Loki**.
  - Metrics dashboards via **Prometheus**.

### Prometheus (Metrics Engine)
- **URL**: [http://prometheus.localhost](http://prometheus.localhost)
- **Purpose**: Direct querying of time-series metrics.

### Alertmanager (Alerting)
- **URL**: [http://alertmanager.localhost](http://alertmanager.localhost)
- **Purpose**: Manage and silence alerts (CPU, Memory, DB).

---

## 🚀 3. Deployment & Cloud Tools

### Terraform (Infrastructure as Code)
Located in the `terraform/` directory.
- `terraform init`: Initialize providers.
- `terraform plan`: Preview infrastructure changes.
- `terraform apply`: Deploy to AWS.

### Kubernetes (EKS)
Managed via `kubectl` and the manifests in `k8s/`.
- `kubectl get pods -n posexei`: View application status.
- `kubectl logs -f [pod-name] -n posexei`: Tail logs.

### GitHub Actions (CI/CD)
Automatic deployments are triggered by pushes to the `main` branch.
- **Workflows**: Located in `.github/workflows/`.
- **Secrets**: Requires `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY` to be set in GitHub.

---

## 🌐 4. External Access

### ngrok (Tunneling)
Exposes your local environment to the internet (required for Webhooks).
- **URL**: [http://localhost:4040](http://localhost:4040) (Inspector)
- **Setup**: Set your `NGROK_URL` and `NGROK_AUTHTOKEN` in `.env`.

---

## 📖 Related Documentation
- [Observability Guide](file:///home/angelo/projects/posexei/OBSERVABILITY_README.md)
- [Production Walkthrough](file:///home/angelo/.gemini/antigravity/brain/84843596-394f-4703-bdf6-c556b36d2209/walkthrough.md)
- [Technical Audit Report](file:///home/angelo/.gemini/antigravity/brain/84843596-394f-4703-bdf6-c556b36d2209/audit_report.md)
