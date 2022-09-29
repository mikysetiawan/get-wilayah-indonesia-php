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
        
        // $levels = ['provinsi', 'kabupaten', 'kecamatan', 'desa'];
        
        //get province
        $this->getFromSigBPS('provinsi');

        //get city of province
        $provinsi = $this->province->getAll();
        foreach ($provinsi as $key => $value) {
            $this->getFromSigBPS('kabupaten', $value->id);
        }

        //get district of city
        $kabupaten = $this->city->getAll();
        foreach ($kabupaten as $key => $value) {
            $this->getFromSigBPS('kecamatan', $value->id);
        }

        //get village of district
        $kecamatan = $this->district->getAll();
        foreach ($kecamatan as $key => $value) {
            $this->getFromSigBPS('desa', $value->id);
        }





        /*sometimes its give error file_get_contents(): SSL: An existing connection was forcibly closed by the remote host. 
        can't load api, 
        you can run the function again to get manually*/

        //example
        // $this->getFromSigBPS('desa', 1104023);
        // $this->getFromSigBPS('kecamatan', 3306);
        // $this->getFromSigBPS('kecamatan', 7171);
	}

    function fixJSON($json) {
        $newJSON = '';

        $jsonLength = strlen($json);
        for ($i = 0; $i < $jsonLength; $i++) {
            if ($json[$i] == '"' || $json[$i] == "'") {
                $nextQuote = strpos($json, $json[$i], $i + 1);
                $quoteContent = substr($json, $i + 1, $nextQuote - $i - 1);
                $newJSON .= '"' . str_replace('"', "'", $quoteContent) . '"';
                $i = $nextQuote;
            } else {
                $newJSON .= $json[$i];
            }
        }

        return $newJSON;
    }

    function getFromSigBPS($level, $parent = null){
        $failed_count = 0;
        $api_url = "https://sig.bps.go.id/rest-bridging-pos/getwilayah?level=".$level;

        if ($parent) {
            $api_url = $api_url."&parent=".$parent;
        }

        try {
            $json = file_get_contents($api_url);
            $json = json_decode($json);
    
            if(isset($json) && $json != null){
                foreach ($json as $key => $value) {
                    $id = $value->kode_bps;
                    $name = $value->nama_bps;
                    $postal = $value->kode_pos;
                    $postal_name = $value->nama_pos;

                    switch ($level) {
                        case 'provinsi':
                            $province_temp = $this->province->getById($id);
                            if (!$province_temp) {
                                $this->province->save($id, $name);
                            } else {
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
                            $city_temp = $this->city->getById($id);
                            if (!$city_temp) {
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
                            } else {
                                echo "DUPLICATE CITY ENTRY ID =".$id." ";
                                echo "<br>";
                                echo "DUPLICATE CITY ENTRY NAME =".$name." ";
                                echo "<br>";
                                echo "with =".$city_temp->city_name." ";
                                echo "<br>";
                                echo "<br>";
                                echo "<br>";
                            }
                            break;
                        case 'kecamatan':
                            $district_temp = $this->district->getById($id);
                            if (!$district_temp) {
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
                            } else {
                                echo "DUPLICATE district ENTRY ID =".$id." ";
                                echo "<br>";
                                echo "DUPLICATE district ENTRY NAME =".$name." ";
                                echo "<br>";
                                echo "with =".$district_temp->district_name." ";
                                echo "<br>";
                                echo "<br>";
                                echo "<br>";
                            }
                            break;
                        case 'desa':
                            $village_temp = $this->village->getById($id);
                            if (!$village_temp) {
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
                            } else {
                                echo "DUPLICATE village ENTRY ID =".$id." ";
                                echo "<br>";
                                echo "DUPLICATE village ENTRY NAME =".$name." ";
                                echo "<br>";
                                echo "with =".$village_temp->village_name." ";
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
    }
}