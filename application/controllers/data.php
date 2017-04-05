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
        //$sensor_latitude = $this->post('latitude');
        //$sensor_longitude = $this->post('longitude');
        $idrumahpompa = $this->post('id');
        $tinggiair = $this->post('ketinggian');
        $interval = 1;

        date_default_timezone_set('Asia/Jakarta');
        $currentdate = date('Y-m-d h:i:s', time());

        $data = $this->rumahpompa_model->getbyId($idrumahpompa);
        //========Mendapatkan Cuaca===========
        $url = 'http://api.wunderground.com/api/886290a3665e0779/hourly/q/';
        //$service_url = $url . $sensor_latitude . ',' . $sensor_longitude . '.json';
        $service_url = $url . $data->latitude . ',' . $data->longitude . '.json';

        $result = file_get_contents($service_url);
        $result = json_decode($result);
        //var_dump($result);

        $hourly_forecast = $result->hourly_forecast;
        $pop = $hourly_forecast[0]->pop;
        $weather = $hourly_forecast[0]->wx;
        //=====================================

        //getrumah pompa yang punya latitude dan longitude sama dengan sensor
        //$data = $this->rumahpompa_model->getbyLocation($sensor_latitude, $sensor_longitude);

        //$idrumahpompa = $data->id_rumah_pompa;
        if($this->data_model->check_existing_data($idrumahpompa)>0){
            $data = $this->data_model->getlastdata($idrumahpompa);
            /*$data_sensor = array(
                'ketinggian_air' => $tinggiair,
                'cuaca' => $weather,
                'waktu' => $currentdate,
                'chanceofrain' => $pop,
                'waktu' => $currentdate,
                'updated_at' => $currentdate);*/
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
            if($data->ketinggian_air-$tinggiair > $interval || $tinggiair-$data->ketinggian_air > $interval|| $data->cuaca != $weather || $data->chanceofrain != $pop){
                $update = $this->data_model->store($data_sensor);
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
        $data["result"] = $this->data_model->getlastdata($idrumahpompa);
        $this->response($data, 200);
    }

    function getalllastdata_get(){
        $this->load->model('data_model');
        $this->load->model('rumahpompa_model');
        $data = array();

        $rumah_pompa = $this->rumahpompa_model->getAll();
        foreach ($rumah_pompa as $row) {
            if ($this->data_model->getlastdata($row->id_rumah_pompa) != null) {
                $data[] = $this->data_model->getlastdata($row->id_rumah_pompa);
            }
            else
                $data[] = json_decode('{}');;
            
        }
        $this->response($data, 200);
    }

    function alert_post(){
        // cek sensor, if
        $this->load->model('data_model');
        $this->load->model('rumahpompa_model');
        $this->load->model('user_rumahpompa_model');
        $this->load->model('user_model');

        $idrumahpompa = $this->post('id');
        $user = $this->user_rumahpompa_model->getbyRumahpompa($idrumahpompa);
        $arrayUser = array();
        foreach($user as $row)
        {
            $token = $this->user_model->getbyUsername($row->username)->token;
            if (isset($token)){
                $arrayUser[] = $this->user_model->getbyUsername($row->username)->token;
            }

        }
        //print_r ($arrayUser);

        $rumahpompa = $this->rumahpompa_model->getbyId($idrumahpompa);
        $data["result"] = $this->data_model->getlastdata($idrumahpompa);

        $tinggi_air = $data["result"]->ketinggian_air;
        $pop = $data["result"]->chanceofrain;
        $cuaca = $data["result"]->cuaca;

        $respon["tinggi_air"] = $tinggi_air;
        $respon["chanceofrain"] = $pop;

        if ($pop>=30){
            $respon["status"] = "true";
            $this->notification($arrayUser);
        }
        elseif ($tinggi_air >= $rumahpompa->threshold_tinggi_air){
            $respon["status"] = "true";
            $this->notification($arrayUser);
        }
        else{
            $respon["status"] = "false";
        }
        $this->response($respon, 200);

    }


    function notification($users){
        $this->load->model('user_model');

        require_once __DIR__ . '/firebase.php';
        require_once __DIR__ . '/push.php';

        $firebase = new Firebase();
        $push = new Push();

        // optional payload
        $payload = array();
        $payload['team'] = 'India';
        $payload['score'] = '5.6';

        // notification title
        $title = 'Rumah Pompa Notification';

        // notification message
        $message = 'Terjadi potensi banjir, nyalakan pompa!';

        // push type - single user / topic
        $push_type = 'multiple';

        // whether to include to image or not
        $include_image = FALSE;

        $push->setTitle($title);
        $push->setMessage($message);
        if ($include_image) {
            $push->setImage('');
        } else {
            $push->setImage('');
        }
        $push->setIsBackground(FALSE);
        $push->setPayload($payload);

        $json = '';
        $response = '';

        $token = $users[0];
        //print $regId;
        if ($push_type == 'multiple') {
            $json = $push->getPush();
            $response = $firebase->sendMultiple($users, $json);
        } else if ($push_type == 'individual') {
            $json = $push->getPush();
            $regId = $token;
            $response = $firebase->send($regId, $json);
        }
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