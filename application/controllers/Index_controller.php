<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index_controller extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->model("province");
        $this->load->model("city");
        $this->load->model("district");
        $this->load->model("village");
	}
	
	public function index()
	{
        ini_set('max_execution_time', '0');
        
        echo "--------------------- RUNNING -------------------";
        echo "<br>";
        echo "<br>";
        // $levels = ['provinsi', 'kabupaten', 'kecamatan', 'desa'];
        
        //get province
        echo "--------------------- GET PROVINCE -------------------";
        echo "<br>";
        echo "<br>";
        $this->getFromSigBPS('provinsi');

        $total_city = 0;
        //get city of province
        $provinsi = $this->province->getAll();
        foreach ($provinsi as $key => $value) {
            echo "--------------------- GET CITY FROM PROVINCE ". $value->province_name ." -------------------";
            echo "<br>";
            echo "<br>";
            $total_city += $this->getFromSigBPS('kabupaten', $value->id);
        }

        echo "TOTAL CITY = ".$total_city;
        echo "<br>";

        $total_district = 0;
        //get district of city
        $kabupaten = $this->city->getAll();
        foreach ($kabupaten as $key => $value) {
            echo "--------------------- GET DISTRICT FROM CITY ". $value->city_name ." -------------------";
            echo "<br>";
            echo "<br>";
            $total_district += $this->getFromSigBPS('kecamatan', $value->id);
        }

        echo "TOTAL DISTRICT = ".$total_district;
        echo "<br>";

        $total_village = 0;
        //get village of district
        $kecamatan = $this->district->getAll();
        // $kecamatan = $this->district->getByIdMoreThan(2171010);
        foreach ($kecamatan as $key => $value) {
            echo "--------------------- GET VILLAGE FROM DISTRICT ". $value->district_name ." -------------------";
            echo "<br>";
            echo "<br>";
            $total_village += $this->getFromSigBPS('desa', $value->id);
        }

        echo "TOTAL VILLAGE = ".$total_village;
        echo "<br>";
	}

    function getFromSigBPS($level, $parent = null){
        $failed_count = 0;
        $count = 0;

        //Run this first to get all complete list
        // $api_url = "https://sig.bps.go.id/rest-bridging/getwilayah?level=".$level;

        //then if you need to get pos, run this to update with pos data
        $api_url = "https://sig.bps.go.id/rest-bridging-pos/getwilayah?level=".$level;

        //then run without pos to get more region that doesn't register on pos API
        

        if ($parent) {
            $api_url = $api_url."&parent=".$parent;
        }

        echo "RUNNING URL = ".$api_url;
        echo "<br>";
        echo "=======================================================";
        echo "<br>";
        echo "<br>";
        try {
            $curl = curl_init(); 
            curl_setopt($curl, CURLOPT_URL, $api_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
            $output = curl_exec($curl); 
            $retry = 0;

            //retry if failed
            if(curl_errno($curl)){
                echo "ERROR = ".curl_errno($curl);
                echo "<br>";
                echo "<br>";
                echo "<br>";
            }

            while((curl_errno($curl) == 28 || curl_errno($curl) == 35) && $retry < 3){
                $response = curl_exec($curl);
                echo "======================== RETRYING ==================";
                echo "<br>";
                $retry++;
            }

            curl_close($curl); 

            $json = json_decode($output);
    
            if(isset($json) && $json != null){
                foreach ($json as $key => $value) {
                    $id = $value->kode_bps;
                    $name = $value->nama_bps;
                    $postal = null;
                    $postal_name = null;

                    if (isset($value->kode_pos)) {
                        $postal = $value->kode_pos;
                    }
                    
                    if (isset($value->nama_pos)) {
                        $postal_name = $value->nama_pos;
                    }

                    $count++;
                    switch ($level) {
                        case 'provinsi':
                            $province_temp = $this->province->getById($id);
                            if (!$province_temp) {
                                $this->province->save($id, $name);
                            } else if ($name != $province_temp->province_name) {
                                echo "DUPLICATE PROVINCE ENTRY ID =".$id." ";
                                echo "<br>";
                                echo "DUPLICATE PROVINCE ENTRY NAME =".$name." ";
                                echo "<br>";
                                echo "with =".$province_temp->province_name." ";
                                echo "<br>";
                                echo "<br>";
                                echo "<br>";
                            }
                            break;
                        case 'kabupaten':
                            $province_temp = $this->province->getById($parent);
                            if ($province_temp) {
                                $this->city->save($id, $province_temp->id, $name, $postal, $postal_name);
                            } else {
                                echo "PROVINCE NOT FOUND ON CITY ID =".$id." ";
                                echo "<br>";
                                echo "PROVINCE NOT FOUND ON CITY NAME =".$name." ";
                                echo "<br>";
                                echo "<br>";
                                echo "<br>";
                            }
                            break;
                        case 'kecamatan':
                            $city_temp = $this->city->getById($parent);
                            if ($city_temp) {
                                $this->district->save($id, $city_temp->province_id, $city_temp->id, $name, $postal, $postal_name);
                            } else {
                                echo "city NOT FOUND ON district ID =".$id." ";
                                echo "<br>";
                                echo "city NOT FOUND ON district NAME =".$name." ";
                                echo "<br>";
                                echo "<br>";
                                echo "<br>";
                            }
                            break;
                        case 'desa':
                            $district_temp = $this->district->getById($parent);
                            if ($district_temp) {
                                $this->village->save($id, $district_temp->province_id, $district_temp->city_id, $district_temp->id, $name, $postal, $postal_name);
                            } else {
                                echo "district NOT FOUND ON village ID =".$id." ";
                                echo "<br>";
                                echo "district NOT FOUND ON village NAME =".$name." ";
                                echo "<br>";
                                echo "<br>";
                                echo "<br>";
                            }
                            break;
                    }
                }
            }else{
                echo "FAILED GET = ".$api_url." ";
                echo "<br>";
                echo "<br>";

                $failed_count++;
            }
        } catch (\Throwable $th) {
            print_r($th);
            echo "FAILED GET = ".$api_url." ";
            echo "<br>";
            echo "<br>";

            $failed_count++;
        } 

        echo "TOTAL GET DATA = ".$count;
        echo "<br>";
        return $count;
    }
}
