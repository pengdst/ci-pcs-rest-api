<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/libraries/Firebase/JWT/JWT.php';

use Restserver\Libraries\REST_Controller;
use Firebase\JWT\JWT;

class Transaksi extends REST_Controller
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
        $this->load->model('AdminModel', 'Admin');
		$this->load->model('TransaksiModel', 'Transaksi');
	}

	public function index_get($id = null)
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);
		$data = $id === null ? $this->Transaksi->all() : $this->Transaksi->getById($id);

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
		if ($this->Admin->getById($this->post('admin_id'))==null) {
			return $this->response([
				'success' => false,
				'message' => "Admin tidak ditemukan",
			], REST_Controller::HTTP_NOT_FOUND);
		}
		if ($this->post('total') == null) {
			return $this->response([
				'success' => false,
				'message' => "Field total harus diisi",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		if (!is_numeric($this->post('total'))) {
			return $this->response([
				'success' => false,
				'message' => "Field total harus numeric",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}

		$params = [
			'admin_id' => $this->post('admin_id'),
			'total' => $this->post('total'),
			'tanggal' => date("Y-m-d H:i:s"),
		];
		if (($id = $this->Transaksi->create($params)) === null) {
			return $this->response([
				'success' => false,
				'message' => "Insert Data Gagal",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		$data = $data = $this->Transaksi->getById($id);

		return $this->response([
			'success' => true,
			'message' => "Data Transaksi berhasil ditambahkan",
			'data' => $data
		], REST_Controller::HTTP_OK);
	}

	public function index_put($id)
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);
		$params = [];
		if ($this->put('admin_id') != null) {
			if ($this->Admin->getById($this->put('admin_id'))) {
				return $this->response([
					'success' => false,
					'message' => "Admin tidak ditemukan",
				], REST_Controller::HTTP_NOT_FOUND);
			}
			$params['admin_id'] = $this->put('admin_id');
		}
		if ($this->put('total') != null) {
			if (!is_numeric($this->put('total'))) {
				return $this->response([
					'success' => false,
					'message' => "Field total harus numeric",
				], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
			}
			$params['total'] = $this->put('total');
		}

		if (empty($params)) return $this->response([
			'success' => false,
			'message' => "Tidak ada perubahan data"
		], REST_Controller::HTTP_NOT_MODIFIED);

		$params['tanggal'] = date("Y-m-d H:i:s");

		$data = $this->Transaksi->update($id, $params);

		if ($data === null) return $this->response([
			'success' => false,
			'message' => "Data tidak ditemukan"
		], REST_Controller::HTTP_NOT_FOUND);

		return $this->response([
			'success' => true,
			'message' => "Data Transaksi berhasil diupdate",
			'data' => $data
		], REST_Controller::HTTP_OK);
	}

	public function index_delete($id)
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);

		if ($this->Transaksi->getById($id) == null) return $this->response([
			'success' => false,
			'message' => "Data Tidak Ditemukan",
		], REST_Controller::HTTP_NOT_FOUND);

		if (!$this->Transaksi->delete($id)) {
			return $this->response([
				'success' => false,
				'message' => "Data Gagal Dihapus",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}

		return $this->response([
			'success' => true,
			'message' => "Data Transaksi berhasil dihapus",
			'data' => $this->Transaksi->all()
		], REST_Controller::HTTP_OK);
	}

	public function item_get($id = null)
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);
		$data = $id === null ? $this->Transaksi->all() : $this->Transaksi->getById($id);

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

	public function item_post()
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);
		if ($this->post('admin_id') == null) {
			return $this->response([
				'success' => false,
				'message' => "Field admin_id harus diisi",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		if ($this->Admin->getById($this->post('admin_id'))==null) {
			return $this->response([
				'success' => false,
				'message' => "Admin tidak ditemukan",
			], REST_Controller::HTTP_NOT_FOUND);
		}
		if ($this->post('total') == null) {
			return $this->response([
				'success' => false,
				'message' => "Field total harus diisi",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		if (!is_numeric($this->post('total'))) {
			return $this->response([
				'success' => false,
				'message' => "Field total harus numeric",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}

		$params = [
			'admin_id' => $this->post('admin_id'),
			'total' => $this->post('total'),
			'tanggal' => date("Y-m-d H:i:s"),
		];
		if (($id = $this->Transaksi->create($params)) === null) {
			return $this->response([
				'success' => false,
				'message' => "Insert Data Gagal",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}
		$data = $data = $this->Transaksi->getById($id);

		return $this->response([
			'success' => true,
			'message' => "Data Transaksi berhasil ditambahkan",
			'data' => $data
		], REST_Controller::HTTP_OK);
	}

	public function item_put($id)
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);
		$params = [];
		if ($this->put('admin_id') != null) {
			if ($this->Admin->getById($this->put('admin_id'))) {
				return $this->response([
					'success' => false,
					'message' => "Admin tidak ditemukan",
				], REST_Controller::HTTP_NOT_FOUND);
			}
			$params['admin_id'] = $this->put('admin_id');
		}
		if ($this->put('total') != null) {
			if (!is_numeric($this->put('total'))) {
				return $this->response([
					'success' => false,
					'message' => "Field total harus numeric",
				], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
			}
			$params['total'] = $this->put('total');
		}

		if (empty($params)) return $this->response([
			'success' => false,
			'message' => "Tidak ada perubahan data"
		], REST_Controller::HTTP_NOT_MODIFIED);

		$params['tanggal'] = date("Y-m-d H:i:s");

		$data = $this->Transaksi->update($id, $params);

		if ($data === null) return $this->response([
			'success' => false,
			'message' => "Data tidak ditemukan"
		], REST_Controller::HTTP_NOT_FOUND);

		return $this->response([
			'success' => true,
			'message' => "Data Transaksi berhasil diupdate",
			'data' => $data
		], REST_Controller::HTTP_OK);
	}

	public function item_delete($id)
	{
		if ($this->checkToken() !== true) return $this->response($this->checkToken(), REST_Controller::HTTP_UNAUTHORIZED);

		if ($this->Transaksi->getById($id) == null) return $this->response([
			'success' => false,
			'message' => "Data Tidak Ditemukan",
		], REST_Controller::HTTP_NOT_FOUND);

		if (!$this->Transaksi->delete($id)) {
			return $this->response([
				'success' => false,
				'message' => "Data Gagal Dihapus",
			], REST_Controller::HTTP_UNPROCESSABLE_ENTITY);
		}

		return $this->response([
			'success' => true,
			'message' => "Data Transaksi berhasil dihapus",
			'data' => $this->Transaksi->all()
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
			$errResponse['message'] = $th->getMessage();
			return $errResponse;
		}
	}
}