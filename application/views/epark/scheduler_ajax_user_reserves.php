
<?php if (empty($orders)) {?>
    <div class="no-data">予約内容はありません。</div>
<?php } ?>
<?php foreach ($orders as $order) {?>
<div order="<?php echo $order['id']; ?>" class="reserve_content_item">
    <?php if($order['status'] == ORDER_STATUS_TABLE_COMPLETE){ ?>
        <i class="fa fa-check fa-2x i-sex-complete"></i>
    <?php }else if($order['status'] == ORDER_STATUS_RESERVE_APPLY){ ?>
        <i class="fa fa-clock-o fa-2x"></i>
    <?php }else{ ?>
        <i class="fa fa-inbox fa-2x"></i>
    <?php } ?>
    <div>
        <p><?php echo substr($order['from_time'], 0, 16); ?></p>
        <p><?php echo $order['user_name']; ?></p>
        <?php if (!empty($order['menus'])){ ?>
            <?php foreach ($order['menus'] as $menu){ ?>
                <p><?php echo $menu['menu_title']; ?></p>
            <?php } ?>
        <?php } ?>
        <p><?php echo $order['staff_name']; ?></p>
    </div>
</div>
<?php } ?>

<script>
    $('.reserve_content_item').on('click', function () {
        close_leftPan('reserve_detail');
        loadLeftReserveDetail($(this).attr('order'));
    })
</script>
