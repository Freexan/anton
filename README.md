# Simple CRUD on FlightPHP (SQLite)

Goal: minimal project with a model, SQL migration, REST API and a plain HTML page (no CSS).

## Run (Docker Compose)
1. Install Docker Desktop: https://www.docker.com/products/docker-desktop
2. Start Docker Desktop
3. Double-click `start.bat` (Windows) or run:
```
docker compose up -d
```
Migrations run automatically. Browser opens at http://localhost:8080/items

To stop:
```
docker compose down
```
or double-click `stop.bat` (Windows)

URLs:
- API: http://localhost:8080/entities
- UI:  http://localhost:8080/items

## Run without Docker
- Prereqs: PHP 8.x with SQLite extension enabled
- Windows (cmd.exe):
```
php -S localhost:8080 -t public
vendor\bin\runway.bat db:migrate
```
- Linux/macOS:
```
php -S localhost:8080 -t public
php vendor/bin/runway db:migrate
```

## Quick smoke test (Part A or B)
- PHP CLI:
```
php tools\smoke.php http://localhost:8080
```
- Docker:
```
docker compose exec anton-app php tools/smoke.php http://localhost:8080
```
The script performs list → create → detail → update → delete → detail(404) and prints OK/FAIL. In Part B it includes sku and price.

## Database
- SQLite file: `app/database.sqlite`
- Migrations:
  - `migrations/001_create_items.sql` (Part A)
  - `migrations/002_add_sku_price.sql` (Part B - adds `sku`, `price`)
- Table (Part B): `items (id, name, sku, price, quantity, note, created_at, updated_at)`

## REST API
- GET /entities — list (200)
- GET /entities/{id} — detail (200, 404)
- POST /entities — create (201, 400)
- PUT /entities/{id} — update (200, 400, 404)
- DELETE /entities/{id} — delete (204, 404)

## Validation
Part A:
- name: required, length ≤ 100
- quantity: integer ≥ 0 (non-numeric rejected)
- note: length ≤ 1000

Part B (adds):
- sku: required, length ≤ 50
- price: number ≥ 0 (rounded to 2 decimals)

Errors: JSON with `errors` or `error` (400/404).

## Frontend (UI)
- Page at `/items` (list + inline edit + delete + create form).
- Part B adds inputs/columns for SKU and Price.
- Pure HTML + JS fetch.

## Acceptance Criteria (Part A)
- Migration 001 creates table on clean DB.
- Endpoints validate input and return sensible errors.
- UI allows full CRUD from one page.
- README sufficient to run in lab.

## Extension (Part B)
- Separate migration 002 adds two new columns (`sku`, `price`).
- API & validation updated for new fields.
- UI exposes and edits new fields.
- Smoke test updated (includes sku, price).
- Tag release after merging: `v0.2-B`.

## Troubleshooting
- Port taken: use another port, e.g. `php -S localhost:8090 -t public`.
- SQLite permissions: ensure `app/` is writable.
- Missing SQLite extension: enable `pdo_sqlite`.
- Docker not available: use local run steps.
