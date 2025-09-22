<?php if (empty($orders)){ ?>
    <div class="no-data">予約情報がありません。</div>
<?php } ?>
<?php foreach ($orders as $order){
    $from_time = substr($order['from_time'], 11, 5);
    $to_time = substr($order['to_time'], 11, 5);
    ?>
        <div order="<?php echo $order['id']; ?>" class="reserve_content_item" >
            <i class="fa <?php if ($order['is_reset'] == 1) echo 'fa-check i-sex-complete'; else echo 'fa-clock-o'; ?>" aria-hidden="true"></i>
                <div>
                    <p><?php echo $from_time; ?>～<?php echo $to_time; ?></p>
                    <p><?php echo $order['user_name']; ?></p>

                    <?php if (!empty($order['menus'])){ ?>
                        <?php if (count($order['menus'])>1){ ?>
                            <?php
                                $ii=0;
                                foreach ($order['menus'] as $menu) {
                                    $ii++;
                            ?>
                                <p>コース名<?php echo $ii; ?>：<?php echo $menu['menu_title']; ?></p>
                            <?php } ?>
                        <?php } else { ?>
                            <p>コース名：<?php echo $order['menus'][0]['menu_title']; ?></p>
                        <?php } ?>
                    <?php } ?>


                    <p>担当者 ：<?php echo $order['staff_name']; ?></p>
                </div>
            <button type="button" class="btn btn-danger"><i class="fa fa-times edu-danger-error"></i></button>
        </div>
<?php } ?>
<script>
    $('.reserve_content_item').click(function () {
        if (!$(this).attr('order')) return;
        loadLeftReserveDetail($(this).attr('order'));
    })
</script>