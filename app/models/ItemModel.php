<?php

namespace app\models;

use flight\Engine;
use flight\util\Collection;

class ItemModel
{
	protected Engine $app;

	public function __construct(Engine $app)
	{
		$this->app = $app;
	}

	public function all(): array
	{
		$rows = $this->app->db()->fetchAll('SELECT id, name, sku, price, quantity, note, created_at, updated_at FROM items ORDER BY id DESC');
		return array_map(function($r){ return ($r instanceof Collection) ? $r->getData() : (array)$r; }, $rows);
	}

	public function find(int $id): ?array
	{
		$row = $this->app->db()->fetchRow('SELECT id, name, sku, price, quantity, note, created_at, updated_at FROM items WHERE id = ?', [ $id ]);
		if ($row instanceof Collection) { return $row->getData(); }
		return $row ?: null;
	}

	public function create(array $data): array
	{
		$now = gmdate('c');
		$this->app->db()->runQuery(
			'INSERT INTO items (name, sku, price, quantity, note, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
			[ $data['name'], $data['sku'], (float)$data['price'], (int)$data['quantity'], $data['note'], $now, $now ]
		);
		$id = (int) $this->app->db()->lastInsertId();
		return $this->find($id) ?? [];
	}

	public function update(int $id, array $data): ?array
	{
		$now = gmdate('c');
		$this->app->db()->runQuery(
			'UPDATE items SET name = ?, sku = ?, price = ?, quantity = ?, note = ?, updated_at = ? WHERE id = ?',
			[ $data['name'], $data['sku'], (float)$data['price'], (int)$data['quantity'], $data['note'], $now, $id ]
		);
		return $this->find($id);
	}

	public function delete(int $id): bool
	{
		$this->app->db()->runQuery('DELETE FROM items WHERE id = ?', [ $id ]);
		return true;
	}
}
