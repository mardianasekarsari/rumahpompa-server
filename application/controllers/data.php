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
        $fcttime = $hourly_forecast[0]->FCTTIME;
        $pop = $hourly_forecast[0]->pop;
        $weather = $hourly_forecast[0]->wx;


        //Insert data cuaca ke database
        // If ada datanya?
        //      if pop dan weather sama?
        //          update
        // Else insert
        if($this->data_model->check_existing_data($idrumahpompa)!=0){
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

        $respon["data"]["ketinggian_air"] = 0;
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
        $sensor_latitude = $this->post('latitude');
        $sensor_longitude = $this->post('longitude');

        $data = $this->rumahpompa_model->getbyLocation($sensor_latitude, $sensor_longitude);
        echo $data->nama_;
        //getrumah pompa yang punya latitude dan longitude sama dengan sensor


    }
}