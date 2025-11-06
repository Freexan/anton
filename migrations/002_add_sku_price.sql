-- Part B migration: add two new columns to items
-- Execute after 001_create_items.sql
ALTER TABLE items ADD COLUMN sku TEXT NOT NULL DEFAULT ''; -- product/service code
ALTER TABLE items ADD COLUMN price REAL NOT NULL DEFAULT 0; -- price amount >= 0

