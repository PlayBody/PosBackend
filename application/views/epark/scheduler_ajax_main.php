    <?php
        $open_time = $organ_time['open_time'];
        $close_time = $organ_time['close_time'];
        $real_open_time = $organ_time['real_open_time'];
        $real_close_time = $organ_time['real_close_time'];
        $show_open_time = $organ_time['show_open_time'];
        $show_close_time = $organ_time['show_close_time'];
        if ($time_mode==2){
            $show_open_time = $open_time;
            $show_close_time = $close_time;
        }
    ?>

<div>

</div>
    <div>
        <div id="time_mark_line" class="btn-group btn-custom-groups btn-custom-groups-one">
            <?php
            for ($i=$open_time; $i<$close_time; $i+=60){
                $h =  floor($i/60);
                ?>
                <button type="button" class="btn btn-primary <?php if ($sel_time==$h) echo 'active'; ?> " value="<?php echo $h; ?>"><?php echo $h; ?></button>
            <?php } ?>

        </div>
    </div>
    <div id="schedule_mod" style="display: flex;">
        <div id="schedule_mod_time" class="btn-group btn-custom-groups btn-custom-groups-one">
            <button id="test" type="button" class="btn btn-primary <?php if ($time_mode==1) echo 'active'; ?>" value="1">基本</button>
            <button type="button" class="btn btn-primary <?php if ($time_mode==2) echo 'active'; ?>" value="2">全体</button>
        </div>
        <div id="schedule_mod_type" class="btn-group btn-custom-groups btn-custom-groups-one">
            <button type="button" class="btn btn-primary <?php if ($type_mode==1) echo 'active'; ?>" value="1">シフト</button>
            <button type="button" class="btn btn-primary <?php if ($type_mode==2) echo 'active'; ?>" value="2">ブース</button>
            <button type="button" class="btn btn-primary <?php if ($type_mode==3) echo 'active'; ?>" value="3">両方</button>
        </div>
        <div id="target_to_other_date" class="<?php if(count($waitings)>0) echo 'waiting'; ?>">

            <?php
            foreach ($waitings as $order){
                $from_time = intval(substr($order['from_time'], 11, 2)) * 60 + intval(substr($order['from_time'], 14, 2));
                $to_time = intval(substr($order['to_time'], 11, 2)) * 60 + intval(substr($order['to_time'], 14, 2));
                $width = ($to_time - $from_time + $order['interval'])/($show_close_time - $show_open_time) * 100;
                $real_width = ($to_time - $from_time) /  ($to_time - $from_time + $order['interval']) * 100;
                $txt_left = 0;
                ?>
                <div order="<?php echo $order['id']; ?>" class="epark-order epark-appoint waiting" style="background-color:<?php echo $order['interval_color']; ?>; border-color: <?php echo $order['border_color']; ?>;  width: <?php echo $width; ?>%; overflow: hidden;" length="<?php echo $to_time - $from_time+$order['interval']; ?>" from="<?php echo $from_time ?>"  to="<?php echo $to_time ?>">
                    <?php include 'scheduler_reserve_obj.php'; ?>
                </div>
            <?php } ?>

        </div>
        <div>
            <a id="btn_shift_add" href="#" class="btn btn-primary">シフト追加</a>
        </div>
    </div>
    <div id="schedule_main" class="epark-schedule-back">
        <div class="time_mark">
            <div class="staffs"></div>
            <div class="hour-list">
                <?php
                for ($i=$show_open_time; $i<$show_close_time; $i+=60){
                    $h =  floor($i/60);
                    ?>
                    <div style="display: flex;position: relative;">
                        <div class="hour"><?php echo $h; ?></div>
                        <?php if ($time_mode==1){ ?>
                            <?php for($ii=1;$ii<6;$ii++){ ?>
                                <div style="position: absolute; left: <?php echo 100/6*$ii; ?>%;"><?php echo $ii*10; ?> </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php if ($type_mode==3){ ?>
            <div class="shift_title">シフト</div>
        <?php } ?>
        <?php if ($type_mode!=2){ ?>
            <div class="shift_content">
                <?php if (empty($staffs)){ ?>
                    <div class="no_shift_comment">
                        シフトがありません。シフト追加からシフトを登録してください。
                    </div>
                <?php }else{ ?>
                    <div class="staffs">
                        <?php foreach ($staffs as $staff){ ?>
                            <div data="<?php echo $staff['staff_id']; ?>" class="staff_name ui-draggable ui-draggable-handle ui-droppable <?php if ($staff['staff_sex']==2) echo 'sex2'; else echo 'sex1'; ?>" ><?php echo $staff['staff_first_name']. " " . $staff['staff_last_name']; ?></div>
                        <?php } ?>
                    </div>
                    <div class="cell-list">
                        <?php foreach ($staffs as $staff){ ?>
                            <div staff="<?php echo $staff['staff_id']; ?>" class="cell-row shift-cell <?php if ($time_mode==1) echo 'time_mode'; ?>">
                                <?php
                                $shifts = $staff['shifts'];
                                $others = [];
                                for ($i=$show_open_time; $i<$show_close_time; $i+=5){
                                    $h =  floor($i/60);
                                    $m = $i % 60;
                                    $m = $m<10 ? "0".$m : $m;
                                    $cell_type = "none";
                                    $isText = false;
                                    foreach ($shifts as $shift){
                                        $from_time = intval(substr($shift['from_time'], 11, 2)) * 60 + intval(substr($shift['from_time'], 14, 2));
                                        $to_time = intval(substr($shift['to_time'], 11, 2)) * 60 + intval(substr($shift['to_time'], 14, 2));
                                        $shift_type = intval($shift['shift_type']);
                                        if ($from_time<=$i && $to_time>$i){
                                            if ($shift_type==SHIFT_STATUS_APPLY || $shift_type==SHIFT_STATUS_ME_APPLY){
                                                $cell_type="shift-apply";
                                            }else{
                                                $cell_type="none";

                                                $isExist = false;
                                                foreach ($others as $other){
                                                    if ($other['shift']['shift_id'] == $shift['shift_id']){
                                                        $isExist = true;
                                                        break;
                                                    }
                                                }
                                                if (!$isExist){
                                                    $isText = true;
                                                    $width = ($to_time - $from_time)/($show_close_time - $show_open_time) * 100;
                                                    $left = ($from_time-$show_open_time)/($show_close_time - $show_open_time) * 100;
                                                    if ($from_time>$show_open_time){
                                                        $txt_left = 0;
                                                    }else{
                                                        $txt_left = ($show_open_time-$from_time)/($show_close_time-$show_open_time)*100;
                                                    }
                                                    $tmp=[];
                                                    $tmp['width'] = $width;
                                                    $tmp['left'] = $left;
                                                    $tmp['txt_left'] = $txt_left;
                                                    $tmp['type'] = $shift_type;
                                                    $tmp['shift'] = $shift;
                                                    $others[] = $tmp;
                                                }
                                            }
                                            break;
                                        }
                                    }
                                    ?>
                                    <div class="cell <?php echo $cell_type; ?>" title="<?php echo $h.":".$m; ?>" <?php if ($cell_type=='shift-apply') echo 'shift="'.$shift['shift_id'].'"'; ?>></div>
                                <?php } ?>
                                <?php foreach ($others as $other){
                                    $shift = $other['shift'];
                                    $cell_type = "";
                                    if($shift['shift_type']==SHIFT_STATUS_SUBMIT){
                                        $cell_type = 'ap-submit';
                                        $cell_title = '申請中';
                                    }
                                    if($shift['shift_type']==SHIFT_STATUS_REJECT){
                                        $cell_type = 'ap-reject';
                                        $cell_title = '拒否';
                                    }
                                    if($shift['shift_type']==SHIFT_STATUS_OUT){
                                        $cell_type = 'ap-out';
                                        $cell_title = '店外待機';
                                    }
                                    if($shift['shift_type']==SHIFT_STATUS_REST){
                                        $cell_type = 'ap-rest';
                                        $cell_title = '休み';
                                    }
                                    if($shift['shift_type']==SHIFT_STATUS_REQUEST){
                                        $cell_type = 'ap-request';
                                        $cell_title = '出勤依頼';
                                    }
                                    if($shift['shift_type']==SHIFT_STATUS_ME_REPLY){
                                        $cell_type = 'ap-reply';
                                        $cell_title = '承認待ち';
                                    }
                                    if($shift['shift_type']==SHIFT_STATUS_ME_REJECT){
                                        $cell_type = 'ap-reject';
                                        $cell_title = '拒否';
                                    }
                                    if ($isLock){
                                        $cell_type="ap-lock";
                                    }

                                    ?>
                                    <div shift="<?php echo $other['shift']['shift_id']; ?>" class="epark-shift-other epark-appoint <?php echo $cell_type; ?>" style="width: <?php echo $other['width']; ?>%; left: <?php echo $other['left']; ?>%;padding-left:  <?php echo $other['txt_left']; ?>%">
                                        <p><?php echo $cell_title; ?></p>
                                        <p>
                                            <?php echo substr($other['shift']['from_time'], 11, 5)."~".substr($other['shift']['to_time'], 11, 5); ?>
                                        </p>
                                    </div>
                                <?php } ?>

                                <?php foreach ($staff['orders'] as $order){
                                    $from_time = intval(substr($order['from_time'], 11, 2)) * 60 + intval(substr($order['from_time'], 14, 2));
                                    $to_time = intval(substr($order['to_time'], 11, 2)) * 60 + intval(substr($order['to_time'], 14, 2));
                                    $width = ($to_time - $from_time + $order['interval'])/($show_close_time - $show_open_time) * 100;
                                    $real_width = ($to_time - $from_time) /  ($to_time - $from_time + $order['interval']) * 100;
                                    $left = ($from_time-$show_open_time)/($show_close_time - $show_open_time) * 100;
                                    if ($from_time>$show_open_time){
                                        $txt_left = 0;
                                    }else{
                                        $txt_left = ($show_open_time-$from_time)/($show_close_time-$show_open_time)*100;
                                    }
                                    ?>
                                    <div order="<?php echo $order['id']; ?>" class="epark-order epark-appoint <?php echo $cell_type; ?> <?php if ($order['is_waiting']){ ?>waiting<?php } ?>" style="background-color:<?php echo $order['interval_color']; ?>; border-color: <?php echo $order['border_color']; ?>;  width: <?php echo $width; ?>%; left: <?php echo $left; ?>%; overflow: hidden; <?php if ($order['is_waiting']){ ?>opacity:0.5;<?php } ?>" length="<?php echo $to_time - $from_time+$order['interval']; ?>" from="<?php echo $from_time ?>"  to="<?php echo $to_time ?>">
                                        <?php include 'scheduler_reserve_obj.php'; ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if ($type_mode==3){ ?>
            <div class="booth_title">ブース</div>
        <?php } ?>
        <?php if ($type_mode!=1){ ?>
            <div class="booth_content">
                <div class="staffs">
                    <?php foreach ($tables as $table){ ?>
                        <div class="seat"><?php echo $table['table_name']; ?></div>
                    <?php } ?>
                </div>
                <div class="cell-list">
                    <?php foreach ($tables as $table){ ?>
                        <div position = "<?php echo $table['table_position']; ?>" class="cell-row table-cell<?php if ($time_mode==1) echo 'time_mode'; ?>">
                            <?php
                            $others = [];
                            for ($i=$show_open_time; $i<$show_close_time; $i+=5){
                                $h =  floor($i/60);
                                $m = $i % 60;
                                $m = $m<10 ? "0".$m : $m;
                                $cell_type = "none";
                                $isText = false;
                                if ($real_open_time<=$i && $real_close_time>$i){
                                    $cell_type="shift-apply";
                                }
                                ?>
                                <div class="cell <?php echo $cell_type; ?>" title="<?php echo $h.":".$m; ?>"></div>
                            <?php } ?>


                            <?php

                            if (!empty($table_orders) && !empty($table_orders[$table['table_position']])){

                                foreach ($table_orders[$table['table_position']] as $order){

                                $from_time = intval(substr($order['from_time'], 11, 2)) * 60 + intval(substr($order['from_time'], 14, 2));
                                $to_time = intval(substr($order['to_time'], 11, 2)) * 60 + intval(substr($order['to_time'], 14, 2));
                                $width = ($to_time - $from_time + $order['interval'])/($show_close_time - $show_open_time) * 100;
                                $real_width = ($to_time - $from_time) /  ($to_time - $from_time + $order['interval']) * 100;
                                $left = ($from_time-$show_open_time)/($show_close_time - $show_open_time) * 100;
                                if ($from_time>$show_open_time){
                                    $txt_left = 0;
                                }else{
                                    $txt_left = ($show_open_time-$from_time)/($show_close_time-$show_open_time)*100;
                                }
                                ?>
                                <div order="<?php echo $order['id']; ?>" class="epark-order epark-appoint <?php echo $cell_type; ?> <?php if ($order['is_waiting']){ ?>waiting<?php } ?>" style="background-color:<?php echo $order['interval_color']; ?>; border-color: <?php echo $order['border_color']; ?>;  width: <?php echo $width; ?>%; left: <?php echo $left; ?>%; overflow: hidden; <?php if ($order['is_waiting']){ ?>opacity:0.5;<?php } ?>" length="<?php echo $to_time - $from_time+$order['interval']; ?>" from="<?php echo $from_time ?>" to="<?php echo $to_time ?>">
                                    <?php include 'scheduler_reserve_obj.php'; ?>
                                </div>
                            <?php } } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <input type="hidden" id="sel_time" name="sel_time" value="<?php echo $sel_time; ?>" />

    <input type="hidden" id="line_from" value="<?php echo $show_open_time; ?>" />
    <input type="hidden" id="line_to" value="<?php echo $show_close_time; ?>" />

    <input type="hidden" id="organ_from_time" value="<?php echo $real_open_time; ?>" />
    <input type="hidden" id="organ_to_time" value="<?php echo $real_close_time; ?>" />


    <!--    <script src="--><?php //echo base_url(); ?><!--assets/js/jquery.min.js"></script>-->
    <script src="<?php echo base_url(); ?>assets/jquery-ui-1.13.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
<!--    <script src="--><?php //echo base_url(); ?><!--assets/kiaalap/js/bootstrap.min.js"></script>-->

    <script src="<?php echo base_url(); ?>assets/js/epark/scheduler-drag.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/epark/scheduler-ajax-content-event.js"></script>
