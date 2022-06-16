<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Mahasiswa_model extends CI_Model {

    public function getData(){
        return $this->db->get('mahasiswas')->result_array();
    }

    public function getDataDetail($id){
        return $this->db->get_where('mahasiswas', ['id'=>$id])->result();
    }

    public function postData($data){
        return $this->db->insert('mahasiswas', $data);
    }

    public function updateData($id, $data){
        $this->db->where('id',$id);
        return $this->db->update('mahasiswas', $data);
    }
}
