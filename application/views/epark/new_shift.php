
<div id="widget_shift_add" class="widget_dialog" style="display: none;">
    <div class="title">
        <div style="float: left;">
            シフト新規登録
        </div>
        <div style="float: right;">
            <button class="btn_close_widget"><i class="fa fa-close"></i></button>
        </div>
    </div>
    <div class="row" style="margin-top: 12px; margin-bottom: 12px">
        <select class="form-control" id="shift_add_staff_id">
            <option value="">スタッフを選択してください。</option>
            <?php foreach ($organ_staffs as $staff){ ?>
                <option value="<?php echo $staff['staff_id']; ?>"><?php echo $staff['sort_name']; ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="row">
        <div class="col-md-2 label">開始時間</div>
        <div class="col-md-4">
            <select class="form-control" id="shift_add_from_h">
                <?php for($i=$available_time_from; $i<=$available_time_to; $i++){ ?>
                    <option
                        <?php if (intval($table_start_time/60) == $i) echo 'selected'; ?> <?php echo $i<10 ? '0'.$i : $i; ?>><?php echo $i<10 ? '0'.$i : $i; ?></option>
                <?php } ?>
            </select>
        </div>
        <div style="float: left; width: 8px; text-align: center; line-height: 32px;">:</div>
        <div class="col-md-4">
            <select class="form-control" id="shift_add_from_m">
                <?php for($i=0; $i<60; $i+=5){ ?>
                    <option
                        <?php if ($table_start_time%60 == $i) echo 'selected'; ?>
                        value="<?php echo $i<10 ? '0'.$i : $i; ?>" >
                        <?php echo $i<10 ? '0'.$i : $i; ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 label">終了時間</div>
        <div class="col-md-4">
            <select class="form-control" id="shift_add_to_h">
                <?php for($i=$available_time_from; $i<=$available_time_to; $i++){ ?>
                    <option <?php if (intval(($table_start_time+$table_length)/60) == $i) echo 'selected'; ?>  <?php echo $i<10 ? '0'.$i : $i; ?>><?php echo $i<10 ? '0'.$i : $i; ?></option>
                <?php } ?>
            </select>
        </div>
        <div style="float: left; width: 8px;text-align: center; line-height: 32px;">:</div>
        <div class="col-md-4">
            <select class="form-control" id="shift_add_to_m">
                <?php for($i=0; $i<60; $i+=5){ ?>
                    <option
                        <?php if (($table_start_time+$table_length)%60 == $i) echo 'selected'; ?>
                        value="<?php echo $i<10 ? '0'.$i : $i; ?>" >
                        <?php echo $i<10 ? '0'.$i : $i; ?>
                    </option>

                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row" style="margin-top: 20px;">
        <div class="col-md-2 label">シフト状態</div>
        <div class="col-md-8">
            <select class="form-control" id="shift_add_type">
                <option value="<?php echo SHIFT_STATUS_SUBMIT; ?>">申請中</option>
                <option value="<?php echo SHIFT_STATUS_REJECT; ?>">拒否</option>
                <option value="<?php echo SHIFT_STATUS_OUT; ?>">店外待機</option>
                <option value="<?php echo SHIFT_STATUS_REST; ?>">休み</option>
                <option value="<?php echo SHIFT_STATUS_REQUEST; ?>">出勤依頼</option>
                <option value="<?php echo SHIFT_STATUS_ME_APPLY; ?>">回答済み</option>
                <option value="<?php echo SHIFT_STATUS_APPLY; ?>">承認</option>
            </select>
        </div>
    </div>
    <div class="row" style="float: right; margin-bottom: 12px; margin-top: 12px;">
        <button id="shift_add_btn" class="btn_grey">登録</button>
    </div>
</div>

<script>
    $('#shift_add_btn').click(function (e) {
        staff_id = $('#shift_add_staff_id').val();
        if(staff_id==''){
            alert('スタッフを選択してください。');
            return;
        }
        sel_date = '<?php echo $select_date; ?>';
        from_time = sel_date + ' ' + $('#shift_add_from_h').val() + ':' + $('#shift_add_from_m').val() + ':00';
        to_time = sel_date + ' ' + $('#shift_add_to_h').val() + ':' + $('#shift_add_to_m').val() + ':00';
        if(from_time>to_time){
            alert('開始時間と終了時間を正確に入力してください。');
            return;
        }
        if(!confirm('登録しますか？')) return;
        $.ajax({
            url: base_url + "apishifts/saveShift",
            type: 'post',
            data : {
                'organ_id' : '<?php echo $organ_id; ?>',
                'staff_id' : staff_id,
                'from_time' : from_time,
                'to_time' : to_time,
                'shift_type' : $('#shift_add_type').val(),
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

    });
</script>
