<?php defined('BASEPATH') OR exit('No direct script access allowed');

class City extends CI_Model
{
    private $_table = "cities";

    public function getAll()
    {
        return $this->db->get($this->_table)->result();
    }

    public function getById($id)
    {
        return $this->db->get_where($this->_table, ["id" => $id])->row();
    }

    public function getByCityNameLike($city)
    {
        $this->db->select('*')
        ->from($this->_table)
        ->like("city_name", $city);

        $query = $this->db->get();         
        return $query->row();  
    }
    
    public function save($id, $province_id, $city_name, $postal, $postal_name)
    {
        $this->id = $id;
        $this->province_id = $province_id;
        $this->city_name = $city_name;
        // $this->city_postal_code = $postal;
        // $this->city_postal_name = $postal_name;
        $this->db->replace($this->_table, $this);
    }

    public function update($id, $province_id, $city_name, $postal, $postal_name)
    {
        $this->province_id = $province_id;
        $this->city_name = $city_name;
        $this->city_postal_code = $postal;
        $this->city_postal_name = $postal_name;
        $this->db->update($this->_table, $this, array('id' => $id));
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("id" => $id));
    }
}