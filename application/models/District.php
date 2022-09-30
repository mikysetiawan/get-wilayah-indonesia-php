<?php defined('BASEPATH') OR exit('No direct script access allowed');

class District extends CI_Model
{
    private $_table = "districts";

    public function getAll()
    {
        return $this->db->get($this->_table)->result();
    }

    public function getById($id)
    {
        return $this->db->get_where($this->_table, ["id" => $id])->row();
    }

    public function getByDistrictNameLike($district)
    {
        $this->db->select('*')
        ->from($this->_table)
        ->like("district_name", $district);

        $query = $this->db->get();         
        return $query->row();  
    }

    public function getByIdMoreThan($id)
    {
        $this->db->select('*')
        ->from($this->_table)
        ->where("id >=", $id);

        $query = $this->db->get();         
        return $query->result();  
    }
    
    public function save($id, $province_id, $city_id, $district_name, $postal, $postal_name)
    {
        $this->id = $id;
        $this->province_id = $province_id;
        $this->city_id = $city_id;
        $this->district_name = $district_name;
        // $this->district_postal_code = $postal;
        // $this->district_postal_name = $postal_name;
        $this->db->replace($this->_table, $this);
    }

    public function update($id, $province_id, $city_id, $district_name, $postal, $postal_name)
    {
        $this->province_id = $province_id;
        $this->city_id = $city_id;
        $this->district_name = $district_name;
        $this->district_postal_code = $postal;
        $this->district_postal_name = $postal_name;
        $this->db->update($this->_table, $this, array('id' => $id));
    }

    public function delete($id)
    {
        return $this->db->delete($this->_table, array("id" => $id));
    }
}