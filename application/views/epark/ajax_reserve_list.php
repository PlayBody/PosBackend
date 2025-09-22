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
    <?php if (empty($orders)){ ?>
        <div style="text-align: center; padding: 12px;" >予約内容はありません。</div>
    <?php } ?>
<?php foreach ($orders as $order){ ?>
            <div class="ajax_reserve_item" data-user-id = "<?php echo $order['user_id']?>" data-reserve-id="<?php echo $order['id']; ?>">
                <div class="record_prefix">
                    <?php if($order['status'] == ORDER_STATUS_TABLE_COMPLETE){ ?>
                        <i class="fa fa-check fa-2x"></i>
                    <?php }else if($order['status'] == ORDER_STATUS_RESERVE_APPLY){ ?>
                        <i class="fa fa-clock-o fa-2x"></i>
                    <?php }else if($order['status'] == ORDER_STATUS_RESERVE_CANCEL){ ?>
                        <i class="fa fa-minus-circle fa-2x" style="color: grey"></i>
                    <?php }else{ ?>
                        <i class="fa fa-inbox fa-2x"></i>
                    <?php } ?>
                </div>
                <div style="width: 70%;">
                    <p class="left_time_range"><?php echo date("H:i", strtotime($order['from_time'])) . '~' . date("H:i", strtotime($order['to_time'])); ?></p>
                    <i class="fa fa-check"></i><a href="#" class="a_link"><?php echo $order['user_name']; ?></a>

                    <?php $ii=0; if(!empty($order['menus'])) foreach ($order['menus'] as $menu){ $ii++;?>
                        <p class="left_course_name">コース名<?php if(count($order['menus'])>1) echo $ii; ?> : <?php echo $menu['menu_title']; ?></p>
                    <?php } ?>
                    <p class="left_manager">担当者 ：<?php if(!empty($order['staff_name'])){ ?><span style="color: red">★</span><?php } ?><?php echo $order['staff_name']; ?></p>
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
