# DevOps Shop

Aplikacja e-commerce napisana w czystym PHP, rozwijana jako projekt portfolio łączący programowanie backendowe z praktycznymi zagadnieniami DevOps.

Projekt obejmuje konteneryzację aplikacji, bazę MySQL, reverse proxy Nginx, testy automatyczne oraz monitoring oparty o Prometheus i Grafanę.

## 🚀 Technologie

### Application

* PHP 8.3
* MySQL 8.0
* PDO
* Composer
* PHPUnit
* HTML / CSS / JavaScript

### DevOps & Infrastructure

* Docker
* Docker Compose
* Nginx
* PHP-FPM
* Prometheus
* Grafana
* cAdvisor
* Node Exporter
* Git / GitHub

## 🏗️ Architecture

Aplikacja działa w środowisku Docker Compose:

```text
                    ┌─────────────────┐
                    │     Browser     │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │      Nginx      │
                    │      :8080      │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │    PHP-FPM      │
                    │      PHP 8.3     │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │      MySQL      │
                    │      :3306      │
                    └─────────────────┘


      ┌─────────────────────────────────────────┐
      │             Monitoring                  │
      │                                         │
      │  Node Exporter ──┐                      │
      │                  ├──► Prometheus ─► Grafana
      │  cAdvisor ───────┘                      │
      │                                         │
      │  Shop /metrics.php ─► Prometheus        │
      └─────────────────────────────────────────┘
```

## 📦 Docker Services

| Service         | Description                |   Port |
| --------------- | -------------------------- | -----: |
| `nginx`         | Web server / reverse proxy | `8080` |
| `php`           | PHP-FPM application        |      - |
| `db`            | MySQL database             | `3306` |
| `prometheus`    | Metrics collection         | `9090` |
| `grafana`       | Metrics visualization      | `3000` |
| `cadvisor`      | Container monitoring       | `8081` |
| `node-exporter` | Host metrics               | `9100` |

## 🛒 Application

The shop provides the basic functionality of an e-commerce application:

* product browsing
* product details
* categories
* shopping cart
* user registration and authentication
* customer account
* order checkout
* order history
* administration panel
* product management
* category management
* order management
* user management
* CSRF protection
* session handling
* application logging

The application uses a simple custom PHP architecture with controllers, models, services and routing instead of a full-stack framework.

### Project structure

```text
devops-shop/
│
├── config/
│   ├── config.php
│   └── database.php
│
├── database/
│   └── shopstack.sql
│
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── Dockerfile
│
├── public/
│   ├── index.php
│   └── metrics.php
│
├── src/
│   ├── Controllers/
│   ├── Core/
│   ├── Helpers/
│   ├── Middleware/
│   ├── Models/
│   └── Services/
│
├── storage/
│   └── logs/
│
├── templates/
│
├── tests/
│   ├── Integration/
│   ├── Support/
│   └── Unit/
│
├── prometheus/
│   └── prometheus.yml
│
├── docker-compose.yml
├── composer.json
└── README.md
```

## 🧪 Testing

The project uses PHPUnit for automated testing.

Current test suite:

```text
23 tests
55 assertions
```

Run the tests with:

```bash
composer test
```

To generate a coverage report:

```bash
composer test:coverage
```

> Code coverage requires a PHP coverage driver such as Xdebug or PCOV.

Tests are divided into:

* **Unit tests** — isolated application logic
* **Integration tests** — interaction with the database
* **Support classes** — test database configuration and bootstrap

## 🐳 Running the project

### Requirements

* Docker
* Docker Compose
* Git

Clone the repository:

```bash
git clone https://github.com/Damianekk-php/devops-shop.git
cd devops-shop
```

Create the environment configuration based on the project's `.env` configuration.

For the Docker environment, the database connection uses the Docker service name:

```env
DB_HOST=db
DB_PORT=3306
DB_DATABASE=shopstack
DB_USERNAME=root
DB_PASSWORD=root_password
```

Start the containers:

```bash
docker compose up -d --build
```

Check container status:

```bash
docker compose ps
```

The application will be available at:

```text
http://localhost:8080
```

## ❤️ Health Check

The application exposes a health endpoint:

```text
http://localhost:8080/health
```

Example response:

```json
{
    "status": "ok",
    "database": "ok"
}
```

The endpoint verifies that the application can communicate with the MySQL database.

HTTP `200` indicates a healthy application/database connection.

## 📊 Monitoring

The project includes a monitoring stack based on Prometheus and Grafana.

### Prometheus

Prometheus collects metrics from:

* Prometheus itself
* cAdvisor
* Node Exporter
* the ShopStack application

Prometheus:

```text
http://localhost:9090
```

### Grafana

Grafana is used to visualize collected metrics:

```text
http://localhost:3000
```

Prometheus is configured as the Grafana data source.

### cAdvisor

cAdvisor provides container-level metrics such as:

* CPU usage
* memory usage
* container activity

Available at:

```text
http://localhost:8081
```

### Node Exporter

Node Exporter exposes system-level metrics:

```text
http://localhost:9100
```

## 📈 Application Metrics

The application exposes Prometheus-compatible metrics through:

```text
http://localhost:8080/metrics.php
```

Currently exposed metrics include:

```text
shop_database_status
shop_products_total
shop_orders_total
```

Example:

```text
# HELP shop_database_status Database connection status.
# TYPE shop_database_status gauge
shop_database_status 1

# HELP shop_products_total Total number of products.
# TYPE shop_products_total gauge
shop_products_total 123

# HELP shop_orders_total Total number of orders.
# TYPE shop_orders_total gauge
shop_orders_total 10
```

These metrics are scraped by Prometheus and can be visualized in Grafana.

## 🗄️ Database

The application uses MySQL 8.0 running inside Docker.

Database data is stored in a persistent Docker volume:

```text
shop-db-data
```

The database container also has a Docker healthcheck using `mysqladmin`.

PHP depends on the database becoming healthy before starting:

```yaml
condition: service_healthy
```

This prevents the application from starting before MySQL is ready.

## 🔐 Configuration

Environment-specific configuration should not be hardcoded into the application.

Important configuration values include:

```env
APP_ENV
APP_DEBUG
APP_URL

DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
```

Production credentials should never be committed to the repository.

## 🧹 Useful Docker commands

Start the environment:

```bash
docker compose up -d
```

Rebuild containers:

```bash
docker compose up -d --build
```

View running containers:

```bash
docker compose ps
```

View logs:

```bash
docker compose logs
```

View logs for a specific service:

```bash
docker compose logs php
docker compose logs nginx
docker compose logs db
```

Stop the environment:

```bash
docker compose down
```

> Do not use `docker compose down -v` unless you intentionally want to remove the Docker volumes and database data.

## 🎯 DevOps Roadmap

The project is being developed incrementally as a practical DevOps portfolio project.

### Completed

* [x] PHP e-commerce application
* [x] Automated PHPUnit tests
* [x] Dockerized PHP-FPM
* [x] Dockerized Nginx
* [x] Dockerized MySQL
* [x] Persistent database volume
* [x] MySQL Docker healthcheck
* [x] Application health endpoint
* [x] cAdvisor
* [x] Node Exporter
* [x] Prometheus
* [x] Grafana
* [x] Custom application metrics

### Planned

* [ ] Centralized logging with Loki
* [ ] Alerting with Alertmanager
* [ ] GitHub Actions CI
* [ ] Automated Docker image builds
* [ ] Container registry
* [ ] VPS deployment
* [ ] HTTPS
* [ ] Production deployment workflow

## 🎓 Project Goal

The main goal of the project is to gain practical experience with the tools and concepts used in modern DevOps environments.

The project focuses on:

* containerization
* service orchestration with Docker Compose
* CI/CD
* automated testing
* monitoring
* logging
* application health checks
* infrastructure
* deployment automation

Rather than using a ready-made DevOps template, the infrastructure is being built incrementally around a real PHP application.

## 👨‍💻 Author

**Damian**

GitHub:

https://github.com/Damianekk-php
