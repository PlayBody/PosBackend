<style>
    .ajax_user_reserve_item{
        padding: 8px 0;
        border:solid #DDC8B8 1px; display: flex; align-items: center;
        border-top: none;
    }
    #section_left_user_info .data-pannel .inner{
        border-top:solid #DDC8B8 1px;
    }
    .data-pannel p{margin: 0}
</style>
<div class="inner">
    <?php if (empty($reserves)){ ?>
        <div style="text-align: center; padding: 12px;" >予約内容はありません。</div>
    <?php } ?>
    <?php foreach ($reserves as $reserve){ ?>
        <div class="ajax_user_reserve_item" style="" >
            <div class="record_prefix"><i class="fa fa-clock-o fa-2x"></i></div>
            <div style="width: 60%;">
                <p class="left_time_range"><?php echo date("y-md-d H:i", strtotime($reserve['reserve_time'])); ?></p>
                <p class="left_time_range"><?php echo $reserve['organ_name']; ?></p>
                <?php foreach ($reserve['menus'] as $menu){ ?>
                    <p class="left_course_name"><?php echo $menu['menu_title']; ?></p>
                <?php } ?>
                <p class="left_manager"><?php echo $reserve['staff_name']; ?></p>
            </div>
            <div style="padding-left: 8px;">
                <button class="btn_red">再予約</button>
            </div>
        </div>
    <?php } ?>
</div>
