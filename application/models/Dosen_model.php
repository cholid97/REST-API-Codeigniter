<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosen_model extends CI_Model
{

    public function getData()
    {
        $this->db->select('dosen.nama as Nama Dosen,matkul.nama_matkul as Nama Mata Kuliah');
        $this->db->join('matkul', 'matkul.id = dosen.id_matkul');
        return $this->db->get('dosen')->row();
    }

    public function getDataDetail($id)
    {
        return $this->db->get_where('dosen', ['id' => $id])->result();
    }
}
