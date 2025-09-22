<?php
class Epark_helper {

    private $company_id;
    private $ci;

    public $api_base;
    private $cookie_file = 'cookie.txt';
    private $api_key;
    private $bussiness_fromtime = '00:00:00';
    private $bussiness_totime ='23:59:59';

    public function __construct($c_id) {
        $this->ci =& get_instance();

        $this->ci->load->model('organ_model');
        $this->ci->load->model('staff_organ_model');
        $this->ci->load->model('shift_model');
    }

    public function authenticate($loginId, $loginPwd) {
        $data = [
            'data[User][login]' => $loginId,
            'data[User][password]' => $loginPwd,
        ];
        
        $opt =  array(
            CURLOPT_URL => $this->api_base . 'users/login',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEJAR => $this->cookie_file,
            CURLOPT_POSTFIELDS => http_build_query($data)
        );

        $this->excuteCURL($opt, true);

        return $this->api_key;

    }

    // public function syncEparkToPos($organ_id, $selected_date){
    //     $organ = $this->ci->organ_model->getFromId($organ_id);
    //     if (empty($organ['epark_id'])){
    //         $this->registerOrganEparkId($organ);
    //     }

    //     return $organ;
    // }

    // public function registerOrganEparkId($organ){
    //     $epark_organs = $this->searchOrgans($organ['organ_name']);
    //     $epark_id = '';
    //     foreach($epark_organs as $item){
    //         if ($organ['organ_name'] == $item['name']){
    //             $epark_id = $item['id'];
    //             break;
    //         }
    //     }

    //     if (empty($epark_id)){

    //     }else{
            
    //     }

    //     var_dump($epark_organs);die();

    // }

    // public function searchOrgans($strKey){
        
    //     $opt =  array(
    //         CURLOPT_URL => $this->api_base . 'Shops/head?strkey=' . $strKey,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_COOKIEFILE => $this->cookie_file,
    //     );

    //     $response = $this->excuteCURL($opt);

    //     $dom = $this->responseToDom($response);

    //     $xpath = new DOMXpath($dom);

    //     $table_rows = $xpath->query('//table/tbody/tr');

    //     $data = [];
    //     foreach($table_rows as $row){
    //         $cols = $row->getElementsByTagName('td');
    //         $temp = [];
    //         //if ($cols->length)
    //         if ($cols->length != 7) continue;
    //         $temp['id'] = $cols[0]->nodeValue;
    //         $temp['code'] = $cols[1]->nodeValue;
    //         $temp['name'] = $cols[2]->nodeValue;
    //         $temp['alias'] = $cols[3]->nodeValue;
    //         $temp['tel'] = $cols[4]->nodeValue;
    //         $temp['mail'] = $cols[5]->nodeValue;
    //         $temp['order_no'] = $cols[6]->nodeValue;

    //         $data[] = $temp;
    //     }

    //     return $data;
    // }

    // public function searchStaff($strKey){
        
    //     $opt =  array(
    //         CURLOPT_URL => $this->api_base . 'staffs/serach?strkey=' . $strKey,
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_COOKIEFILE => $this->cookie_file,
    //     );

    //     $response = $this->excuteCURL($opt);

    //     $dom = $this->responseToDom($response);

    //     $xpath = new DOMXpath($dom);

    //     $table_rows = $xpath->query('//table/tbody/tr');

    //     $data = [];
    //     foreach($table_rows as $row){
    //         $cols = $row->getElementsByTagName('td');
    //         $temp = [];
    //         //if ($cols->length)
    //         if ($cols->length != 4) continue;
    //         $temp['id'] = $cols[0]->nodeValue;
    //         $temp['name'] = $cols[1]->nodeValue;
    //         $temp['nick'] = $cols[2]->nodeValue;
    //         $temp['order_no'] = $cols[3]->nodeValue;

    //         $data[] = $temp;
    //     }

    //     return $data;
    // }

//     public function addStaffInEpark($staff, $epark_organ_id){
        
//         $opt =  array(
//             CURLOPT_URL => $this->api_base . 'staffs/edit',
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_COOKIEFILE => $this->cookie_file,
//             CURLOPT_POSTFIELDS => array(
//                 '_method' => 'POST',
//                 'data[Staff][id]' => '',
//                 'data[Staff][code]' => '',
//                 'data[Staff][shop_id]' => $epark_organ_id,
//                 'data[Staff][name]' => $staff['staff_first_name'].$staff['staff_last_name'],
//                 'data[Staff][name_last]' => $staff['staff_first_name'],
//                 'data[Staff][name_first]' => $staff['staff_last_name'],
//                 'data[Staff][yomi]' => '',
//                 'data[Staff][yomi_last]' => '1',
//                 'data[Staff][yomi_first]' => '1',
//                 'data[Staff][nickname]' => $staff['staff_nick'],
//                 'data[Staff][sex]' => (empty($staff['staff_sex']) || $staff['staff_sex']==1) ? 0 : 1,
//                 'data[Staff][tel]' => $staff['staff_tel'],
//                 'data[Staff][email]' => $staff['staff_mail'],
// //                'userfile'=> new CURLFILE('/path/to/file'),
//  //               'data[Staff][sort]' => '0',
//                 'data[Staff][salary_type]' => 1,
//                 // 'data[Staff][salary]' => '',
//                 // 'data[Staff][salary_fix]' => '',
//                 // 'data[Staff][salary_help]' => '',
//                 // 'data[Staff][salary_ratio]' => '',
//                 // 'data[Staff][traffic_cost]' => '',
//                 'registeredBusinessIsOpen' => '0',
//                 'data[Staff][activebegin][year]' => date('Y'),
//                 'data[Staff][activebegin][month]' => date('m'),
//                 'data[Staff][activebegin][day]' => date('d'),
//                 'data[Staff][activeend][year]' => '',
//                 'data[Staff][activeend][month]' => '',
//                 'data[Staff][activeend][day]' => '',
//                 'data[Staff][active]' => '1',
//             ),
//         );

//         $response = $this->excuteCURL($opt);
//     }

    public function loadEparkReceipt($organ_id, $selected_date){
        $organ = $this->ci->organ_model->getFromId($organ_id);
        if (empty($organ['epark_id'])){
            return 3; // not find epark organ;
        }
        $shop_id = $organ['epark_id'];
        $shifts = $this->loadEparkShifts($shop_id, $selected_date);
        $bookings = $this->loadEparkBookings($shop_id, $selected_date);

        $this->receiptEparkToPosOfShfit($organ_id, $shifts, $bookings, $selected_date);
        return 0;
    }

    public function staffEparkToPos($epark_staff_id, $organ_id){
        
        $opt =  array(
            CURLOPT_URL => $this->api_base . 'Staffs/edit/' . $epark_staff_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEFILE => $this->cookie_file,
        );

        $response = $this->excuteCURL($opt);

        $dom = $this->responseToDom($response);

        $xpath = new DOMXpath($dom);

        $company_id = $this->company_id;
        $staff_first_name = $xpath->query("//input[@id='StaffNameLast']")->item(0)->getAttribute('value');
        $staff_last_name = $xpath->query("//input[@id='StaffNameFirst']")->item(0)->getAttribute('value');
        $staff_nick_name = $xpath->query("//input[@id='StaffNickname']")->item(0)->getAttribute('value');
        $staff_auth = STAFF_AUTH_OWNER;
        $staff_tel = $xpath->query("//input[@id='StaffTel']")->item(0)->getAttribute('value');
        $staff_mail = $xpath->query("//input[@id='StaffEmail']")->item(0)->getAttribute('value');

        $staff_sex = 1;
        if ($xpath->query("//input[@id='StaffSex0']")->item(0)->getAttribute('checked')) $staff_sex = 1;
        if ($xpath->query("//input[@id='StaffSex1']")->item(0)->getAttribute('checked')) $staff_sex = 2;
        $staff_sort_no = $xpath->query("//input[@id='StaffSort']")->item(0)->getAttribute('value');

//        $organ = $this->ci->organ_model->getFromId($organ_id);
//        if (empty($organ) || empty($organ['epark_id'])) return false;

        $origin_staff_organ = $this->ci->staff_organ_model->getOneByParam(['epark_staff_id' => $epark_staff_id]);
        //die();
        if (empty($origin_staff_organ)){
            $staff = $this->ci->staff_model->getOneByParam(['staff_first_name'=>$staff_first_name, 'staff_last_name'=>$staff_last_name, 'staff_nick'=>$staff_nick_name]);
            if (empty($staff)){
                $_id = $this->ci->staff_model->insertRecord([
                    'company_id' => $company_id,
                    'staff_auth' => $staff_auth,
                    'staff_nick' => $staff_nick_name,
                    'staff_first_name' => $staff_first_name,
                    'staff_last_name' => $staff_last_name,
                    'staff_tel' => $staff_tel,
                    'staff_mail' => $staff_mail,
                    'staff_sex' => $staff_sex,
                    'visible' => 1,
                    'sort_no' => $staff_sort_no
                ]);
            }else{
                $_id = $staff['staff_id'];
            }

            $this->ci->staff_organ_model->insertRecord([
                 'organ_id' => $organ_id,
                 'staff_id' => $_id,
                 'epark_staff_id' => $epark_staff_id
            ], false);
            
            return $_id;
        }else{
            return $origin_staff_organ['staff_id'];
        }

        return false;
    }

    private function excuteCURL($opt, $is_load_cookie = false){

        $curl = curl_init();

        curl_setopt_array($curl,$opt);
        
        $response = curl_exec($curl);

        if ($is_load_cookie){
            $cookies = curl_getinfo($curl, CURLINFO_COOKIELIST);
            $this->api_key = substr(explode('CAKEPHP' ,$cookies[0])[1], 1);
        }

        curl_close($curl);

        return $response;
    }

    private function responseToDom($response){
        
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);

        $dom->encoding = 'UTF-8'; // Set the document encoding to UTF-8
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $response); // Prepend XML encoding declaration
        $dom->formatOutput = true;
        libxml_clear_errors();

        return $dom;
    }


    // ---------------------------------------------------------

    public function receiptEparkToPosOfShfit($organ_id, $shifts, $bookings, $selected_date){
    
        $exist_staffs = [];
        foreach($shifts as $shift){

            $staff_id = $this->staffEparkToPos($shift['staff_id'], $organ_id);
            $exist_staffs[] = $staff_id;

            $day_shifts = array();
            $tmp = [];
            $tmp['from_time'] = $shift['datetime_begin'];
            $tmp['to_time'] = $shift['datetime_end'];
            $day_shifts[] = $tmp;

            foreach($bookings as $booking){
                if ($booking['shift_id'] == $shift['id'] && $booking['kind'] > 0 && empty($booking['cancelled'])){
                    for($i=0; $i<count($day_shifts); $i++){
                        $item = $day_shifts[$i];
                        if ($item['from_time']<=$booking['datetime_begin'] && $item['to_time']>= $booking['datetime_end']){
                            if ($item['from_time'] == $booking['datetime_begin'] && $item['to_time'] == $booking['datetime_end']){
                                array_splice($day_shifts, $i, 1);
                            }elseif($item['from_time'] == $booking['datetime_begin']){
                                $day_shifts[$i]['from_time'] = $booking['datetime_end'];
                            }elseif($item['to_time'] == $booking['datetime_end']){
                                $day_shifts[$i]['to_time'] = $booking['datetime_begin'];
                            }else{
                                $_to_time = $day_shifts[$i]['to_time'];
                                $day_shifts[$i]['to_time'] = $booking['datetime_begin'];
                                
                                $tmp = [];
                                $tmp['from_time'] = $booking['datetime_end'];
                                $tmp['to_time'] = $_to_time;
                                $day_shifts[] = $tmp;
                            }
                            break;
                        }
                    }
                }
            }
            
            $pos_shifts = $this->ci->shift_model->getListDataByCond([
                'organ_id' => $organ_id, 
                'staff_id'=>$staff_id, 
                'select_date'=>$selected_date, 
                'is_enable_apply' =>1
            ]);

            if (empty($pos_shifts)){
                foreach($day_shifts as $item){
                    $this->ci->shift_model->insertRecord([
                        'staff_id' => $staff_id,
                        'organ_id' => $organ_id,
                        'from_time' => $item['from_time'],
                        'to_time' => $item['to_time'],
                        'shift_type' => SHIFT_STATUS_APPLY,
                        //'epark_shift_id' => $shift_id,
                        'visible' => 1
                    ]);
                }
            }else{
                array_multisort(array_column($pos_shifts, 'from_time'), $pos_shifts);
                $leng = count($day_shifts) > count($pos_shifts) ? count($day_shifts) : count($pos_shifts);

                for($i=0; $i<$leng; $i++){
                    if ($i >= count($pos_shifts)){
                        $item = $day_shifts[$i];
                        $this->ci->shift_model->insertRecord([
                            'staff_id' => $staff_id,
                            'organ_id' => $organ_id,
                            'from_time' => $item['from_time'],
                            'to_time' => $item['to_time'],
                            'shift_type' => SHIFT_STATUS_APPLY,
                            //'epark_shift_id' => $shift_id,
                            'visible' => 1
                        ]);
                    }elseif($i >= count($day_shifts)){
                        $this->ci->shift_model->delete_force($pos_shifts[$i]['shift_id'], 'shift_id');
                    }else{
                        $item = $pos_shifts[$i];
                        $item['from_time'] = $day_shifts[$i]['from_time'];
                        $item['to_time'] = $day_shifts[$i]['to_time'];
                        
                        $this->ci->shift_model->updateRecord($item, 'shift_id');
                    }
                }
            }
            $this->updateOldOtherFromEpark($organ_id, $staff_id, $selected_date);
        }

        $this->ci->shift_model->deleteEparkDeleteData($organ_id, $selected_date, $exist_staffs);

    }

    public function updateShiftToEpark($organ_id, $staff_id, $selected_date){
        $organ = $this->ci->organ_model->getFromId($organ_id);
        if (empty($organ['epark_id'])) return array('result' => false, 'msg' => 'no epark shop');

        $epark_organ_id = $organ['epark_id'];
        
        $stf_result = $this->loadEparkStaff($staff_id, $organ_id, $epark_organ_id);
        if (!$stf_result['result']) return $stf_result;

        $epark_staff_id = $stf_result['epark_staff_id'];

        $pos_shifts = $this->ci->shift_model->getListDataByCond([
            'organ_id' => $organ_id,
            'staff_id' => $staff_id,
            'select_date' => $selected_date
        ]);

        $shifts = $this->loadEparkShifts($epark_organ_id, $selected_date);
        $bookings = $this->loadEparkBookings($epark_organ_id, $selected_date);
        
        $isNew = true;
        foreach($shifts as $shift){
            if ($shift['staff_id'] == $epark_staff_id){
                $isNew = false;
                $updateShift = $shift;
            }
        }

        if(empty($pos_shifts) && !$isNew){
            foreach($bookings as $booking){
                if ($booking['shift_id'] == $updateShift['id'] && empty($booking['cancelled']) && $booking['kind']>0)
                    $this->deleteBookingInEpark($booking);
            }

            foreach($shifts as $shift){
                $this->deleteShiftInEpark($updateShift);
            }

            return array('result' => true);
        }

        $from = null;
        $to = null;
        $_last_to = null;
        $others = [];
        array_multisort(array_column($pos_shifts, 'from_time'), $pos_shifts);
        foreach($pos_shifts as $item){
            if (empty($from) || $from>$item['from_time']) $from = $item['from_time'];
            if (empty($to) || $to<$item['to_time']) $to = $item['to_time'];

            if (!empty($_last_to) && $item['from_time']>$_last_to){
                $temp = [];
                $temp['from_time'] = $_last_to;
                $temp['to_time'] = $item['from_time'];
                $others[] = $temp;
            }

            if ($item['shift_type'] != SHIFT_STATUS_APPLY && $item['shift_type'] != SHIFT_STATUS_ME_APPLY){
                $others[] = $item;
            }
            $_last_to = $item['to_time'];
        }

        if ($isNew){
            $sht_result = $this->addShiftInEpark($from, $to, $epark_staff_id, $epark_organ_id);
        }else{
            if ($updateShift['datetime_begin'] != $from || $updateShift['datetime_end'] != $to){
                $f_bookings = [];
                foreach($bookings as $booking){
                    if ($booking['shift_id'] == $updateShift['id'] && empty($booking['cancelled']) && $booking['kind']>0){
                        if ($from>$booking['datetime_begin'] || $to<$booking['datetime_end']){
                            $this->deleteBookingInEpark($booking);
                        }
                    }
                }
    
                $sht_result = $this->updateShiftInEpark($updateShift, $from, $to);
            }
        }

        
        $shifts = $this->loadEparkShifts($epark_organ_id, $selected_date);
        $epark_shift_id = null;

        foreach($shifts as $shift){
            if ($shift['staff_id'] == $epark_staff_id){
                $epark_shift_id = $shift['id'];
                break;
            }
        }
        if (empty($epark_shift_id)) return array('result' => false);

        $bookings = $this->loadEparkBookings($epark_organ_id, $selected_date);

        $ef_bookings = [];
        foreach($bookings as $booking){
            if ($booking['shift_id'] == $epark_shift_id && empty($booking['cancelled']) && $booking['kind']>0){
                $ef_bookings[] = $booking;
            }
        }

        // $leng = count($others) > count($ef_bookings) ? count($others) : count($ef_bookings);

        // for($i=0; $i<$leng; $i++){
        //     if ($i >= count($others)){
        //         $r = $this->deleteBookingInEpark($ef_bookings[$i]);
        //     }elseif($i >= count($ef_bookings)){
        //         $r = $this->addBookingInEpark($others[$i]['from_time'], $others[$i]['to_time'], $epark_staff_id, $epark_organ_id,  $epark_shift_id);
        //     }else{
        //         if($others[$i]['from_time'] != $ef_bookings[$i]['datetime_begin'] || $others[$i]['to_time'] != $ef_bookings[$i]['datetime_end']){
        //             $r = $this->updateBookingInEpark($others[$i]['from_time'], $others[$i]['to_time'], $ef_bookings[$i]);
        //         }
        //     }
        // }
        array_multisort(array_column($ef_bookings, 'datetime_begin'), $ef_bookings);
        $epark_i = 0;
        $pos_i = 0;
        while($epark_i<count($ef_bookings) || $pos_i<count($others)){
            
            if( $epark_i >= count($ef_bookings) ){
                $r = $this->addBookingInEpark($others[$pos_i]['from_time'], $others[$pos_i]['to_time'], $epark_staff_id, $epark_organ_id,  $epark_shift_id);
                $pos_i++;
                continue;
            }
            if( $pos_i >= count($others) ){
                $r = $this->deleteBookingInEpark($ef_bookings[$epark_i]);
                $epark_i++;
                continue;
            }

            $u_pos = $others[$pos_i];
            $u_booking = $ef_bookings[$epark_i];

            if($u_pos['from_time'] == $u_booking['datetime_begin'] && $u_pos['to_time'] == $u_booking['datetime_end']){
                $pos_i++;
                $epark_i++;
                continue;
            }

            $d_cnt = 0;
            while($epark_i+$d_cnt+1 < count($ef_bookings) && $ef_bookings[$epark_i+$d_cnt+1]['datetime_begin'] < $u_pos['to_time']){
                if ($ef_bookings[$epark_i+$d_cnt+1]['datetime_end'] <= $u_pos['to_time']){
                    $r = $this->deleteBookingInEpark($ef_bookings[$epark_i+$d_cnt+1]);
                    $d_cnt++;
                }else{
                    $r = $this->updateBookingInEpark($u_pos['to_time'], $others[$epark_i+$d_cnt+1]['to_time'], $ef_bookings[$epark_i+$d_cnt+1]);
                    break;
                }
            }

            $r = $this->updateBookingInEpark($others[$epark_i]['from_time'], $others[$epark_i]['to_time'], $ef_bookings[$epark_i]);
            
            $pos_i++;
            $epark_i = $epark_i + 1 + $d_cnt;
        }

        return ['result' => true];
    }

    private function loadEparkStaff($staff_id, $organ_id, $epark_organ_id){
        
        $staff_organ = $this->ci->staff_organ_model->getOneByParam(['staff_id'=>$staff_id, 'organ_id'=>$organ_id]);
        if (empty($staff_organ['epark_staff_id'])){
            $staff = $this->ci->staff_model->getFromId($staff_id);
            $search_staffs = $this->searchStaff($staff['staff_first_name'].$staff['staff_last_name']);

            if (empty($search_staffs)){
                $this->addStaffInEpark($staff, $epark_organ_id);

                $search_staffs = $this->searchStaff($staff['staff_first_name'].$staff['staff_last_name']);
                if (empty($search_staffs)){
                    return array('result' => false, 'msg' => 'sync staff fail');
                }
            }
            
            $load_staff = null;
            foreach($search_staffs as $epark_staff){
                if ($epark_staff['nick'] == $staff['staff_nick']){
                    $load_staff = $epark_staff;
                }
            }

            if (empty($load_staff)) return array('result' => false, 'msg' => 'sync staff fail');
            $epark_staff_id = $load_staff['id'];
            if (empty($staff_organ)){
                $this->ci->staff_organ_model->insertRecord(array(
                    'organ_id' => $organ_id,
                    'staff_id' => $staff_id,
                    'epark_staff_id' => $epark_staff_id
                ), false);
            }else{
                $staff_organ['epark_staff_id'] = $epark_staff_id;
                $this->ci->staff_organ_model->updateRecord($staff_organ, 'id', false);
            }
        }else{
            $epark_staff_id = $staff_organ['epark_staff_id'];
        }

        return array('result' => true, 'epark_staff_id' => $epark_staff_id);
    }

    private function updateOldOtherFromEpark($organ_id, $staff_id, $selected_date){
        $pos_shifts = $this->ci->shift_model->getListDataByCond([
            'organ_id' => $organ_id, 
            'staff_id'=>$staff_id, 
            'select_date'=>$selected_date, 
            'is_enable_apply' =>1
        ]);

        foreach($pos_shifts as $item){
            $other_shifts = $this->ci->shift_model->getListDataByCond([
                'organ_id' => $organ_id, 
                'staff_id'=>$staff_id, 
                'in_from_time' => $item['from_time'],
                'in_to_time' => $item['to_time'],
                'is_disable_apply' =>1
            ]);

            foreach($other_shifts as $other){
                if ($other['shift_type']== SHIFT_STATUS_REST){
                    $this->ci->shift_model->delete_force($other['shift_id'], 'shift_id');
                } 

                if ($other['from_time'] >= $item['from_time'] && $other['to_time'] <= $item['to_time']){
                    $this->ci->shift_model->delete_force($other['shift_id'], 'shift_id');
                }elseif($other['from_time'] < $item['from_time'] && $other['to_time'] > $item['to_time']){
                    $_to = $other['to_time'];
                    $other['to_time'] = $item['from_time'];
                    $this->ci->shift_model->updateRecord($other, 'shift_id');
                    $this->ci->shift_model->insertRecord([
                        'staff_id' => $item['staff_id'],
                        'organ_id' => $item['organ_id'],
                        'from_time' => $item['to_time'],
                        'to_time' => $_to,
                        'shift_type' => $other['shift_type'],
                        'epark_shift_id' => $item['epark_shift_id'],
                        'visible' => 1
                    ]);
                }elseif($other['from_time'] < $item['to_time']){
                    $other['from_time'] = $item['to_time'];
                    $this->ci->shift_model->updateRecord($other, 'shift_id');
                }elseif($other['to_time'] > $item['from_time']){
                    $other['to_time'] = $item['from_time'];
                    $this->ci->shift_model->updateRecord($other, 'shift_id');
                }

            }
        }
    }

    public function updateEpark($shop_id, $select_date, $data){

        $epark_olds = $this->loadEparkShifts($shop_id, $select_date);
        $epark_old_bookings = $this->loadEparkBookings($shop_id, $select_date);
        
        foreach ($data as $item){
            $staff_id = $item['epark_staff_id'];
            $shift_from = $item['shift_from'];
            $shift_to = $item['shift_to'];
            $bookings = $item['bookings'];

            $old = [];
            foreach($epark_olds as $temp){
                if ($temp['staff_id'] == $staff_id){
                    $old = $temp;
                    break;
                }
            }
            
            if (empty($old)){
                $result = $this->addShiftInEpark($shift_from, $shift_to, $staff_id, $shop_id);
                if (!empty($result['id'])) $shift_id = $result['id'];
            }else{
                $old['datetime_begin'] = $shift_from;
                $old['datetime_end'] = $shift_to;
                $shift_id = $old['id'];

                $this->updateShiftInEpark($old);
            }

            if(!empty($shift_id)){
                $this->updateBooking($epark_old_bookings, $shift_id, $bookings, $staff_id, $shop_id);
            }
        }

        foreach($epark_olds as $temp){
            $is_delete = true;
            foreach($data as $item){
                if ($temp['staff_id'] == $item['epark_staff_id']){
                    $is_delete = false;
                    break;
                }
            }
            if($is_delete){
                foreach($epark_old_bookings as $tmp_booking){
                    if (!empty($tmp_booking['cancelled'])) continue;
                    if($tmp_booking['shift_id'] == $temp['id']){
                        $this->deleteBookingInEpark($tmp_booking);
                    }
                }
                $this->deleteShiftInEpark($temp);
            }
        }
        
    }

    public function loadShopBussinessTime($shop_id){
        
        $opt =  array(
            CURLOPT_URL => $this->api_base . 'Shops/edit/' . $shop_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEFILE => $this->cookie_file,
        );

        $response = $this->excuteCURL($opt);
    
        $dom = $this->responseToDom($response);

        $xpath = new DOMXpath($dom);

        $from = $xpath->query("//*[@id='ShopsettingTimebegin']/option[@selected='selected']/@value")->item(0)->value;
        $to = $xpath->query("//*[@id='ShopsettingTimeend']/option[@selected='selected']/@value")->item(0)->value;

        if (!empty($from)) $this->bussiness_fromtime = $from;
        if (!empty($to)) $this->bussiness_totime = $to;
    }
    
    private function loadEparkShifts($shop_id, $selected_date){
        $opt = array(
            CURLOPT_URL => $this->api_base . 'mi_api/board/master/read/',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_POSTFIELDS => json_encode(array(
                    'api_key' => $this->api_key,
                    'shop_id' => $shop_id,
                    'date' => $selected_date,
                    'front_view' => 0
                )),
            );


        $response = $this->excuteCURL($opt);

        $res = json_decode($response, true);
        $shifts = [];
        if ($res['status']){
            if (!empty($res['result']['shifts'])){
                $shifts = $res['result']['shifts'];
            }
        }

        return $shifts;
    }
    
    public function addShiftInEpark($from, $to, $epark_staff_id, $epark_organ_id){
        $the_date = substr($from,0,10);
        $from_time = substr($from, 11);
        $to_time = substr($to, 11);

        if($to_time < $this->bussiness_fromtime || $from_time > $this->bussiness_totime){
            return array('result'=>false);
        }

        if ($from_time < $this->bussiness_fromtime) $from_time = $this->bussiness_fromtime;
        if ($to_time > $this->bussiness_totime) $to_time = $this->bussiness_totime;

        $opt =  array(
            CURLOPT_URL => $this->api_base . 'mi_api/newpm/shift/register',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_POSTFIELDS => json_encode(array(
               'api_key' => $this->api_key,
               'shop_id' => $epark_organ_id,
               'date' => $the_date,
               'front_view' => 0,
               'shift_detail' => array(
                    $epark_staff_id => array(
                        'staff_id' => $epark_staff_id,
                        'shift_timebegin' => $from_time,
                        'shift_timeend' => $to_time
                    )
               )

            )),
        );
        
        $response = $this->excuteCURL($opt);

        $result = json_decode($response, true);

        if (!$result['status']){
            return array('result'=>false, 'msg' => 'sync shift fail');
        }

        return array('result'=>true, 'id' => array_values($result['result']['shift_detail'])[0]);
    }

    public function updateShiftInEpark($shift){
        $from_time = $shift['datetime_begin'];
        $to_time = $shift['datetime_end'];

        $from_time_str = substr($from_time, 11);
        $to_time_str = substr($to_time, 11);

        if($to_time_str < $this->bussiness_fromtime || $from_time_str > $this->bussiness_totime){
            return array('result'=>false);
        }

        if ($from_time_str < $this->bussiness_fromtime) $from_time_str = $this->bussiness_fromtime;
        if ($to_time_str > $this->bussiness_totime) $to_time_str = $this->bussiness_totime;

        $from_time = $shift['thedate'] . ' ' . $from_time_str;
        $to_time = $shift['thedate'] . ' ' . $to_time_str;

        $opt =  array(
            CURLOPT_URL => $this->api_base . 'mi_api/newpm/shift/updateMultiple',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_POSTFIELDS => json_encode(array(
               'api_key' => $this->api_key,
               'shop_id' => $shift['shop_id'],
               'shifts' => array(
                    [
                        'date' => $shift['thedate'],
                        'shift_detail' => [
                            'shift_id' => $shift['id'],
                            'shift_timebegin' => $from_time,
                            'shift_timeend' => $to_time,
                        ]
                    ]
               )
            )),
        );

        $response = $this->excuteCURL($opt);
        $result = json_decode($response, true);
        
        if (!$result['status']){
            return array('result'=>false, 'msg' => 'sync shift fail');
        }

        return array('result'=>true);
    }

    public function deleteShiftInEpark($shift){

        $opt =  array(
            CURLOPT_URL => $this->api_base . 'mi_api/newpm/shift/deleteMultiple',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_POSTFIELDS => json_encode(array(
               'api_key' => $this->api_key,
               'shop_id' => $shift['shop_id'],
               'shifts' => array(
                    [
                        'date' => $shift['thedate'],
                        'shift_id' => $shift['id'],
                    ]
               )
            )),
        );

        $response = $this->excuteCURL($opt);
        $result = json_decode($response, true);

        if (!$result['status']){
            return array('result'=>false, 'msg' => 'sync shift fail');
        }

        return array('result'=>true);
    }

    private function loadEparkBookings($shop_id, $selected_date){
        $opt = array(
            CURLOPT_URL => $this->api_base . 'mi_api/board/booking/list/',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_POSTFIELDS => json_encode(array(
                    'api_key' => $this->api_key,
                    'shop_id' => $shop_id,
                    'date' => $selected_date,
                    'front_view' => 0
                )),
            );


        $response = $this->excuteCURL($opt);
        
        $res = json_decode($response, true);
        $bookings = [];
        if ($res['status']){
            if (!empty($res['result']['booking'])){
                $bookings = $res['result']['booking'];
            }
        }
        return $bookings;

    }

    private function updateBooking($old_bookings, $shift_id, $others, $staff_id, $shop_id){
        
        $ef_bookings = [];
        foreach($old_bookings as $tmp){
            if (!empty($tmp['cancelled'])) continue;
            if($tmp['shift_id'] == $shift_id){
                $ef_bookings[] = $tmp;
            }
        }
        
        array_multisort(array_column($ef_bookings, 'datetime_begin'), $ef_bookings);
        $epark_i = 0;
        $pos_i = 0;
        while($epark_i<count($ef_bookings) || $pos_i<count($others)){
            
            if( $epark_i >= count($ef_bookings) ){
                $r = $this->addBookingInEpark($others[$pos_i]['from_time'], $others[$pos_i]['to_time'], $staff_id, $shop_id,  $shift_id);
                $pos_i++;
                continue;
            }
            if( $pos_i >= count($others) ){
                $r = $this->deleteBookingInEpark($ef_bookings[$epark_i]);
                $epark_i++;
                continue;
            }

            $u_pos = $others[$pos_i];
            $u_booking = $ef_bookings[$epark_i];

            if($u_pos['from_time'] == $u_booking['datetime_begin'] && $u_pos['to_time'] == $u_booking['datetime_end']){
                $pos_i++;
                $epark_i++;
                continue;
            }

            $d_cnt = 0;
            while($epark_i+$d_cnt+1 < count($ef_bookings) && $ef_bookings[$epark_i+$d_cnt+1]['datetime_begin'] < $u_pos['to_time']){
                if ($ef_bookings[$epark_i+$d_cnt+1]['datetime_end'] <= $u_pos['to_time']){
                    $r = $this->deleteBookingInEpark($ef_bookings[$epark_i+$d_cnt+1]);
                    $d_cnt++;
                }else{
                    $r = $this->updateBookingInEpark($u_pos['to_time'], $others[$epark_i+$d_cnt+1]['to_time'], $ef_bookings[$epark_i+$d_cnt+1]);
                    break;
                }
            }

            $r = $this->updateBookingInEpark($others[$epark_i]['from_time'], $others[$epark_i]['to_time'], $ef_bookings[$epark_i]);
            
            $pos_i++;
            $epark_i = $epark_i + 1 + $d_cnt;
        }
    }

    
    public function addBookingInEpark($from, $to, $epark_staff_id, $epark_organ_id, $epark_shift_id){
        $from_int_array = explode(':', substr($from, 11));
        $to_int_array = explode(':', substr($to, 11));

        $f = intval($from_int_array[0]) * 60 + intval($from_int_array[1]);
        $t = intval($to_int_array[0]) * 60 + intval($to_int_array[1]);

        $opt =  array(
            CURLOPT_URL => $this->api_base . 'mi_api/board/booking/saveoperation',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_POSTFIELDS => json_encode(array(
               'api_key' => $this->api_key,
               'shop_id' => $epark_organ_id,
               'date' => substr($from,0,10),
               'booking_id' => null,
               'booking_course_id' => null,
               'shift_id' => $epark_shift_id,
               'datetime_begin' => $from,
               'mins_body' => $t - $f,
               'opt_type' => 2,
               'comment' => '業務'
            )),
        );
        
        $response = $this->excuteCURL($opt);

        $result = json_decode($response, true);

        if (!$result['status']){
            return array('result'=>false, 'msg' => 'sync shift fail');
        }

        return array('result'=>true);
    }

    public function updateBookingInEpark($from, $to, $booking){
        $from_int_array = explode(':', substr($from, 11));
        $to_int_array = explode(':', substr($to, 11));

        $f = intval($from_int_array[0]) * 60 + intval($from_int_array[1]);
        $t = intval($to_int_array[0]) * 60 + intval($to_int_array[1]);


        $opt =  array(
            CURLOPT_URL => $this->api_base . 'mi_api/board/booking/saveoperation',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_POSTFIELDS => json_encode(array(
               'api_key' => $this->api_key,
               'shop_id' => $booking['shop_id'],
               'date' => substr($from,0,10),
               'booking_id' => $booking['booking_id'],
               'booking_course_id' => $booking['booking_course_id'],
               'shift_id' => $booking['shift_id'],
               'booth_id' => null,
               'datetime_begin' => $from,
               'mins_body' => $t - $f,
               'opt_type' => $booking['kind'],
               'comment' => ($booking['kind']==2) ? '業務' : null,
            )),
        );
        
        $response = $this->excuteCURL($opt);

        $result = json_decode($response, true);
        
        if (!$result['status']){
            return array('result'=>false, 'msg' => 'sync shift fail');
        }

        return array('result'=>true);
    }

    public function deleteBookingInEpark($booking){

        $opt =  array(
            CURLOPT_URL => $this->api_base . 'mi_api/board/booking/cancel',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => $this->cookie_file,
            CURLOPT_POSTFIELDS => json_encode(array(
               'api_key' => $this->api_key,
               'shop_id' => $booking['shop_id'],
               'booking_id' => $booking['booking_id'],
               'special_instructions' => 1,
            )),
        );

        $response = $this->excuteCURL($opt);
        $result = json_decode($response, true);
        if (!$result['status']){
            return array('result'=>false, 'msg' => 'sync shift fail');
        }

        return array('result'=>true);
    }
    
}

?>