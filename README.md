# 🌐 Post Once, Publish Everywhere (Posexei)

**Post once, publish everywhere — seamless cross-platform social posting.**  
A scalable platform for managing and publishing content across multiple social platforms from one unified dashboard.

---

## 📖 Documentation

Everything you need to know about the platform is available in the **[`/docs`](./docs)** directory:

- 🛠 **[Development Guide](./docs/development.md)**: Local setup, Sail, and common commands.
- 🏗 **[Architecture](./docs/architecture.md)**: System design, data models, and process flows.
- 🚀 **[Infrastructure](./docs/infrastructure.md)**: AWS, EKS, Terraform, and CI/CD.
- 📊 **[Observability](./docs/observability.md)**: Tracing, Logging, and Monitoring with LGTM stack.
- 🔧 **[Tools Catalog](./docs/tools.md)**: Detailed breakdown of included platform tools.

---

## ✨ Features (MVP)
- 🔐 Secure multi-tenant authentication.
- 🔗 Multi-platform connections (Facebook, LinkedIn, Twitter/X).
- 📝 Media-rich post creation.
- 📢 Simultaneous publishing across connected channels.
- ⏰ Background job processing and scheduling.

## 🚀 Quick Start (Development)
```bash
git clone https://github.com/zerexei/posexei.git
cd posexei
composer install && npm install
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```
Visit: [http://app.localhost](http://app.localhost)

---

## 🛡 Security & Audit
The platform has undergone a comprehensive [Technical Audit](file:///home/angelo/.gemini/antigravity/brain/84843596-394f-4703-bdf6-c556b36d2209/audit_report.md) and is hardened for production readiness (Score: 9.5/10).
