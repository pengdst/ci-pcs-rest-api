<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
use Firebase\JWT\JWT;

class Product extends REST_Controller {

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
    function __construct() {
        parent::__construct();
        $this->load->model('ProductModel', 'Product');
    }

	public function index_get($id=null)
	{
		$this->checkToken();
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
		if ($this->post('stok') == null) {
			return $this->response([
				'success' => false,
				'message' => "Field stok harus diisi",
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
		$this->checkToken();
		$params = [];
		if ($this->put('admin_id') != null) $params['admin_id'] = $this->put('admin_id');
		if ($this->put('nama') != null) $params['nama'] = $this->put('nama');
		if ($this->put('harga') != null) $params['harga'] = $this->put('harga');
		if ($this->put('stok') != null) $params['stok'] = $this->put('stok');

		$data = $this->Product->update($id, $params);

		return $this->response([
            'success' => true,
            'message' => "Data Product berhasil diupdate",
            'data' => $data
        ], REST_Controller::HTTP_OK);
	}

	public function index_delete($id)
	{
		$this->checkToken();
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

		if (empty($token)) {
			return;
		}

		$token = explode(" ", $token);
		if ($token[0] == "Bearer") {
			$token = $token[1];
		}

		try {
			$decoded = JWT::decode($token, $this->secretKey, ['HS256']);

			return $this->response([
				'success' => true,
				'message' => "Login Berhasil",
				'data' => [
					'token' => $decoded
				]
			], REST_Controller::HTTP_OK);
		} 
		catch(\Throwable $th) {
			return $this->response([
				'success' => true,
				'message' => "Token tidak valid",
				'error_code' => 1204
			], REST_Controller::HTTP_OK);
		}
	}
}