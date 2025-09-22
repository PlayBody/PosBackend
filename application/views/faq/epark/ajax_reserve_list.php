<style>
    .ajax_reserve_item{
        border:solid #DDC8B8 1px; display: flex; align-items: center;
        border-top: none;
    }
    #section_left_reserves .inner{
        border-top:solid #DDC8B8 1px;
    }
</style>
<div class="inner">
    <?php if (empty($reserves)){ ?>
        <div style="text-align: center; padding: 12px;" >予約内容はありません。</div>
    <?php } ?>
<?php foreach ($reserves as $reserve){ ?>
            <div class="ajax_reserve_item" data-user-id = "<?php echo $reserve['user_id']?>" data-reserve-id="<?php echo $reserve['id']; ?>">
                <div class="record_prefix"><i class="fa fa-clock-o fa-2x"></i></div>
                <div style="width: 70%;">
                    <p class="left_time_range"><?php echo date("H:i", strtotime($reserve['from_time'])) . '~' . date("H:i", strtotime($reserve['to_time'])); ?></p>
                    <i class="fa fa-check"></i><a href="#" class="a_link"><?php echo $reserve['user_name']; ?></a>

                    <?php if(!empty($reserve['menus'])) foreach ($reserve['menus'] as $menu){ ?>
                        <p class="left_course_name">コース名 : <?php echo $menu['menu_title']; ?></p>
                    <?php } ?>
                    <p class="left_manager">担当者 ：<?php echo $reserve['staff_name']; ?></p>
                </div>
                <div style="padding-left: 8px;">
                    <a href="#" class="a_close"><i class="fa fa-close"></i></a>
                </div>
            </div>
<?php } ?>
</div>

<script>
    $('.ajax_reserve_item').click(function (e){
        user_id = $(this).attr('data-user-id');
        reserve_id = $(this).attr('data-reserve-id');
        user_detail(user_id, 'reserve_detail', reserve_id);
    })
</script>
