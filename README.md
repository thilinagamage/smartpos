# SmartPOS

A web-based Point of Sale system built with Laravel 12, deployed to AWS using Terraform-managed infrastructure, Docker containers, and a fully automated GitHub Actions CI/CD pipeline.

[![Deploy SmartPOS](https://github.com/thilinagamage/smartpos/actions/workflows/deploy.yml/badge.svg)]
![CI/CD](https://github.com/thilinagamage/doctor-channelling-system/actions/workflows/ci.yml/badge.svg)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-18-blue?logo=postgresql)
![AWS](https://img.shields.io/badge/AWS-Deployed-orange?logo=amazon-aws)


<img width="1915" height="910" alt="image" src="https://github.com/user-attachments/assets/1365ad1b-8782-4ce7-aa8b-9c81e6d50e96" />


## Table of contents

- [Overview](#overview)
- [Screenshots](#screenshots)
- [Tech stack](#tech-stack)
- [Architecture](#architecture)
- [Infrastructure as Code](#infrastructure-as-code)
- [CI/CD pipeline](#cicd-pipeline)
- [Running locally](#running-locally)
- [Deploying with Docker](#deploying-with-docker)
- [Database schema](#database-schema)
- [Features](#features)
- [AWS Well-Architected alignment](#aws-well-architected-alignment)
- [Project structure](#project-structure)
- [License](#license)

## Overview

SmartPOS is a full-featured point of sale and inventory management system covering sales, purchases, stock tracking, customer and supplier management, role-based access control, and reporting. This repository contains both the application code and the complete infrastructure-as-code used to run it in production on AWS.

The goal of this project was to take a real Laravel application from source code to a live, publicly accessible deployment using practices aligned with the AWS Well-Architected Framework: infrastructure defined in Terraform, containerized application delivery, least-privilege networking, and a CI/CD pipeline that tests, builds, and deploys automatically on every push to `main`.

**Live demo:** add your domain or Elastic IP here once SSL is configured, e.g. `https://smartpos.yourdomain.com`

## Screenshots


<img width="1906" height="910" alt="image" src="https://github.com/user-attachments/assets/4e3b2cb0-f9b4-4df3-a00b-fb4a04fc4295" />

<img width="1912" height="912" alt="image" src="https://github.com/user-attachments/assets/4317a089-8dde-42d5-be0c-6ae882b61992" />

<img width="1915" height="910" alt="image" src="https://github.com/user-attachments/assets/1365ad1b-8782-4ce7-aa8b-9c81e6d50e96" />

<img width="1907" height="912" alt="image" src="https://github.com/user-attachments/assets/98674032-8c79-4156-8f13-e1ef692e8b98" />

<img width="1907" height="911" alt="image" src="https://github.com/user-attachments/assets/7f9cc126-ac4f-4003-b322-11181e19afb9" />



## Tech stack

| Layer | Technology |
|---|---|
| Application | Laravel 12, PHP 8.2+ |
| Frontend | Blade templates, Bootstrap 5, Tailwind CSS v4, Vite 7 |
| Database | PostgreSQL 16 (Amazon RDS) |
| File storage | Amazon S3 |
| Web server | nginx + php-fpm (Docker) |
| Compute | Amazon EC2 (Ubuntu 24.04 LTS) |
| Infrastructure as Code | Terraform v1.9+ |
| CI/CD | GitHub Actions → GitHub Container Registry → EC2 |
| Authentication | Session-based with a custom RBAC system |

## Architecture

```
GitHub push to main
        │
        ▼
GitHub Actions
  ├── test            → PHPUnit against a PostgreSQL service container
  ├── build-and-push  → builds Docker image, pushes to ghcr.io
  └── deploy          → SSHes into EC2, pulls image, runs migrations
        │
        ▼
EC2 instance (Docker Compose)
  ├── app container     → nginx + php-fpm
  └── worker container  → php artisan queue:work
        │
        ├──► RDS PostgreSQL (private subnet)
        └──► S3 bucket (file storage, via IAM instance role)
```

<img width="1360" height="1360" alt="architecture-diagram" src="https://github.com/user-attachments/assets/ef19c5d2-e8a4-4c59-bf1c-4b3066066bb0" />


The EC2 instance sits in a public subnet with an Elastic IP. RDS sits in a private subnet across two availability zones and only accepts connections from the EC2 security group — it has no public access. The application authenticates to S3 through an IAM instance profile attached to the EC2 instance, so no static AWS credentials are stored anywhere in the application configuration.

## Infrastructure as Code

All AWS infrastructure is defined in Terraform under `infra/`, split into focused modules:

```
infra/
├── main.tf                    provider and backend configuration
├── variables.tf                input variables
├── outputs.tf                  EC2 IP, RDS endpoint, S3 bucket name, SSH command
├── terraform.tfvars.example    template for required values
└── modules/
    ├── networking/             VPC, public + private subnets, internet gateway
    ├── security/                security groups, IAM role for EC2 → S3 access
    ├── compute/                  EC2 instance, Elastic IP, Ubuntu AMI lookup
    ├── database/                  RDS PostgreSQL subnet group and instance
    └── storage/                    S3 bucket with encryption, versioning, public access block
```

Terraform state is stored remotely in S3 rather than locally, so the deployment is reproducible and the state isn't tied to any one machine.

### Provisioning the infrastructure

```bash
cd infra
cp terraform.tfvars.example terraform.tfvars
# edit terraform.tfvars with your key pair name and database password

terraform init
terraform plan
terraform apply
```

`terraform output` afterward gives you the EC2 public IP, RDS endpoint, S3 bucket name, and a ready-to-use SSH command.


## CI/CD pipeline

The pipeline is defined in `.github/workflows/deploy.yml` and runs on every push to `main` or `develop`, and on pull requests targeting `main`.

**test** — spins up a PostgreSQL service container, installs PHP and Node dependencies, builds frontend assets, runs migrations against the test database, and executes the PHPUnit suite.

**build-and-push** — builds the Docker image from the multi-stage `Dockerfile` and pushes it to GitHub Container Registry, tagged with both `latest` and the commit SHA. Runs only on `main`.

**deploy** — connects to the EC2 instance over SSH, pulls the new image, restarts the containers with Docker Compose, and runs the post-deploy Artisan commands (`migrate --force`, `config:cache`, `route:cache`, `view:cache`, `queue:restart`).

<img width="1896" height="467" alt="image" src="https://github.com/user-attachments/assets/2b682f11-e493-4881-923c-64b82edb3fd1" />


### Required GitHub Secrets

| Secret | Purpose |
|---|---|
| `EC2_HOST` | Elastic IP of the EC2 instance |
| `EC2_USER` | SSH user, typically `ubuntu` |
| `EC2_SSH_KEY` | Private key matching the EC2 key pair |
| `GHCR_TOKEN` | GitHub Personal Access Token with `write:packages` scope |

## Running locally

```bash
git clone https://github.com/thilinagamage/smartpos.git
cd smartpos

composer install
npm install

cp .env.example .env
php artisan key:generate

# configure DB_* variables in .env for your local PostgreSQL or SQLite instance

php artisan migrate --seed
npm run dev
php artisan serve
```

Seeded login credentials (local/test environments only):

| Role | Email | Password |
|---|---|---|
| Super Admin | admin@smartpos.com | password |
| Admin | admin2@smartpos.com | password |
| Cashier | cashier@smartpos.com | password |

## Deploying with Docker

The application runs as two containers defined in `docker-compose.yml`: an `app` container running nginx and php-fpm, and a `worker` container running the Laravel queue worker, both built from the same multi-stage `Dockerfile`.

```bash
docker compose pull
docker compose up -d

docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

The `.env` file is bind-mounted into the container at runtime rather than baked into the image, so configuration can be changed on the host without rebuilding.

## Database schema

The application uses 16 tables covering roles and permissions, users, products with categories and brands, sales and sale items, purchases and purchase items, suppliers, customers, stock movements, and settings, plus the standard Laravel sessions, cache, and password reset tables. Role-based access control is implemented through a `permissions` table joined to `roles` via a pivot table, covering 38 granular permission slugs across 11 functional groups.


## Features

- Point of sale screen for processing sales with barcode/SKU lookup
- Inventory and stock management with low-stock alerts and stock adjustment history
- Purchase order management with automatic stock updates
- Customer and supplier management with purchase/sales history
- Role-based access control across 4 roles and 38 granular permissions
- Sales refunds and printable receipts
- Reporting: daily/monthly sales, profit, inventory, low stock, and warranty lookup by invoice
- CSV import/export for products, and Excel/PDF export for reports
- Barcode generation for products

## AWS Well-Architected alignment

**Security** — RDS has no public accessibility and only accepts traffic from the EC2 security group. The EC2 instance authenticates to S3 via an IAM instance role rather than static credentials. Database storage is encrypted at rest, and S3 has public access fully blocked.

**Reliability** — RDS automated backups run on a 7-day retention window, with deletion protection enabled to prevent accidental data loss. The Elastic IP keeps the public address stable across instance restarts.

**Operational excellence** — All infrastructure changes go through `terraform plan` before `apply`, giving a reviewable diff of every change. Deployments are fully automated through GitHub Actions, removing manual SSH steps from the normal release process.

**Performance efficiency** — Laravel's configuration, route, and view caches are rebuilt on every deploy. The queue worker runs in its own container so background jobs don't compete with web request handling.

**Cost optimization** — The deployment uses burstable `t3.small` and `db.t3.micro` instance classes appropriate for a low-traffic POS workload, and avoids a NAT Gateway by keeping EC2 in a public subnet with a direct internet gateway route.

## Project structure

```
smartpos/
├── app/
│   ├── Http/Controllers/    16 controllers
│   ├── Models/                14 models
│   └── Services/               SaleService, PurchaseService, StockService, ReportService
├── database/
│   ├── migrations/             16 migrations
│   └── seeders/
├── resources/views/            Blade templates
├── routes/web.php
├── Dockerfile                  multi-stage build (Node → PHP)
├── docker-compose.yml
├── .github/workflows/deploy.yml
└── infra/                      Terraform infrastructure (see above)
```


## License

This project is available for portfolio and educational purposes.
