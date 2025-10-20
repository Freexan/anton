# Simple CRUD on FlightPHP (SQLite)

Goal: minimal project with a model, SQL migration, REST API and a plain HTML page (no CSS).

## Run (Docker Compose)
1. Install Docker and Docker Compose.
2. In project root:
```
docker compose down --remove-orphans
docker compose build
docker compose up -d
```
3. Run migration (creates DB file and table):
```
docker compose exec flight php vendor/bin/runway db:migrate
```
4. Open in browser:
- API: http://localhost:8080/entities
- UI:  http://localhost:8080/items

Run without Docker (needs PHP):
```
php -S localhost:8080 -t public
php vendor/bin/runway db:migrate
```

## Database
- SQLite file: `app/database.sqlite`
- Migrations: `migrations/001_create_items.sql`
- Table: `items (id, name, quantity, note, created_at, updated_at)`

## REST API
- GET /entities — list (200)
- GET /entities/{id} — detail (200, 404)
- POST /entities — create (201, 400)
- PUT /entities/{id} — update (200, 400, 404)
- DELETE /entities/{id} — delete (204, 404)

Validation:
- name: required, length ≤ 100
- quantity: number ≥ 0
- note: length ≤ 1000

Errors: returns JSON with `errors` or `error` and proper code (400/404).

Screenshot (schematic): `public/img.png`

