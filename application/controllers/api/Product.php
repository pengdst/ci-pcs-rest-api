<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/libraries/Firebase/JWT/JWT.php';

use Restserver\Libraries\REST_Controller;
use Firebase\JWT\JWT;

class Product extends REST_Controller
{

	private $secretKey = "psho";

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('ProductModel', 'Product');
	}

	public function index_get($id = null)
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);
		$data = $id === null ? $this->Product->all() : $this->Product->getById($id);

		if ($data === null) {
			return $this->response([
				'success' => false,
				'message' => "Data tidak ditemukan"
			], REST_Controller::HTTP_NOT_FOUND);
		}

		return $this->response([
			'success' => true,
			'message' => "Data berhasil ditampilkan",
			'data' => $data
		], REST_Controller::HTTP_OK);
	}

	public function index_post()
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);
		if ($this->post('admin_id') == null) {
			return $this->response([
				'success' => false,
				'message' => "Field admin_id harus diisi",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		if ($this->post('nama') == null) {
			return $this->response([
				'success' => false,
				'message' => "Field nama harus diisi",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		if ($this->post('harga') == null) {
			return $this->response([
				'success' => false,
				'message' => "Field harga harus diisi",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		if (!is_numeric($this->post('harga'))) {
			return $this->response([
				'success' => false,
				'message' => "Field harga harus numeric",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		if ($this->post('stok') == null) {
			return $this->response([
				'success' => false,
				'message' => "Field stok harus diisi",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		if (!is_numeric($this->post('stok'))) {
			return $this->response([
				'success' => false,
				'message' => "Field stok harus numeric",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}

		$params = [
			'admin_id' => $this->post('admin_id'),
			'nama' => $this->post('nama'),
			'harga' => $this->post('harga'),
			'stok' => $this->post('stok'),
		];
		if (($id = $this->Product->create($params)) === null) {
			return $this->response([
				'success' => false,
				'message' => "Insert Data Gagal",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		$data = $data = $this->Product->getById($id);

		return $this->response([
			'success' => true,
			'message' => "Data Product berhasil ditambahkan",
			'data' => $data
		], REST_Controller::HTTP_OK);
	}

	public function index_put($id)
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);
		$params = [];
		if ($this->put('admin_id') != null) $params['admin_id'] = $this->put('admin_id');
		if ($this->put('nama') != null) $params['nama'] = $this->put('nama');
		if ($this->put('harga') != null) {
			if (!is_numeric($this->put('harga'))) {
				return $this->response([
					'success' => false,
					'message' => "Field harga harus numeric",
				], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
			}
			$params['harga'] = $this->put('harga');
		}
		if ($this->put('stok') != null) {
			if (!is_numeric($this->put('stok'))) {
				return $this->response([
					'success' => false,
					'message' => "Field stok harus numeric",
				], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
			}
			$params['stok'] = $this->put('stok');
		}

		$data = $this->Product->update($id, $params);

		return $this->response([
			'success' => true,
			'message' => "Data Product berhasil diupdate",
			'data' => $data
		], REST_Controller::HTTP_OK);
	}

	public function index_delete($id)
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);
		if (!$this->Product->delete($id)) {
			return $this->response([
				'success' => false,
				'message' => "Data Gagal Dihapus",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}

		return $this->response([
			'success' => true,
			'message' => "Data Product berhasil dihapus",
			'data' => $this->Product->all()
		], REST_Controller::HTTP_OK);
	}

	public function checkToken()
	{
		$token = $this->input->get_request_header('Authorization');

		$errResponse = [
			'success' => false,
			'message' => "Token tidak valid",
			'error_code' => 1204
		];

		if (empty($token)) {
			$errResponse['message'] = "Token kosong!";
			return $errResponse;
		}

		$token = explode(" ", $token);
		if ($token[0] == "Bearer") {
			$token = $token[1];
		}

		try {
			JWT::decode($token, $this->secretKey, ['HS256']);
			return true;
		} catch (\Throwable $th) {
			return $errResponse;
		}
	}
}
