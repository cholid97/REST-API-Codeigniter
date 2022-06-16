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

class Dosen extends RestController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Dosen_model");
    }

    public function data_get($id)
    {
        if (!$this->Dosen_model->getDataDetail($id)) return  $this->response(["message" => "Wrong Id"]);
        $result = $this->Dosen_model->getData();
        $this->response([
            'status' => 'List Data Dosen',
            'data' =>  $this->Dosen_model->getData()
        ], RestController::HTTP_OK);
    }
}
