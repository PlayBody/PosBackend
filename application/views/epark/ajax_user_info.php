<style>
    .user_info p{
        margin: 0px;
    }
    .user_name_record i:before{font-size: 24px; }
    .record_sex_male{color : black;}
    .record_sex_female{color : #CC3340;}
    .user_name_record{font-size: 18px; font-weight: bold; padding: 4px 0;}

    .user_nav_button{display: table;width: 100%;}
    .user_nav_button ul{ display:flex; float: right;}
    .user_nav_button li{
        cursor: pointer;
        margin-left: 4px;
        border : solid #8b532b 1px;
        border-radius: 3px;
        background-color: white;
        color: #cf5600;
        list-style: none;
        text-align: center;
        padding: 4px 6px;
    }
    .user_nav_button li:hover{
        background-color: #e1c698;
    }
</style>
<div class="inner">
    <div class="user_info">
        <p>会員番号 : <?php echo $user['user_no']; ?></p>
        <p>TEL : <?php echo $user['user_tel']; ?></p>
        <div class="user_name_record">
            <i class="fa <?php if ($user['user_sex']==1){ ?> fa-male record_sex_male <?php }else{ ?> fa-female record_sex_female  <?php } ?>"></i>
            <span><?php echo $user['user_first_name'] . ' ' . $user['user_last_name']; ?></span>
            <span style="float: right;">回数:15/15</span>
        </div>
        <div style="width: 100%; height: 64px; overflow-y: scroll;"></div>
        <div style="width: 100%; height: 1px; background-color: black; margin: 4px 0;"></div>
        <div class="user_nav_button">
            <ul>
                <li id="tab_user_info_reserve"><p>顧客</p><i class="fa fa-user fa-2x"></i></li>
                <li id="tab_user_info_history" data-user_id="<?php echo $user['user_id']; ?>"><p>履歴</p><i class="fa fa-history fa-2x"></i></li>
                <li><p>会計</p><i class="fa fa-shopping-cart fa-2x"></i></li>
            </ul>
        </div>
    </div>
</div>

<script>
    $('#tab_user_info_history').click(function(e){

        user_detail($(this).attr('data-user_id'), '', '');
    })

    $('.reserve_tab_user_reserves_all').click(function(e){
        $.ajax({
            url: base_url + "epark/receipt/getAjaxUserReserveList",
            type: 'post',
            data : {'user_id' : user_id},
            context: document.body
        }).done(function(res) {
            $('#section_left_user_info .data-pannel').html(res);
        });
    })

    $('.reserve_tab_user_reserves_service').click(function(e){
        $.ajax({
            url: base_url + "epark/receipt/getAjaxUserReserveList",
            type: 'post',
            data : {'user_id' : user_id, 'is_service':'1'},
            context: document.body
        }).done(function(res) {
            $('#section_left_user_info .data-pannel').html(res);
        });
    })
    $('.reserve_tab_user_reserves_goods').click(function(e){
        $.ajax({
            url: base_url + "epark/receipt/getAjaxUserReserveList",
            type: 'post',
            data : {'user_id' : user_id, 'is_goods':'1'},
            context: document.body
        }).done(function(res) {
            $('#section_left_user_info .data-pannel').html(res);
        });
    })

</script>

