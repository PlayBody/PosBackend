<style>

    .reserve_menu_btn{
        cursor: pointer;
        list-style: none;
        margin-right: 4px;
        margin-bottom: 12px;
        padding: 8px;
        border: solid grey 1px;
        border-radius: 3px;
    }

    .reserve_menu_btn.active{
        border: solid #eb0909 1px;
        background-color: #fff6a0;
    }
</style>
<div id="widget_reserve_add" class="widget_dialog" style="display: none;">
    <div class="title">
        <div style="float: left;">
            予約登録<span style="padding-left: 40px; font-size: 16px;" id="reserve_add_from_time"></span>
        </div>
        <div style="float: right;">
            <button class="btn_close_widget"><i class="fa fa-close"></i></button>
        </div>
    </div>
    <div style="padding : 12px;">
        <div class="comment_label">顧客</div>
        <div class="row" style="margin-top: 12px; margin-bottom: 12px">
            <select class="form-control" id="reserve_add_user_id">
                <option value="">顧客を選択してください。</option>
                <?php foreach ($users as $user){ ?>
                    <option value="<?php echo $user['user_id']; ?>"><?php echo $user['user_first_name'].' '.$user['user_last_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="comment_label">スタッフ</div>
        <div class="row" style="margin-top: 12px; margin-bottom: 12px">
            <select class="form-control" id="reserve_add_staff_id">
                <option value="">スタッフを選択してください。</option>
                <?php foreach ($organ_staffs as $staff){ ?>
                    <option value="<?php echo $staff['staff_id']; ?>"><?php echo $staff['sort_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="comment_label">メニュー</div>
        <div class="row" style="margin-top: 12px; margin-bottom: 12px">
            <ul style="display: flex; flex-wrap: wrap;">
                <?php foreach ($menus as $menu){ ?>
                    <li data="<?php echo $menu['menu_id']; ?>" id="reserve_menu_btn_<?php echo $menu['menu_id']; ?>" class=" reserve_menu_btn"><?php echo $menu['menu_title']; ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="row" style="float: right; margin-bottom: 12px; margin-top: 12px;">
        <button id="reserve_add_btn" class="btn_grey">登録</button>
    </div>
</div>

<script>
    var menus = [];
    $('#reserve_add_btn').click(function (e) {
        reserve_time = $('#reserve_add_from_time').html();
        staff_id = $('#reserve_add_staff_id').val();
        user_id = $('#reserve_add_user_id').val();
        if(reserve_time==''){
            alert('予約時間が選択されていません。');
            return;
        }
        if(user_id==''){
            alert('顧客を選択してください。');
            return;
        }
        if(user_id==''){
            alert('顧客を選択してください。');
            return;
        }
        if(menus.length<1){
            alert('メニューを選択してください。');
            return;
        }

        if(!confirm('登録しますか？')) return;

        $.ajax({
            url: base_url + "epark/receipt/saveUserReserve",
            type: 'post',
            data : {
                'organ_id' : '<?php echo $organ_id; ?>',
                'staff_id' : staff_id,
                'user_id' : user_id,
                'reserve_start_time' : reserve_time,
                'menus' : menus
            },
            context: document.body
        }).done(function(res) {
            data = JSON.parse(res);
            if(data['isSave']){
                refresh_load();
            }else{
                alert('予約できません。');
            }
        });

    });


    $('.reserve_menu_btn').click(function (e){
        menu_id = $(this).attr('data');
        if(menus.indexOf(menu_id)<0){
            menus.push(menu_id);
            $('#reserve_menu_btn_'+menu_id).addClass('active');
        }else{
            menus.splice(menus.indexOf(menu_id),1);
            $('#reserve_menu_btn_'+menu_id).removeClass('active');
        }
        console.log(menus);
        // $('.shift_info_time_btn').removeClass('active');
        // $('#time_btn_'+time).addClass('active');
        //
        // $('#edit_shift_length').val(time);
    });
</script>
