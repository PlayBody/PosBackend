
<div id="left_user_head" class="left-content">
    <div>会員番号 : <span class="user_no"></span></div>
    <div>TEL : <span class="user_tel"></span></div>
    <div class="user_title">
        <div>
            <i class="fa fa-female"></i>
            <span class="user_name"></span>
        </div>
        <div>
            回数:15/15
        </div>
    </div>
    <div class="comment user_comment">

    </div>
    <div class="btn_list">
        <button class="btn btn-warning"><p>顧客</p><i class="fa fa-user"></i></button>
        <button id="btn_user_reserve_history" class="btn btn-warning"><p>履歴</p><i class="fa fa-history"></i></button>
        <button class="btn btn-warning"><p>会計</p><i class="fa fa-shopping-cart"></i></button>
    </div>
</div>

<div id="left_reserve_detail" class = "left-content" >
    <div class="left_content_header_button">
        <p></p>
        <div>
            <button id="btn_complete_order" type="button" class="btn btn-danger">完了</button>
            <button id="btn_reset_order" type="button" class="btn btn-default">リセット</button>
            <button type="button" class="btn close_btn" panel="reserve_detail"><i class="fa fa-times edu-danger-error"></i></button>
        </div>
    </div>
    <div class="left_content_header_button">
        <p><span class="order_reg_date">2023-07-19 14:27</span> 登録</p>
    </div>

    <div class="tab_title">
        <h4><span class="user_name"></span></h4>
    </div>
    <div class="btn-list-area-space">
        <button id="btn_exit_order" type="button" class="btn btn-default">精算</button>
        <button id="btn_delete_order" type="button" class="btn btn-danger">削除</button>
    </div>
    <ul class="list-comment">
        <li>
            <p>日時</p>
            <p><span class="order_from_time"></span>~</p>
        </li>
        <li>
            <p>ブース</p>
            <p><span class="order_table_position"></span></p>
        </li>
        <li>
            <p>スタッフ</p>
            <p><span class="order_sel_staff" style="color:red">★</span><span class="order_staff_name"></span></p>
        </li>
        <li class="menus"></li>
        <li>
            <p>オプション</p>
            <p></p>
        </li>
        <li>
            <p>こだわり</p>
            <p></p>
        </li>
        <li>
            <p>予約タグ</p>
            <p></p>
        </li>
        <li>
            <p>施術タグ</p>
            <p></p>
        </li>
        <li>
            <p>施術コメント</p>
            <p></p>
        </li>
    </ul>

</div>


<div id="left_user_reserves" class = "left-content" >
    <div class="left_content_header_button">
        <div>
            <button type="button" class="btn close_btn" panel="user_reserves"><i class="fa fa-times edu-danger-error"></i></button>
        </div>
    </div>

    <h4>顧客履歴</h4>
    <div class="tab_header1">
        <button service='' class="btn active">全て</button>
        <button service='1' class="btn">施術</button>
        <button service='2' class="btn">物販</button>
    </div>
    <div class="tab_content1">

    </div>

</div>