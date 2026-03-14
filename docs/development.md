# Local Development Guide

## Prerequisites
- Docker & Docker Compose
- PHP 8.3+ (local) & Composer (for initial setup)

## Initial Setup

1. **Clone and Install**:
```bash
git clone https://github.com/zerexei/posexei.git
cd posexei
composer install
npm install
```

2. **Environment Configuration**:
Copy `.env.example` to `.env` and configure your credentials.
```bash
cp .env.example .env
php artisan key:generate
```

3. **Start the Environment**:
Posexei uses Laravel Sail for containerized development.
```bash
./vendor/bin/sail up -d
```

4. **Database Migrations**:
```bash
./vendor/bin/sail artisan migrate --seed
```

## Service URLs (Self-Hosted)
| Service | URL | Note |
| :--- | :--- | :--- |
| **App** | [http://app.localhost](http://app.localhost) | Main UI |
| **Traefik** | [http://localhost:8080](http://localhost:8080) | Proxy Dashboard |
| **pgAdmin** | [http://pgadmin.localhost](http://pgadmin.localhost) | Database GUI |
| **MinIO** | [http://minio.localhost](http://minio.localhost) | S3 Dashboard |
| **Grafana** | [http://grafana.localhost](http://grafana.localhost) | Observability Dashboard |

## Common Commands
- **Stop**: `./vendor/bin/sail down`
- **Tails Logs**: `./vendor/bin/sail logs -f`
- **Run Tests**: `./vendor/bin/sail test`

## Troubleshooting
- **Port Conflicts**: Ensure ports 80, 5432, and 6379 are not in use by local services.
- **Xdebug**: Enable via `SAIL_XDEBUG_MODE=develop,debug` in `.env`.
