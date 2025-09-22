<style>
    .div_reserve_table_row{
        display: flex;
        width: 100%;
        margin: 6px 0;
    }
    .div_reserve_table_row .th{
        width: 30%;
        background-color: #cccccc;
        margin-right: 12px;
    }
</style>

<div class="inner">
    <div style="display: table; width: 100%;">
        <div style="float:left;">
            <?php echo date("Y-m-d H:i", strtotime($reserve['create_date'])); ?> <b>受付</b>
        </div>
        <div style="float:right;">
            <button class="btn_red">完了</button>
        </div>
    </div>
    <div style="border-bottom: solid #aaaaaa 1px; width: 100%; display: table; padding-left:8px;margin-bottom:2px;">
        <div style="float: left; border: solid #aaaaaa 1px; padding: 4px 8px; border-bottom:none;">
            <?php echo $reserve['user_name']; ?>
        </div>
    </div>
    <div style="display: table; width:100%; margin-bottom:12px;">
        <div style="float:left;">
            <button class="btn_grey">精算</button>
        </div>
        <div style="float:right;">
            <button class="btn_red">削除</button>
        </div>
    </div>
    <div class="div_reserve_table_row">
        <div class="th">日時</div>
        <div><?php echo date("Y-m-d H:i", strtotime($reserve['from_time'])) . '~'; ?></div>
    </div>

    <div style="border-bottom: solid #aaaaaa 1px; width: 100%; display: table; padding-left:8px;margin-bottom:2px;">
        <?php foreach ($menus as $menu){ ?>
            <div style="float: left; border: solid #aaaaaa 1px; padding: 4px 8px; border-bottom:none;">
                <?php echo $menu['menu_title']; ?>
            </div>
        <?php } ?>
    </div>
    <?php foreach ($menus as $menu){ ?>
    <div id = "ordermenu_<?php echo $menu['id']; ?>">
        <div class="div_reserve_table_row">
            <div class="th">時間</div>
            <div><?php echo date("H:i", strtotime($reserve['from_time'])) . '~'; ?></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">ブース</div>
            <div></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">スタッフ</div>
            <div><?php echo $reserve['staff_name']; ?></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">コース</div>
            <div><?php echo $menu['menu_title']; ?></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">オプション</div>
            <div></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">こだわり</div>
            <div></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">予約タグ</div>
            <div></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">施術タグ</div>
            <div></div>
        </div>
        <div class="div_reserve_table_row">
            <div class="th">施術コメント</div>
            <div></div>
        </div>
    </div>
    <?php } ?>

</div>
