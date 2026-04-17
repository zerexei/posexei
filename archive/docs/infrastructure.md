# Infrastructure & Deployment

## Cloud Architecture (AWS)
The production environment is provisioned using Terraform and runs on Amazon EKS.

- **Networking**: Custom VPC with public and private subnets across 3 Availability Zones.
- **Compute**: Amazon EKS (Managed Node Groups).
- **Managed Services**:
  - **RDS**: Managed PostgreSQL.
  - **ElastiCache**: Managed Redis.
  - **S3**: Object storage for application uploads and observability backends (Loki/Tempo).

## Infrastructure as Code (Terraform)
Located in `/infra/terraform/`, organized by modules:
- `modules/network`: VPC, Subnets, IGW, NAT Gateways.
- `modules/kubernetes`: EKS Cluster, IAM Roles.
- `modules/database`: RDS Instance and Security Groups.
- `modules/storage`: S3 Buckets for backups and logs.

## Kubernetes Deployment
Manifests are managed in `/infra/k8s/`:
- `app/`: Core application manifests (PHP-FPM, Traefik, Workers).
- `observability/`: Helm value files and OpenTelemetry Collector configs.

### Hardening
- **IAM Roles for Service Accounts (IRSA)**: Pods use specific IAM roles instead of node credentials.
- **RBAC**: Application namespace uses restricted roles.
- **Network Policies**: Default-deny policies with explicit allow-listing for inter-service communication.

## CI/CD Pipeline
GitHub Actions (`.github/workflows/deploy.yml`) automates the following:
1. Linting and Testing.
2. Docker Image Build & Push to ECR.
3. Kubernetes Deployment (Rolling Updates).

## 📖 Related Documentation
- [Architecture](architecture.md)
- [Development](development.md)
- [Observability](observability.md)
- [Tools](tools.md)