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
        border: solid #eb0909 2px;
        background-color: #fff6a0;
    }

    #userInput{ 
        box-sizing: border-box;
        background-image: url('searchicon.png');
        background-position: 14px 12px;
        background-repeat: no-repeat;
        font-size: 16px;
        /* padding: 14px 20px 12px 45px; */
    }

    #userDropdown a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    a#dropIcon {
        position : absolute;
        padding : 6px 10px;
        top: 1px;
        right: 1px;
    }

/* Change color of dropdown links on hover */
.dropdown-content a:hover {background-color: #f1f1f1}
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
            <div id="userDropdownGroup" class="dropdown-content" style="position:relative;">
                <input type="text" class="form-control" placeholder="" id="userInput">
                <input type="hidden" class="form-control" placeholder="" id="reserve_add_user_id">
                <a id="dropIcon" href="#"><i class="fa fa-angle-down"></i></a>
                <div id="userDropdown" style="position:absolute; background-color:white; max-height:150px; overflow-y:scroll;width:100%;display:none;border: solid #cccccc 1px;">
                    <?php foreach ($users as $user){ ?>
                        <a href="#<?php echo $user['user_id']; ?>" 
                            data-id="<?php echo $user['user_id']; ?>" 
                            data-name="<?php echo $user['user_first_name'].' '.$user['user_last_name']; ?>" 
                            data-tel="<?php echo $user['user_tel']; ?>">
                                <?php echo $user['user_first_name'].' '.$user['user_last_name']; ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <!-- <select class="form-control select2" id="reserve_add_user_id">
                <option value="">顧客を選択してください。</option>
                <?php foreach ($users as $user){ ?>
                    <option value="<?php echo $user['user_id']; ?>"><?php echo $user['user_first_name'].' '.$user['user_last_name']; ?></option>
                <?php } ?>
            </select> -->
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
                    <li data="<?php echo $menu['menu_id']; ?>" id="reserve_menu_btn_<?php echo $menu['menu_id']; ?>" class=" reserve_menu_btn" <?php if ($menu['color']){ ?> style="background-color:<?php echo $menu['color']; ?>;"<?php } ?> ><?php echo $menu['menu_title']; ?></li>
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

        console.log({
                'organ_id' : '<?php echo $organ_id; ?>',
                'staff_id' : staff_id,
                'user_id' : user_id,
                'reserve_start_time' : reserve_time,
                'menus' : menus
            });
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

    $('#userInput').on('focus', function(){
        setDropBox(false, true);
    });
    
    // $('#userDropdownGroup').on('blur', function(){
    //    $('#userDropdown').hide();
    // });
    $('#userInput').on('keyup', function(){
        input = $('#userInput').val();
        filter = input.toUpperCase();
        list = $('#userDropdown a');

        $('#userDropdown a').each(function (e){
            txtValue = $(this).data('name') + $(this).data('tel');
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        // for (i = 0; i < list.length; i++) {
        //     txtValue = list[i].textContent || list[i].innerText;
        //     if (txtValue.toUpperCase().indexOf(filter) > -1) {
        //         list[i].style.display = "";
        //     } else {
        //         list[i].style.display = "none";
        //     }
        // }
    })
    $('#userDropdown a').on('click', function(){
        $('#userInput').val($(this).data('name'));
        $('#reserve_add_user_id').val($(this).data('id'));
        setDropBox(false, false);
    });

    $('#dropIcon').on('click', function(){
        setDropBox(true);
    });

    function setDropBox(auto = false, isShow = true){
        if (auto){
            isVisible = $('#userDropdown').css('display') == 'none';
        }else{
            isVisible = isShow;
        }

        if (isVisible){
            $('#userDropdown').show();
            $('#dropIcon i').removeClass('fa-angle-down');
            $('#dropIcon i').addClass('fa-angle-up');
        }else{
            $('#userDropdown').hide();
            $('#dropIcon i').addClass('fa-angle-down');
            $('#dropIcon i').removeClass('fa-angle-up');
        }

    }

    // function filterFunction() {
    //     var input, filter, ul, li, a, i;
    //     input = document.getElementById("userInput");
    //     filter = input.value.toUpperCase();
    //     div = document.getElementById("myDropdown");
    //     a = div.getElementsByTagName("a");
    // }
</script>
