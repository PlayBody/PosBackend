
<?php
    $from_hour =  substr($shift['from_time'], 11, 2);
    $from_min =  substr($shift['from_time'], 14, 2);
    $to_hour =  substr($shift['to_time'], 11, 2);
    $to_min =  substr($shift['to_time'], 14, 2);

    $organ_from_time = floor($organ_from_time / 60);
    $organ_to_time = $organ_to_time % 60 > 0 ? floor($organ_to_time / 60) + 1 : floor($organ_to_time / 60);

?>
<p><?php echo substr($shift['create_date'], 0, 16); ?> 登録</p>
<h3>スタッフ名：</h3>
<p><?php echo $shift['staff_name']; ?></p>
<h3>開始時間：</h3>
<div class="shift_edit_time_row">
    <span><?php echo substr($shift['from_time'], 0, 10); ?></span>
    <select id="shift_edit_from_hour" class="form-control custom-select-value">
        <?php for($i=$organ_from_time; $i<=$organ_to_time; $i++){
            $h = $i<10 ? '0'.$i : $i;
            ?>
            <option <?php if ($h==$from_hour) echo 'selected'; ?> value="<?php echo $h; ?>"><?php echo $h; ?></option>
        <?php } ?>
    </select>
    <span>:</span>
    <select id="shift_edit_from_min" class="form-control custom-select-value">
        <?php for($i=0; $i<60; $i+=5){
            $m = $i<10 ? '0'.$i : $i;
            ?>
            <option <?php if ($m==$from_min) echo 'selected'; ?> value="<?php echo $m; ?>"><?php echo $m; ?></option>
        <?php } ?>
    </select>
    <span>:00</span>
</div>

<h3>終了時間：</h3>
<div class="shift_edit_time_row">
    <span><?php echo substr($shift['from_time'], 0, 10); ?> </span>
    <select id="shift_edit_to_hour" class="form-control custom-select-value" >
        <?php for($i=$organ_from_time; $i<=$organ_to_time; $i++){
            $h = $i<10 ? '0'.$i : $i;
            ?>
            <option <?php if ($h==$to_hour) echo 'selected'; ?> value="<?php echo $h; ?>"><?php echo $h; ?></option>
        <?php } ?>
    </select>
    <span>:</span>
    <select id="shift_edit_to_min" class="form-control custom-select-value" name="account">
        <?php for($i=0; $i<60; $i+=5){
            $m = $i<10 ? '0'.$i : $i;
            ?>
            <option <?php if ($m==$to_min) echo 'selected'; ?> value="<?php echo $m; ?>"><?php echo $m; ?></option>
        <?php } ?>
    </select>
    <span>:00</span>
</div>
<h3>シフト状態：</h3>
<div class="form-select-list">
    <select id="shift_edit_type" class="form-control custom-select-value" name="account">
        <option value=""></option>
        <option <?php if ($shift['shift_type'] == SHIFT_STATUS_SUBMIT) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_SUBMIT; ?>">申請中</option>
        <option <?php if ($shift['shift_type'] == SHIFT_STATUS_REJECT) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_REJECT; ?>">拒否</option>
        <option <?php if ($shift['shift_type'] == SHIFT_STATUS_OUT) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_OUT; ?>">店外待機</option>
        <option <?php if ($shift['shift_type'] == SHIFT_STATUS_REST) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_REST; ?>">休み</option>
        <option <?php if ($shift['shift_type'] == SHIFT_STATUS_REQUEST) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_REQUEST; ?>">出勤依頼</option>
        <option <?php if ($shift['shift_type'] == SHIFT_STATUS_APPLY) echo 'selected'; ?> value="<?php echo SHIFT_STATUS_APPLY; ?>">承認</option>
    </select>
</div>
<input type="hidden" id="shift_edit_staff_id" value="<?php echo $shift['staff_id']; ?>" />
