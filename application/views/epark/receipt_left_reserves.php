<style>
    #section_left_reserves p{
        margin: 0;
    }
    #section_left_reserves .a_link{
        color: #0000ff;
    }
    .record_prefix{
        padding: 4px;
    }
    .record_prefix i{
        color: #9F6E4B;
    }

    #section_left_reserves .a_close{
        color: #333333;
        padding: 8px;
        background-color: #F5EEEA;
        border: solid #9F6E4B 1px;
        border-radius: 3px;
    }
    #section_left_reserves .a_close:hover{
        color: white;
        background-color: #c9302c;
    }

    ul.tab{
        margin-bottom: 4px;
        border-bottom: solid #F5EEEA 1px;
        display: flex;
    }
    .tab li{
        cursor: pointer;
        border-top-left-radius: 6px;
        border-top-right-radius: 6px;
        margin-right: 2px;
        background-color: #F5EEEA;
        list-style: none;
        padding: 10px 12px;
        font-size: 12px;
        color: #9F6E4B;
        font-weight: bold;
    }
    .tab li.active{
        margin-bottom: -2px;
        background-color: white;
        border: solid #F5EEEA 1px;
        border-bottom: none;
    }
    .left_section_content{
        background-color: white; padding:4px;
        border-radius: 4px;
    }
    .left_section_content h3{
        text-align: center; font-size: 22px; font-weight: bold;
    }
</style>
<section id="section_left_users">
    <div class="left_section_content">
        <h3>顧客検索結果一覧</h3>
        <div class="data-pannel">
        </div>
    </div>
</section>
<section id="section_left_reserves">
    <div class="left_section_content">
        <h3>当日情報一覧</h3>
        <div>
            <ul class="tab">
                <li class="reserve_tab_reserve_info">予約情報</li>
                <li>販売情報</li>
                <li>返客情報</li>
            </ul>
        </div>
        <div class="data-pannel">
        </div>
    </div>
</section>

<section id="section_left_user_info">
    <div class="left_section_content header-pannel">
    </div>

    <div style="margin-top: 3px;" class="left_section_content user_info_main_pannel">
        <div class="user_info_history_header">
            <h3>顧客履歴</h3>
            <div>
                <ul class="tab">
                    <li class="reserve_tab_user_reserves_all">全て</li>
                    <li class="reserve_tab_user_reserves_service">施術</li>
                    <li class="reserve_tab_user_reserves_goods">物販</li>
                </ul>
            </div>
        </div>
        <div class="data-pannel">

        </div>
    </div>
</section>

<section id="section_left_user_add">
    <div style="margin-top: 3px;" class="left_section_content">
        <div>
            <button class="btn_red">登録</button>
        </div>
        <div>性別:</div>
        <div>名前(カナ):</div>
        <div>電話番号:</div>
        <div>会員番号:</div>
    </div>
</section>

<section id="section_left_staff_shift">
    <div style="margin-top: 3px;" class="left_section_content data_pannel">
        <div class="data-pannel">
        </div>
    </div>
</section>

<script>
    $('.reserve_tab_reserve_info').click(function(e){
        $('#receipt_left_panel section').hide();
        $('#section_left_reserves').show();
        //$('.tab li').addClass('active');
        $('.reserve_tab_reserve_info').addClass('active');
        $.ajax({
            url: base_url + "epark/receipt/getAjaxLeftReserveList",
            type: 'post',
            data : {'organ_id' : '<?php echo $organ_id ?>', 'select_date' : '<?php echo $select_date; ?>'},
            context: document.body
        }).done(function(res) {
            $('#section_left_reserves .data-pannel').html(res);
        });
    });

    $('#left_panel_search').click(function(e){
        $('#receipt_left_panel section').hide();
        $('#section_left_users').show();
        $.ajax({
            url: base_url + "epark/receipt/getAjaxLeftUserList",
            type: 'post',
            data : {'user_name' : $('#left_search_name').val()},
            context: document.body
        }).done(function(res) {
            $('#section_left_users .data-pannel').html(res);
        });
    });

    function user_detail(user_id, type='', show_detail_id) {
        $('#receipt_left_panel section').hide();
        $('#section_left_user_info').show();
        console.log('1'+type+'1');
        $.ajax({
            url: base_url + "epark/receipt/getAjaxUserInfo",
            type: 'post',
            data : {'user_id' : user_id},
            context: document.body
        }).done(function(res) {
            $('#section_left_user_info .header-pannel').html(res);
            if(type==''){
                $('.user_info_history_header').show();
                $('.reserve_tab_user_reserves_all').trigger('click');
            }
            if(type=='reserve_detail'){
                $('.user_info_history_header').hide();
                show_reserve_detail(show_detail_id);
            }

        });
    }

    function show_reserve_detail(reserve_id){
        $.ajax({
            url: base_url + "epark/receipt/getAjaxReserveInfo",
            type: 'post',
            data : {'reserve_id' : reserve_id},
            context: document.body
        }).done(function(res) {
            $('#section_left_user_info .data-pannel').html(res);
        });
    }

    function user_add() {
        $('#receipt_left_panel section').hide();
        $('#section_left_user_add').show();
        // $.ajax({
        //     url: base_url + "epark/receipt/getAjaxUserInfo",
        //     type: 'post',
        //     data : {'user_id' : user_id},
        //     context: document.body
        // }).done(function(res) {
        //     $('#section_left_user_info .header-pannel').html(res);
        //     if(type=='')
        //         $('.reserve_tab_user_reserves_all').trigger('click');
        //     if(type=='reserve_detail')
        //         show_reserve_detail(show_detail_id);
        //
        // });
    }
    function shift_detail(shift_id) {
        $('#receipt_left_panel section').hide();
        $('#section_left_staff_shift').show();
        $.ajax({
            url: base_url + "epark/receipt/getAjaxShiftInfo",
            type: 'post',
            data : {'shift_id' : shift_id,
                'organ_from_time' : organ_from_hour,
                'organ_to_time' : organ_to_hour
            },
            context: document.body
        }).done(function(res) {
            $('#section_left_staff_shift .data-pannel').html(res);
        });
    }

</script>

