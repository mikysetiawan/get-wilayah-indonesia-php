<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Province extends CI_Model
{
    private $_table = "provinces";

    public function getAll()
    {
        return $this->db->get($this->_table)->result();
    }

    public function getById($id)
    {
        return $this->db->get_where($this->_table, ["id" => $id])->row();
    }

    public function getByProvinceNameLike($province)
    {
        $this->db->select('*')
        ->from($this->_table)
        ->like("province_name", $province);

        $query = $this->db->get();         
        return $query->row();  
    }
    
    public function save($id, $province_name)
    {        
        $this->id = $id;
        $this->province_name = $province_name;
        $this->db->insert($this->_table, $this);
    }

    public function update($id, $province_name)
    {
        $this->province_name = $province_name;
        $this->db->update($this->_table, $this, array('id' => $id));
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("id" => $id));
    }
}