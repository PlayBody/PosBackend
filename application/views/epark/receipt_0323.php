<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>

<div class="page-title-box">
    <form method="post">
        <div class="row">
            <div class="button-wrap col-md-2" style="width: 190px;">
                <button id="prev_select_date" type="button" class="btn btn-default outline-btn"> < </button>
                <button id="today_select_date" type="button" class="btn btn-default outline-btn"> 今日 </button>
                <button id="next_select_date" type="button" class="btn btn-default outline-btn"> > </button>
            </div>
            <div class="col-md-2" style="width: 180px;">
                <div class="input-group">
                    <input  type="text" name="select_date" class="form-control" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" id="receipt_date" value="<?php echo $select_date; ?>" onchange="form.submit();">
                    <span class="input-group-addon b-0 text-white"><i class="icon-calender"></i></span>
                </div>
            </div>
            <div class="button-wrap col-md-2">
                <button type="submit" class="btn btn-warning"><i class="icon-refresh"></i></button>
            </div>
            <div class="button-wrap" style="float: right; display: flex;">
                <select class="form-control m-b-15" name="organ_id" onchange="form.submit()">
                    <?php foreach ($organs as $organ){ ?>
                    <option <?php if($organ['organ_id']==$organ_id){ echo 'selected'; }?> value="<?php echo $organ['organ_id']; ?>"><?php echo $organ['organ_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <input type="hidden" name = 'mod' id="mod" value="<?php echo $mod; ?>" />
    </form>
</div>
<div class="epark_content" style="display: flex;">
    <div style="width: 45px; background-color: #ebe9e9;">
        <ul class="left_nav">
            <li nav_id="left_reserve" class="nav_item"><div>予約</div><i class="fa fa-clock-o fa-2x"></i></li>
            <li nav_id="left_close" class="nav_item"><div>閉じる</div><i class="fa fa-arrow-left fa-2x"></i></li>
<!--            <li class="nav_item"><div>返客</div><i class="fa fa-reply fa-2x"></i></li>-->
            <li class="nav_item"><div>会計</div><i class="fa  fa-shopping-cart fa-2x"></i></li>
<!--            <li nav_id="left_user" class="nav_item"><div>顧客</div><i class="fa fa-user fa-2x"></i></li>-->
<!--            <li class="nav_item"><div>カード</div><i class="fa fa-credit-card fa-2x"></i></li>-->
<!--            <li class="nav_item"><div>失効</div><i class="fa fa-warning fa-2x"></i></li>-->
        </ul>
    </div>
    <div id="receipt_left_panel" style="width: 280px; background-color: #d38751; padding: 8px 10px;display: none; color: #333333; font-size: 12px;" >
        <div class="left_search_pannel">
            <div>
                <input id="left_search_name" type="text"  class="form-control" style="border-radius: 4px;"/>
            </div>
            <div style="display: table; width: 100%; margin-top: 8px;">
                <div style="float: left;">
                    <input type="checkbox" />
                    <span style="color: white">自店のみ</span>
                </div>
                <div style="float: right;">
                    <input id="left_panel_search" class="btn_search" type="button" value="検索"/>
                </div>
            </div>
            <div style="height: 24px;"></div>
        </div>
        <?php include 'receipt_left_reserves.php'; ?>
    </div>
    <div class="content" style="    flex: 1 0 0%;">
        <div class="full_button_group m-b-10">
            <?php for($i=$available_time_from; $i<=$available_time_to; $i++){ ?>
                <div>
                    <span><?php echo $reserve_counts[$i]; ?></span>
                    <button type="button" ><?php echo $i; ?></button>
                </div>
            <?php } ?>
        </div>

        <div class="m-b-10" style="display: flex;">
            <div class="btn-group" style="display: contents;">
                <button type="button" class="btn btn-white">基本</button>
                <button type="button" class="btn btn-warning">全体</button>
            </div>
            <div style="width: 12px;"></div>
            <div class="btn-group" style="display: contents;">
                <button type="button" class="btn <?php echo $mod=='shift' ? 'btn-warning' : 'btn-white'; ?>" onclick="change_view_mod('shift')">シフト</button>
                <button type="button" class="btn <?php echo $mod=='table' ? 'btn-warning' : 'btn-white'; ?>" onclick="change_view_mod('table')">ブース</button>
                <button type="button" class="btn <?php echo $mod=='both' ? 'btn-warning' : 'btn-white'; ?>" onclick="change_view_mod('both')">両方</button>
            </div>
            <div style="width: 12px;"></div>
            <div class="move-other-date" style="margin 0 12px; border:1px dashed #9e9e9e;background-color: white;padding: 6px 12px; width: 100%; text-align: center;">
                <div class="move-over-show" style="display:none; width:150px; border:solid #000 2px; text-align:left; padding: 0 8px; background-color:grey; color:white;">
                    test test<br />
                    2022-10-17 10:00~<br />
                    指名
                </div>
                <div class="comment" style="display:flex;">
                    <?php if ($waitings){ ?>
                        <?php foreach ($waitings as $waiting){ ?>
                            <div data="<?php echo $waiting['id']; ?>" time_length="<?php echo  $waiting['width']; ?>" user_id="<?php echo $waiting['user_id']; ?>" class="reserve_item_<?php echo $waiting['id']; ?> reserve_item_main <?php
                                if($waiting['is_before']){
                                    echo 'reserve-before';
                                }else{
                                    if($waiting['status']==ORDER_STATUS_RESERVE_REQUEST){
                                        echo 'reserve-request';
                                    }elseif($waiting['status']==ORDER_STATUS_RESERVE_APPLY){
                                        echo 'reserve-apply';
                                    }elseif($waiting['status']==ORDER_STATUS_TABLE_END || $waiting['status']==ORDER_STATUS_TABLE_START){
                                        echo 'reserve-now';
                                    }elseif($waiting['status']==ORDER_STATUS_TABLE_COMPLETE){
                                        echo 'reserve-before';
                                    }
                                }
                            ?>" style="text-align:left;  width: <?php echo $one_minute_length * ($waiting['width']+$waiting['interval']); ?>%; height: 50px; display: flex; margin-right:20px; background-color:<?php echo $waiting['is_before'] ? 'grey' : $waiting['color']; ?>;" 
                            data-name="<?php echo $waiting['user_name']; ?>" data-time="<?php echo $waiting['from'].'~'.$waiting['to']; ?>" >
                                <div class="reserve_item" style="width:<?php echo  $waiting['width']/($waiting['width']+$waiting['interval'])*100; ?>%; background-color:<?php echo $waiting['is_before'] ? 'grey' : $waiting['color']; ?>; filter:brightness(80%);">
                                    <div class="reserve_user">
                                        <span class="sex <?php echo $waiting['user_sex']==1 ? 'sex_1' : 'sex2'; ?>"><?php echo $waiting['user_sex']==1 ? '男' : '女'; ?></span>
                                        <span><?php echo $waiting['user_name']; ?></span>
                                    </div>
                                    <div class="reserve_time">
                                        <span><?php echo $waiting['from'].'~'.$waiting['to']; ?></span>
                                    </div>

                                    <div class="sel_staff">
                                        <?php if($waiting['select_staff_type']==3){ ?>
                                            <span class="sel_staff_mark_red">指名</span>
                                        <?php }else if($waiting['select_staff_type']==1 ){ ?>
                                            <span class="sel_staff_mark_blue">希望</span>
                                        <?php }else if($waiting['select_staff_type']==2 ){ ?>
                                            <span class="sel_staff_mark_red">希望</span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php }else{ ?>
                        <div style="width:100%; text-align:center;">日付をまたいで予定を変更したい場合は、一旦このエリアに置いてください。</div>
                    <?php } ?>

                </div>
            </div>
            <div style="width: 12px;"></div>
            <button id="show_widget_shift_add_btn" type="button" class="btn btn-white">シフト追加</button>
        </div>

        <div class="epark_content_view" style="background-color: #9e9e9e;" >
            <div class="epark_content_row" style="display: flex; padding:4px 0;">
                <div style="width: 180px;"></div>
                <div style="width: 100%; display: flex;">

                    <?php for($i=$available_time_from; $i<=$available_time_to; $i++){ ?>
                        <div style="width: 10%;">
                            <div style="background-color: #5e5e5e;color:white; border-radius: 20px;display: inline-block; width: 28px; height: 28px;line-height:28px;text-align: center;">
                                <?php echo $i; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php if($mod=='both'){ ?>
                <div class="shift_item_title">シフト</div>
            <?php } ?>

            <?php if($mod!='table'){ ?>
            <?php foreach ($staffs as $staff_id => $staff){ ?>

                <div class="epark_content_row" style="display: flex;">

                    <div data="<?php echo $staff_id; ?>" class="staff_drag_item <?php echo $staff['sex']==1 ? 'staff_sex_1' : 'staff_sex_2'; ?>">
                        <div style="width: 100%;"><?php echo $staff['name']; ?></div>
                        <i class="icon-lock"></i>
                    </div>

                    <div style="width: 100%; display: flex;position:relative; background-color: #b5b5b5;">
                        <div class="row_move_over" style="color: white; font-size: 20px; padding-top: 8px; width: 100px; background-color: grey;position: absolute; left: 50%; top: 3px; bottom: 3px; display: none; z-index: 99994">
                        </div>
                        <div class="" style="left: <?php echo $one_minute_length * ($table_start_time-$available_time_from*60); ?>%;  width: <?php echo $one_minute_length * $table_length; ?>%; height: 50px; display: flex; position: absolute; border-right: solid #a3a3a3 1px; background-color: #cdcdcd;">
                            <?php for($t = $table_start_time; $t<$table_start_time + $table_length; $t+=5){ ?>
                                <div style="width: <?php echo 5/$table_length*100; ?>%;" class="time_element <?php if ($t%60==0){ ?>time_border_left_2<?php  }elseif ($t%10==0){  ?>time_border_left<?php } ?>" title="<?php echo intval($t/60).':'.$t%60; ?>"></div>
                            <?php } ?>
                        </div>
                        <?php if (!empty($staff['shifts']))
                            foreach ($staff['shifts'] as $shift) { ?>
                                <?php if ($shift['shift_type']==SHIFT_STATUS_REST) {
                                    $shift['start'] = $table_start_time;
                                    $shift['width'] = $table_length;
                                } ?>

                                <div data="<?php echo $shift['shift_id']; ?>" class="div_shift <?php
                                if($is_lock){
                                    if($shift['shift_type']==SHIFT_STATUS_APPLY || $shift['shift_type']==SHIFT_STATUS_ME_APPLY){
                                        echo 'shift-apply';
                                    }else {
                                        echo 'shift-grey';
                                    }

                                }else{
                                    if($shift['shift_type']==SHIFT_STATUS_SUBMIT){ echo 'shift-submit'; }
                                    if($shift['shift_type']==SHIFT_STATUS_REJECT){ echo 'shift-reject'; }
                                    if($shift['shift_type']==SHIFT_STATUS_OUT){ echo 'shift-out'; }
                                    if($shift['shift_type']==SHIFT_STATUS_REST){ echo 'shift-rest'; }
                                    if($shift['shift_type']==SHIFT_STATUS_REQUEST){ echo 'shift-request'; }
                                    if($shift['shift_type']==SHIFT_STATUS_ME_REPLY){ echo 'shift-reply'; }
                                    if($shift['shift_type']==SHIFT_STATUS_ME_APPLY){ echo 'shift-apply'; }
                                    if($shift['shift_type']==SHIFT_STATUS_APPLY){ echo 'shift-apply'; }

                                }
                                ?>" style="left: <?php echo $one_minute_length * ($shift['start']-$available_time_from*60); ?>%;  width: <?php echo $one_minute_length * $shift['width']; ?>%; height: 50px; display: flex; position: absolute;">
                                    <?php if($shift['shift_type']==SHIFT_STATUS_ME_APPLY || $shift['shift_type']==SHIFT_STATUS_APPLY  || $shift['shift_type']==SHIFT_STATUS_OUT){ ?>
                                        <?php for($t = $shift['start']; $t<$shift['start'] + $shift['width']; $t+=5){ ?>
                                            <div style="width: <?php echo 5/$shift['width']*100; ?>%;" data="<?php echo $t; ?>" staff_id="<?php echo $staff_id; ?>" class="apply_shift_element time_element <?php if ($t%60==0){ ?>time_border_left_2<?php  }elseif ($t%10==0){  ?>time_border_left<?php } ?>" time="<?php echo $t; ?>" title="<?php echo intval($t/60).':'.$t%60; ?>"></div>
                                        <?php } ?>
                                        <!-- <?php
                                            if($shift['shift_type']==SHIFT_STATUS_OUT){
                                        ?>
                                        <div style="color: white; padding: 2px; position:absolute;">
                                            <span>
                                                <?php
                                                    if($shift['shift_type']==SHIFT_STATUS_OUT){ echo '店外待機'; }
                                                ?>
                                            </span>
                                            <p>
                                                <?php echo date("H:i", strtotime($shift['from_time'])) .
                                                '~' . date("H:i", strtotime($shift['to_time'])); ?>
                                            </p>
                                        </div>
                                        <?php } ?> -->
                                    <?php }else{ ?>
                                        <div style="color: white; padding: 2px;">
                                            <span>
                                                <?php
                                                    if($shift['shift_type']==SHIFT_STATUS_SUBMIT){ echo '申請中'; }
                                                    if($shift['shift_type']==SHIFT_STATUS_REJECT){ echo '拒否'; }
                                                    if($shift['shift_type']==SHIFT_STATUS_OUT){ echo '店外待機'; }
                                                    if($shift['shift_type']==SHIFT_STATUS_REST){ echo '休み'; }
                                                    if($shift['shift_type']==SHIFT_STATUS_REQUEST){ echo '出勤依頼'; }
                                                    if($shift['shift_type']==SHIFT_STATUS_ME_REPLY){ echo '承認待ち'; }
                                                ?>
                                            </span>
                                            <p>
                                                <?php echo date("H:i", strtotime($shift['from_time'])) .
                                                '~' . date("H:i", strtotime($shift['to_time'])); ?>
                                            </p>
                                        </div>
                                    <?php } ?>
                                </div>
                        <?php } ?>
                        <?php if (!empty($staff['reserves']))
                            foreach ($staff['reserves'] as $reserve) {
                                if ($reserve['is_waiting']) continue;
                                if ($reserve['status']==ORDER_STATUS_RESERVE_CANCEL || $reserve['status']==ORDER_STATUS_RESERVE_REJECT) continue;
                                ?>

                            <div data="<?php echo $reserve['id']; ?>" time_length="<?php echo  $reserve['width']; ?>" user_id="<?php echo $reserve['user_id']; ?>" class="reserve_item_<?php echo $reserve['id']; ?> reserve_item_main <?php
                                if($reserve['is_before']){
                                    echo 'reserve-before';
                                }else{
                                    if($reserve['status']==ORDER_STATUS_RESERVE_REQUEST){
                                        echo 'reserve-request';
                                    }elseif($reserve['status']==ORDER_STATUS_RESERVE_APPLY){
                                        echo 'reserve-apply';
                                    }elseif($reserve['status']==ORDER_STATUS_TABLE_END || $reserve['status']==ORDER_STATUS_TABLE_START){
                                        echo 'reserve-now';
                                    }elseif($reserve['status']==ORDER_STATUS_TABLE_COMPLETE){
                                        echo 'reserve-before';
                                    }
                                }
                            ?>" style="left: <?php echo $one_minute_length * ($reserve['start']-$available_time_from*60); ?>%;  width: <?php echo $one_minute_length * ($reserve['width']+$reserve['interval']); ?>%; height: 50px; display: flex; position: absolute; background-color:<?php echo $reserve['is_before'] ? 'grey' : $reserve['color']; ?>;" 
                            data-name="<?php echo $reserve['user_name']; ?>" data-time="<?php echo $reserve['from'].'~'.$reserve['to']; ?>" >
                                <div class="reserve_item" style="width:<?php echo  $reserve['width']/($reserve['width']+$reserve['interval'])*100; ?>%; background-color:<?php echo $reserve['is_before'] ? 'grey' : $reserve['color']; ?>; filter:brightness(80%);">
                                    <div class="reserve_user">
                                        <span class="sex <?php echo $reserve['user_sex']==1 ? 'sex_1' : 'sex2'; ?>"><?php echo $reserve['user_sex']==1 ? '男' : '女'; ?></span>
                                        <span><?php echo $reserve['user_name']; ?></span>
                                    </div>
                                    <div class="reserve_time">
                                        <span><?php echo $reserve['from'].'~'.$reserve['to']; ?></span>
                                    </div>

                                    <div class="sel_staff">
                                        <?php if($reserve['select_staff_type']==3){ ?>
                                            <span class="sel_staff_mark_red">指名</span>
                                        <?php }else if($reserve['select_staff_type']==1 ){ ?>
                                            <span class="sel_staff_mark_blue">希望</span>
                                        <?php }else if($reserve['select_staff_type']==2 ){ ?>
                                            <span class="sel_staff_mark_red">希望</span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <?php }
            if($mod != 'shift'){ ?>
            <?php if($mod=='both'){ ?>
                <div class="shift_item_title">ブース</div>
            <?php } ?>
            <?php foreach ($tables as $table){ ?>
                <div class="epark_content_row" style="display: flex;">

                    <div class="staff_drag_item">
                        <div style="width: 100%;"><?php echo $table['table_name']; ?></div>
                        <i class="icon-lock"></i>
                    </div>

                    <div style="width: 100%; display: flex;position:relative; background-color: #b5b5b5;">
                        <div class="" style="left: <?php echo $one_minute_length * ($table_start_time-$available_time_from*60); ?>%;  width: <?php echo $one_minute_length * $table_length; ?>%; height: 50px; display: flex; position: absolute; border-right: solid #a3a3a3 1px; background-color: white;">
                            <?php for($t = $table_start_time; $t<$table_start_time + $table_length; $t+=5){ ?>
                                <div style="width: <?php echo 5/$table_length*100; ?>%;" class="time_element <?php if ($t%60==0){ ?>time_border_left_2<?php  }elseif ($t%10==0){  ?>time_border_left<?php } ?>" title="<?php echo intval($t/60).':'.$t%60; ?>"></div>
                            <?php } ?>
                        </div>
                        <?php if (!empty($table['data']))
                            foreach ($table['data'] as $reserve) {
                                if ($reserve['is_waiting']) continue;
                                if ($reserve['status']==ORDER_STATUS_RESERVE_CANCEL || $reserve['status']==ORDER_STATUS_RESERVE_REJECT) continue;
                                ?>
                                <div data="<?php echo $reserve['id']; ?>" time_length="<?php echo  $reserve['width']; ?>" user_id="<?php echo $reserve['user_id']; ?>" class="reserve_item_<?php echo $reserve['id']; ?> reserve_item_main <?php
                                if($reserve['is_before']){
                                    echo 'reserve-before';
                                }else{
                                    if($reserve['status']==ORDER_STATUS_RESERVE_REQUEST){
                                        echo 'reserve-request';
                                    }elseif($reserve['status']==ORDER_STATUS_RESERVE_APPLY){
                                        echo 'reserve-apply';
                                    }elseif($reserve['status']==ORDER_STATUS_TABLE_END || $reserve['status']==ORDER_STATUS_TABLE_START){
                                        echo 'reserve-now';
                                    }elseif($reserve['status']==ORDER_STATUS_TABLE_COMPLETE){
                                        echo 'reserve-before';
                                    }
                                }
                                ?>" style="left: <?php echo $one_minute_length * ($reserve['start']-$available_time_from*60); ?>%;  width: <?php echo $one_minute_length * ($reserve['width']+$reserve['interval']); ?>%; height: 50px; display: flex; position: absolute;">
                                    <div class="reserve_item" style="width:<?php echo  $reserve['width']/($reserve['width']+$reserve['interval'])*100; ?>%">
                                        <div class="reserve_user">
                                            <span class="sex <?php echo $reserve['user_sex']==1 ? 'sex_1' : 'sex2'; ?>"><?php echo $reserve['user_sex']==1 ? '男' : '女'; ?></span>
                                            <span><?php echo $reserve['user_name']; ?></span>
                                        </div>
                                        <div class="reserve_time">
                                            <span><?php echo $reserve['from'].'~'.$reserve['to']; ?></span>
                                        </div>
                                        <div class="sel_staff">
                                            <?php if($reserve['staff_sex']==1){ ?>
                                                <span class="sel_staff_mark_blue">希望</span>
                                            <?php }else if($reserve['staff_sex']==2){ ?>
                                                <span class="sel_staff_mark_red">希望</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <?php } ?>
        </div>
        <div id="sum_view">
            <div class="sum_view_row red">
                <span>目標：71,534円</span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span>総販売額：60,730円</span>
            </div>
            <div class="sum_view_row">
                <span>新規：4名</span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span>リピート：12名 </span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span>総来店人数：16名 </span>
            </div>
            <div class="sum_view_row blue">
                <span>施術：<?php echo number_format($service_complete_amount, 0); ?>円/<?php echo number_format($service_complete_amount, 0); ?>円</span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span> 物販：<?php echo number_format($goods_complete_amount, 0); ?>円/<?php echo number_format($goods_complete_amount, 0); ?>円</span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span> 回数券販売：0円/0円</span>
            </div>
            <div class="sum_view_row">
                <span>現金：<?php echo number_format($cash_complete_amount, 0); ?>円 </span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span>クレジット：<?php echo number_format($credit_complete_amount, 0); ?>円</span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span>回数券利用：0円 </span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span>商品券利用：0円</span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <span>電子マネー：<?php echo number_format($other_complete_amount, 0); ?>円 </span>
            </div>
        </div>
    </div>
</div>

<div id="widget_back" style="position: absolute; width: 100%; height: 100vh; background-color: black;  top:0px; left: 0px;z-index: 1000; opacity: 0.5; display: none;">
</div>

<?php include 'new_shift.php'; ?>
<?php include 'new_reserve.php'; ?>
<?php include 'reserve_move_confirm.php'; ?>

<style>
    #sum_view{
        width: 100%; background-color: white;margin-top: 40px; border-radius: 3px; padding:8px 20px;
    }
    .sum_view_row{
        display: flex; color: black; font-size: 18px; font-weight: bold;padding-top: 8px;
    }
    .red{
        color: red;
    }
    .blue{
        color: blue;
    }

    .btn_search{
        background-color: #fc5654;
        border: solid #fc5654 1px;
        border-radius: 4px;
        padding: 2px 8px;
        color: white;
    }
    .btn_search:hover{
        background-color: #FC716C;
    }

    .btn_grey{
        background-color: #efefef;
        border: solid #333333 1px;
        border-radius: 4px;
        padding: 4px 8px;
        color: #fc5654;
    }
    .btn_grey:hover{
        background-color: #ffbd4a;
    }
    .btn_red{
        background-color: #fc5654;
        border: solid #fc5654 1px;
        border-radius: 4px;
        padding: 4px 8px;
        color: white;
    }
    .btn_red:hover{
        background-color: #FC716C;
    }
    .staff_drag_item{
        width: 180px; background-color: white;
        height: 50px; display: flex;
        padding: 0 12px;
        align-items: center;
        cursor: pointer;
    }

    .staff_sex_1{ border-left: solid #6eade9 6px; }
    .staff_sex_2{ border-left: solid #ffa5a5 6px; }

    .time_element{
        height : 50px;
        box-sizing: content-box;
    }

    .time_element:hover{
        background-color: rgba(252, 86, 84, 0.4);
    }
    .time_border_left{
        border-left:solid #a3a3a3 1px;
    }
    .time_border_left_2{
        border-left:solid #a3a3a3 3px;
    }
    .div_shift{border: solid #333333 1px; cursor: pointer;}
    .shift-submit{ background-color:#1f5f9d; }
    .shift-apply{ background-color:white; }
    .shift-reject{ background-color:#a14040; }
    .shift-out{ background-color:#d325a5; }
    .shift-grey{ background-color:#b9b9b9; }
    .shift-reply{ background-color:#d87b1b; }
    .shift-request{ background-color:#20a379; }
    .shift-rest{ background-color:#af9462; }

    .reserve-request:hover, .reserve-before:hover, .reserve-apply:hover{ border:solid #333333 3px; cursor: move;}
    .reserve_item_main.active{ border:solid #333333 3px; cursor: move;}
    .reserve-request{ background-color:#a4c19b; border:solid #33cc00 1px; }
    .reserve-request>div{ background-color: #6db357; color: white;}
    .reserve-apply{ background-color:#a5a5c5; border:solid #7171c5 1px; }
    .reserve-apply>div{ background-color: #8e8ebf; color: black;}
    .reserve-before{ background-color:#c1c1c1; border:solid #757575 1px; }
    .reserve-before>div{ background-color: #9d9d9d; color: black;}
    .reserve-now{ background-color:#ffdea2; border:solid #757575 1px; }
    .reserve-now>div{ background-color: #d7a242; color: black;}

    .reserve_item{padding: 1px;}
    .reserve_item div, .reserve_item span{ overflow:hidden; white-space: nowrap; font-size: 12px; line-height: 16px; color: #333333;}
    .reserve_item .sex { color: white; padding: 1px 2px;border-radius: 3px;}
    .reserve_item .sex_1 { background-color: #348add;}
    .reserve_item .sex2 { background-color: #954a4a;}
    .reserve_item .sel_staff_mark_blue{color: white; background-color: #3579cf; padding: 0px 1px;border-radius: 3px;}
    .reserve_item .sel_staff_mark_red{color: white; background-color: #d93790; padding: 0px 1px;border-radius: 3px;}
    .reserve-before .reserve_item span{ color: #eeeeee;}
    .reserve-before .sex, .reserve-before .sel_staff_mark{background-color: grey;}

    .shift_item_title{ color: #333333;padding: 4px 12px;background-color: #b5b5b5;border-bottom: solid #9e9e9e 1px;}
    /*.staff_drag_item:hover{*/
    /*    background-color: grey;*/
    /*}*/

    .comment_label{background-color: #f5eacc; padding: 4px;}
    .comment_content{     padding: 22px 4px;font-size: 16px;font-weight: bold; align-items: center;}

    .widget_dialog{

        color: black;
        position: absolute;
        width: 600px;
        /*height: 330px;*/
        background-color: white;
        right:400px;
        top:300px;
        z-index: 1000;
        border: solid #d79500 3px;
    }
    .widget_dialog .title{
        font-size: 20px;
        font-weight: bold;
        padding: 12px;
        background-color: #ffe19c;
        /*margin: 2px;*/
        border-radius: 6px;
        display: table;
        width: 100%;
        align-items: center;
    }

    .widget_dialog .row{
        margin:2px 20px;
    }
    .widget_dialog .row .col-md-4{
        padding: 0px;
    }

    .widget_dialog .label{
        line-height: 32px;
        color: #000;
        font-size: 14px;
        font-weight: bold;
    }


</style>
<script>

    const base_url = "<?php echo base_url(); ?>";
    const organ_from_hour = "<?php echo $available_time_from; ?>";
    const organ_to_hour = "<?php echo $available_time_to; ?>";
    drop_staff = '';
    function change_view_mod(mod){
        $('#mod').val(mod);
        $('form').submit();
    }

    isDrop = false;
    isReserveDrop = false;
    $(function() {
        $('#receipt_date').datepicker({
            dateFormat: 'yy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
        $('.staff_drag_item').draggable({
            start: function(event, ui) {
                console.log('dragstart');
                $(this).css('z-index', 99999);
            },
            stop: function(event, ui) {
                if(!isDrop) {
                    $(this).css('left', ui.originalPosition.left);
                    $(this).css('top', ui.originalPosition.top);
                }
                if(isDrop){
                    $.ajax({
                        url: base_url + "api/exchangeSort",
                        type: 'post',
                        data : {'staff_id' : "<?php echo $this->staff['staff_id']; ?>",
                            'move_staff' : $(this).attr('data'),
                            'target_staff' : drop_staff
                        },
                        context: document.body
                    }).done(function(res) {
                        data = JSON.parse(res);
                        if(data['isSave']){
                            refresh_load();
                        }else{
                            alert(data['message']);
                        }
                    });
                }
                isDrop = false;
            }
        });


        $( ".staff_drag_item" ).droppable({
            drop: function( event, ui ) {
                isDrop = true;
                drop_staff = $(this).attr('data');
            }
        });
    });

    $('.reserve_item_main').mouseover(function (e){
        data = $(this).attr('data');
        $('.reserve_item_'+data).addClass('active');
    });
    $('.reserve_item_main').mouseout(function (e){
        data = $(this).attr('data');
        $('.reserve_item_'+data).removeClass('active');
    });

    $('.epark_content .left_nav .nav_item').click(function(e){
        var nav_id = $(this).attr('nav_id');
        if(nav_id=='left_reserve'){
            $('#receipt_left_panel').show();
            $('.reserve_tab_reserve_info').trigger('click');
            return;
        }
        var nav_id = $(this).attr('nav_id');
        if(nav_id=='left_close'){
            $('#receipt_left_panel').hide();
            return;
        }
        if(nav_id=='left_user'){
            $('#receipt_left_panel').show();
            user_add();
            return;
        }
    });


    $('.div_shift').click(function(e){
        var shift_id = $(this).attr('data');
        $('#receipt_left_panel').show();
        shift_detail(shift_id);
    });

    $('#show_widget_shift_add_btn').click(function (e){
        $('#widget_back').toggle();
        $('#widget_shift_add').toggle();
    });
    $('.btn_close_widget').click(function (e){
        $('#widget_back').hide();
        $('.widget_dialog').hide();
    });

    $('#today_select_date').click(function (e){
        _date =  new Date();
        year = _date.getFullYear();
        month = _date.getMonth()+1;
        date = _date.getDate();
        select_date = year+'-'+(month<10?'0'+month:month)+'-'+(date<10?'0'+date:date);
        $("[name='select_date']").val(select_date);
        refresh_load();
    });

    $('#prev_select_date').click(function (e){
        _date =  new Date($("[name='select_date']").val());
        _date.setDate(_date.getDate() - 1);
        year = _date.getFullYear();
        month = _date.getMonth()+1;
        date = _date.getDate();
        select_date = year+'-'+(month<10?'0'+month:month)+'-'+(date<10?'0'+date:date);
        $("[name='select_date']").val(select_date);
        refresh_load();
    });

    $('#next_select_date').click(function (e){
        _date =  new Date($("[name='select_date']").val());
        _date.setDate(_date.getDate() + 1);
        year = _date.getFullYear();
        month = _date.getMonth()+1;
        date = _date.getDate();
        select_date = year+'-'+(month<10?'0'+month:month)+'-'+(date<10?'0'+date:date);
        $("[name='select_date']").val(select_date);
        refresh_load();
    });

    $('.apply_shift_element').click(function(e){
       time_value = $(this).attr('data');
       hour = parseInt(time_value/60);
       min = time_value % 60;
       from_time = '<?php echo $select_date; ?>';
       from_time += ' ' + (hour<10 ? '0'+hour : hour) + ':' + (min<10 ? '0'+min : min) + ':00';


        $('#widget_reserve_add #reserve_add_from_time').html(from_time);
        $('#widget_reserve_add #reserve_add_staff_id').val($(this).attr('staff_id'));
        $('#widget_back').toggle();
        $('#widget_reserve_add').toggle();
    });

    $('.reserve_item_main').mouseup(function(e){
        if (isDragReserve) return;
        $('#receipt_left_panel').show();
        reserve_id = $(this).attr('data');
        user_id = $(this).attr('user_id');
        user_detail(user_id, 'reserve_detail', reserve_id);
    })

    function refresh_load() {
        $('form').submit();
    }

    var reserve_move_obj;
    var old_reserve_move_obj;
    var objWidth;
    var time_length;
    var move_from_time;
    var isDragReserve = false;

    var select_reserve_staff_id;
    var select_reserve_from;
    var select_reserve_id;
    var select_reserve_time_length;
    var select_reserve_obj_original_left;
    var select_reserve_obj_original_top;
    var select_reserve_obj;


    $('.reserve_item_main').draggable({
        start: function(event, ui) {
            isDragReserve = true;
            $(this).css('z-index', 99999);
            $(this).css('opacity', 0.2);
            select_reserve_obj = $(this);
            objWidth = $(this).css('width');
            time_length = $(this).attr('time_length');
        },
        stop: function(event, ui) {
            $('.row_move_over').hide();
            if(!isReserveDrop) {
                if (isOtherMove){
                    
                    $('.move-other-date>.move-over-show').hide();
                    $.ajax({
                       url: base_url + "epark/receipt/ajaxMoveReserveWaiting",
                       type: 'post',
                       data : {
                           'order_id' : select_reserve_obj.attr('data'),
                           'is_wait' : 1,
                       },
                    }).done(function(res) {
                       data = JSON.parse(res);
                       if(data['isUpdate']){
                           refresh_load();
                       }else{
                         alert('選択した時間には予約できません。');
                           $(this).css('left', ui.originalPosition.left);
                           $(this).css('top', ui.originalPosition.top);
                       }
                    });
                }else{
                    $(this).css('left', ui.originalPosition.left);
                    $(this).css('top', ui.originalPosition.top);
                    $('.move-other-date>.move-over-show').hide();
                    $('.move-other-date>.comment').show();
                }
            }
            if(isReserveDrop){
                var strText = '開始時間を'+move_from_time+'に変更になりますか、よろしいですか？';
                $('#reserve_confirm_message').html(strText);
                $('#widget_back').show();
                $('#widget_reserve_move_confirm').show();

                select_reserve_obj_original_left = ui.originalPosition.left;
                select_reserve_obj_original_top = ui.originalPosition.top;
                select_reserve_obj = $(this);

                select_reserve_staff_id = reserve_move_obj.parent().parent().children('.staff_drag_item').attr('data');
                select_reserve_from = '<?php echo $select_date; ?>'+' ' + move_from_time+':00';
                select_reserve_id = $(this).attr('data');
                select_reserve_time_length = time_length;

                //$.ajax({
                //    url: base_url + "epark/receipt/ajaxUpdateReserveTime",
                //    type: 'post',
                //    data : {
                //        'staff_id' : reserve_move_obj.parent().parent().children('.staff_drag_item').attr('data'),
                //        'reserve_time' : '<?php //echo $select_date; ?>//'+' ' + move_from_time+':00',
                //        'reserve_id' : $(this).attr('data'),
                //        'time_length' : time_length
                //    },
                //}).done(function(res) {
                //    data = JSON.parse(res);
                //    if(data['isSave']){
                //        refresh_load();
                //    }else{
                //      alert('選択した時間には予約できません。');
                //        $(this).css('left', ui.originalPosition.left);
                //        $(this).css('top', ui.originalPosition.top);
                //    }
                //});
            }
            isReserveDrop = false;
            isDragReserve = false;
            isOtherMove = false;
            $(this).css('z-index', 99990);
            $(this).css('opacity', 1);

        }
    });

    $( ".apply_shift_element" ).droppable({
        drop: function( event, ui ) {
            if (!isDragReserve) return;
            isReserveDrop = true;
        },
        over: function(event, ui){
            if (!isDragReserve) return;
            _calcDiffTime = parseInt(time_length/2/5)*5;
            time = $(this).attr('time') - _calcDiffTime;

            one_time_length = '<?php echo $one_minute_length; ?>';
            start_time = '<?php echo $available_time_from; ?>';
            left = one_time_length * (time-start_time*60);

            move_from_time = (parseInt(time/60)<10?'0'+parseInt(time/60):parseInt(time/60)) +':' + (time%60<10 ? '0'+time%60 : time%60);
            old_reserve_move_obj = reserve_move_obj;
            $('.row_move_over').hide();
            reserve_move_obj = $(this).parent().parent().children('.row_move_over');
            reserve_move_obj.html(move_from_time)
            reserve_move_obj.css('width', objWidth);
            reserve_move_obj.css('left', left+'%');
            console.log(left+'%');
            reserve_move_obj.show();
        },
    });

    $( ".move-other-date" ).droppable({
        drop: function( event, ui ) {
            if (!isDragReserve) return;
            isOtherMove = true;
        },
        over: function(event, ui){
            console.log(ui)
            if (!isDragReserve) return;
            $('.row_move_over').hide();
            $('.move-other-date>.move-over-show').html(select_reserve_obj.data('name') + '<br />'+select_reserve_obj.data('time'));
            $('.move-other-date>.comment').hide();
            $('.move-other-date>.move-over-show').css('display', 'table');
        },
        out : function(event, ui){
            if (!isDragReserve) return;
            $('.move-other-date>.move-over-show').hide();
            $('.move-other-date>.comment').show();
        },
    });
</script>

