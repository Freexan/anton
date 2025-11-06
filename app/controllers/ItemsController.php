<?php

namespace app\controllers;

use app\models\ItemModel;
use flight\Engine;

class ItemsController
{
	protected Engine $app;
	protected ItemModel $model;
	private const ERR_NOT_FOUND = 'Not found';

	public function __construct(Engine $app)
	{
		$this->app = $app;
		$this->model = new ItemModel($app);
	}

	public function page()
	{
		$this->app->render('items', [ 'nonce' => $this->app->get('csp_nonce') ]);
	}

	public function index()
	{
		$this->app->json($this->model->all());
	}

	public function show($id)
	{
		$item = $this->model->find((int)$id);
		if (!$item) {
			$this->app->json(['error' => self::ERR_NOT_FOUND], 404);
			return;
		}
		$this->app->json($item);
	}

	public function create()
	{
		$data = $this->input();
		$errors = $this->validate($data);
		if ($errors) {
			$this->app->json(['errors' => $errors], 400);
			return;
		}
		$item = $this->model->create([
			'name' => trim($data['name']),
			'sku' => trim($data['sku']),
			'price' => round((float)$data['price'], 2),
			'quantity' => (int)$data['quantity'],
			'note' => isset($data['note']) ? trim($data['note']) : ''
		]);
		// Set Location header for the created resource
		$this->app->response()->header('Location', '/entities/' . $item['id']);
		$this->app->json($item, 201);
	}

	public function update($id)
	{
		$id = (int)$id;
		if (!$this->model->find($id)) {
			$this->app->json(['error' => self::ERR_NOT_FOUND], 404);
			return;
		}
		$data = $this->input();
		$errors = $this->validate($data);
		if ($errors) {
			$this->app->json(['errors' => $errors], 400);
			return;
		}
		$item = $this->model->update($id, [
			'name' => trim($data['name']),
			'sku' => trim($data['sku']),
			'price' => round((float)$data['price'], 2),
			'quantity' => (int)$data['quantity'],
			'note' => isset($data['note']) ? trim($data['note']) : ''
		]);
		$this->app->json($item);
	}

	public function delete($id)
	{
		$id = (int)$id;
		if (!$this->model->find($id)) {
			$this->app->json(['error' => self::ERR_NOT_FOUND], 404);
			return;
		}
		$this->model->delete($id);
		$this->app->halt(204);
	}

	protected function input(): array
	{
		$raw = $this->app->request()->getBody();
		if ($raw) {
			$decoded = json_decode($raw, true);
			if (is_array($decoded)) {
				return $decoded;
			}
		}
		return $this->app->request()->data->getData();
	}

	protected function validate(array $data): array
	{
		$errors = [];
		$name = isset($data['name']) ? trim((string)$data['name']) : '';
		$sku = isset($data['sku']) ? trim((string)$data['sku']) : '';
		$priceRaw = $data['price'] ?? null;
		$qtyRaw = $data['quantity'] ?? null;
		$note = isset($data['note']) ? trim((string)$data['note']) : '';

		if ($name === '' || strlen($name) > 100) {
			$errors['name'] = 'name required, max 100';
		}
		if ($sku === '' || strlen($sku) > 50) {
			$errors['sku'] = 'sku required, max 50';
		}
		if ($priceRaw === null || $priceRaw === '' || !is_numeric($priceRaw) || (float)$priceRaw < 0) {
			$errors['price'] = 'price must be number >= 0';
		}
		// quantity must be provided and be an integer >= 0 (reject non-numeric inputs)
		if ($qtyRaw === null || $qtyRaw === '' || !is_numeric($qtyRaw) || (int)$qtyRaw < 0 || (string)(int)$qtyRaw !== (string)trim((string)$qtyRaw)) {
			$errors['quantity'] = 'quantity must be integer >= 0';
		}
		if (strlen($note) > 1000) {
			$errors['note'] = 'note too long';
		}
		return $errors;
	}
}
