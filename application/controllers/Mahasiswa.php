<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';
require APPPATH . '/libraries/JWT.php';
require APPPATH . '/libraries/ExpiredException.php';
require APPPATH . '/libraries/BeforeValidException.php';
require APPPATH . '/libraries/SignatureInvalidException.php';
require APPPATH . '/libraries/JWK.php';
require APPPATH . '/libraries/Key.php';
require APPPATH . '/libraries/CachedKeySet.php';

use chriskacerguis\RestServer\RestController;

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use \Firebase\JWT\ExpiredException;

class Mahasiswa extends RestController
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("Mahasiswa_model");
	}

	public function configToken()
	{
		$cnf['exp'] = 3600000; //milisecond
		$cnf['secretkey'] = '2212336221';
		return $cnf;
	}

	public function getToken_post()
	{
		$exp = time() + 3600;
		$token = array(
			"iss" => 'apprestservice',
			"aud" => 'pengguna',
			"iat" => time(),
			"nbf" => time() + 10,
			"exp" => $exp,
			"data" => array(
				"username" => $this->input->post('username'),
				"password" => $this->input->post('password')
			)
		);

		$jwt = JWT::encode($token, $this->configToken()['secretkey'], 'HS256');
		$output = [
			'status' => 200,
			'message' => 'Berhasil login',
			"token" => $jwt,
			"expireAt" => $token['exp']
		];
		$data = array('kode' => '200', 'pesan' => 'token', 'data' => array('token' => $jwt, 'exp' => $exp));
		$this->response($data, 200);
	}

	public function authtoken()
	{
		$secret_key = $this->configToken()['secretkey'];
		$token = null;
		$authHeader = $this->input->request_headers()['Authorization'];
		$arr = explode(" ", $authHeader);
		$token = $arr[1];
		if ($token) {
			try {
				$decoded = JWT::decode($token, new Key($this->configToken()['secretkey'], 'HS256'));
				if ($decoded) {
					return 'benar';
				}
			} catch (\Exception $e) {
				$result = array('pesan' => 'Kode Signature Tidak Sesuai');
				return 'salah';
			}
		}
	}

	public function data_get()
	{
		if ($this->authtoken() == 'salah') {
			return $this->response(array('kode' => '401', 'pesan' => 'signature tidak sesuai', 'data' => []), '401');
			die();
		}

		$this->response([
			'status' => 'List Data Mahasiswa',
			'data' => $this->Mahasiswa_model->getData()
		], RestController::HTTP_OK);
	}

	public function datadetail_get($id)
	{
		$this->response([
			'status' => 'Detais Mahasiswa',
			'data' => $this->Mahasiswa_model->getDataDetail($id)
		], RestController::HTTP_OK);
	}

	public function data_post()
	{
		if ($this->authtoken() == 'salah') {
			return $this->response(array('kode' => '401', 'pesan' => 'signature tidak sesuai', 'data' => []), '401');
			die();
		}

		try {
			$this->form_validation->set_data($this->post());
			$this->form_validation->set_rules('nama', 'Nama', 'required', ['required' => 'Field Nama Wajib Diisi']);
			$this->form_validation->set_rules('alamat', 'Alamat', 'required', ['required' => 'Field Alamat Wajib Diisi']);
			$this->form_validation->set_rules('class', 'Kelas', 'required', ['required' => 'Field Kelas Wajib Diisi']);

			if (!$this->form_validation->run()) throw new Exception();

			$data = [
				'nama'    => $this->post('nama'),
				'alamat'  => $this->post('alamat'),
				'class'   => $this->post('class'),
			];

			if ($this->Mahasiswa_model->postData($data)) {
				$this->response([
					'status' => "Berhasil memasukan data Mahasiswa",
					'message' => $data,
				], RestController::HTTP_CREATED);
			} else {
				throw new Exception('Cek Kembali Format Pengiriman');
			}
		} catch (\Throwable $e) {
			$this->response([
				'status' => "Gagal Menambahkan Data",
				'message' => $this->form_validation->error_array(),
			], RestController::HTTP_BAD_REQUEST);
		}
	}

	public function data_put($id)
	{
		try {
			$this->form_validation->set_data($this->put());
			$this->form_validation->set_rules('nama', 'Nama', 'required', array('required' => 'Field Nama Wajib Diisi'));
			$this->form_validation->set_rules('alamat', 'Alamat', 'required', array('required' => 'Field Alamat Wajib Diisi'));
			$this->form_validation->set_rules('class', 'Kelas', 'required', array('required' => 'Field Kelas Wajib Diisi'));

			if (!$this->form_validation->run()) throw new Exception(validation_errors());

			$data = [
				'nama'    => $this->put('nama'),
				'alamat'  => $this->put('alamat'),
				'class'   => $this->put('class'),
			];

			if ($this->Mahasiswa_model->updateData($id, $data)) {
				$this->response([
					'status' => "Berhasil Mengupdate data Mahasiswa",
					'message' => $data,
				], RestController::HTTP_OK);
			} else {
				throw new Exception('Cek Kembali Format Pengiriman');
			}
		} catch (\Throwable $e) {
			$this->response([
				'status' => "Gagal Mengupdate Data",
				'message' => $this->form_validation->error_array(),
			], RestController::HTTP_BAD_REQUEST);
		}
	}
}
