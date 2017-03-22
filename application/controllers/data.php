<?php


require APPPATH.'/libraries/REST_Controller.php';

/**
 * Created by PhpStorm.
 * User: Mardiana
 * Date: 3/20/2017
 * Time: 8:10 PM
 */
class data extends REST_Controller
{

    function cuaca_post(){
        $this->load->model('rumahpompa_model');
        $this->load->model('data_model');

        $idrumahpompa = $this->post('id');
        date_default_timezone_set('Asia/Jakarta');
        $currentdate = date('Y-m-d h:i:s', time());

        $date = date("d");
        $hour = date("H");

        //Cari Latitude Longitude RumahPompa berdasarkan ID
        $latitude = $this->rumahpompa_model->getbyId($idrumahpompa)->latitude;
        $longitude = $this->rumahpompa_model->getbyId($idrumahpompa)->longitude;

        $url = 'http://api.wunderground.com/api/886290a3665e0779/hourly/q/';
        $service_url = $url . $latitude . ',' . $longitude . '.json';

        $result = file_get_contents($service_url);
        $result = json_decode($result);
        //var_dump($result);

        $hourly_forecast = $result->hourly_forecast;
        $pop = $hourly_forecast[0]->pop;
        $weather = $hourly_forecast[0]->wx;

        if($this->data_model->check_existing_data($idrumahpompa)>0){
            $data = $this->data_model->getbyIdrumahpompa($idrumahpompa);
            $data_cuaca = array(
                'cuaca' => $weather,
                'waktu' => $currentdate,
                'chanceofrain' => $pop,
                'updated_at' => $currentdate);
            if($data->chanceofrain != $pop || $data->cuaca != $weather){
                $update = $this->data_model->edit($idrumahpompa, $data_cuaca);
                if($update){
                    $respon["status"]= true;
                    $respon["msg"]= "Edit Berhasil";
                }else{
                    $respon["status"]= false;
                    $respon["msg"]= "Edit Gagal";
                }
            }
            else{
                $respon["status"]= true;
                $respon["msg"]= "Data Tidak Berubah";
            }
        }
        else{
            //data ketinggian air belom
            //echo $this->data_model->generateid();
            $data_cuaca = array(
                'id_data' => $this->data_model->generateid(),
                'id_rumah_pompa' => $idrumahpompa,
                'ketinggian_air' => 0,
                'cuaca' => $weather,
                'waktu' => $currentdate,
                'chanceofrain' => $pop,
                'created_at' => $currentdate,
                'updated_at' => $currentdate,
                'soft_delete' => 'false');
            $insert = $this->data_model->store($data_cuaca);
            if ($insert){
                $respon["status"] = "true";
                $respon["msg"]= "Insert Berhasil";

            }else{
                $respon["status"] = "false";
                $respon["msg"]= "Insert Gagal";
            }
        }

        $respon["data"]["ketinggian"] = $data->ketinggian_air;
        $respon["data"]["cuaca"] = $weather;
        $respon["data"]["waktu"] = $currentdate;
        $respon["data"]["chanceofrain"] = $pop;
        $this->response($respon, 200);

        //var_dump($weather);

        /*$length = count($hourly_forecast);

        for($i=0; $i<$length; $i++){
            $fcttime = $hourly_forecast[$i]->FCTTIME;
            $current_date = $fcttime->mday_padded;
            $current_hour = $fcttime->hour_padded;

            if ($current_date == $date && $current_hour == $hour){

                $pop = $hourly_forecast[$i]->pop;
                //var_dump($pop);
            }

            //var_dump($current_hour);
        }*/

    }

    function sensor_post(){
        $this->load->model('rumahpompa_model');
        $this->load->model('data_model');
        $sensor_latitude = $this->post('latitude');
        $sensor_longitude = $this->post('longitude');
        $tinggiair = $this->post('ketinggian');

        date_default_timezone_set('Asia/Jakarta');
        $currentdate = date('Y-m-d h:i:s', time());

        //========Mendapatkan Cuaca===========
        $url = 'http://api.wunderground.com/api/886290a3665e0779/hourly/q/';
        $service_url = $url . $sensor_latitude . ',' . $sensor_longitude . '.json';

        $result = file_get_contents($service_url);
        $result = json_decode($result);
        //var_dump($result);

        $hourly_forecast = $result->hourly_forecast;
        $pop = $hourly_forecast[0]->pop;
        $weather = $hourly_forecast[0]->wx;
        //=====================================

        //getrumah pompa yang punya latitude dan longitude sama dengan sensor
        $data = $this->rumahpompa_model->getbyLocation($sensor_latitude, $sensor_longitude);

        $idrumahpompa = $data->id_rumah_pompa;
        if($this->data_model->check_existing_data($idrumahpompa)>0){
            $data = $this->data_model->getbyIdrumahpompa($idrumahpompa);
            $data_sensor = array(
                'ketinggian_air' => $tinggiair,
                'cuaca' => $weather,
                'waktu' => $currentdate,
                'chanceofrain' => $pop,
                'waktu' => $currentdate,
                'updated_at' => $currentdate);
            if($data->ketinggian_air != $tinggiair || $data->cuaca != $weather || $data->chanceofrain != $pop){
                $update = $this->data_model->edit($idrumahpompa, $data_sensor);
                if($update){
                    $respon["status"]= true;
                    $respon["msg"]= "Edit Berhasil";
                }else{
                    $respon["status"]= false;
                    $respon["msg"]= "Edit Gagal";
                }
            }
            else{
                $respon["status"]= true;
                $respon["msg"]= "Data Tidak Berubah";
            }
        }
        else{
            $data_sensor = array(
                'id_data' => $this->data_model->generateid(),
                'id_rumah_pompa' => $idrumahpompa,
                'ketinggian_air' => $tinggiair,
                'cuaca' => $weather,
                'waktu' => $currentdate,
                'chanceofrain' => $pop,
                'created_at' => $currentdate,
                'updated_at' => $currentdate,
                'soft_delete' => 'false');
            $insert = $this->data_model->store($data_sensor);
            if ($insert){
                $respon["status"] = "true";
                $respon["msg"]= "Insert Berhasil";

            }else{
                $respon["status"] = "false";
                $respon["msg"]= "Insert Gagal";
            }
        }

        $respon["data"]["ketinggian_air"] = $tinggiair;
        $respon["data"]["cuaca"] = $weather;
        $respon["data"]["waktu"] = $currentdate;
        $respon["data"]["chanceofrain"] = $pop;
        $this->response($respon, 200);
        //echo $data->nama_;
    }

    function getbyId_post(){
        $this->load->model('data_model');
        $idrumahpompa = $this->post('id');
        $data["result"] = $this->data_model->getbyIdrumahpompa($idrumahpompa);
        $this->response($data, 200);
    }

    function location_get(){
        $ip  = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $url = "http://freegeoip.net/json/$ip";
        $ch  = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $data = curl_exec($ch);
        curl_close($ch);

        if ($data) {
            $location = json_decode($data);

            /*$lat = $location->latitude;
            $lon = $location->longitude;

            $sun_info = date_sun_info(time(), $lat, $lon);*/
            var_dump($location);
        }
    }
}