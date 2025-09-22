<div style="background-color: <?php echo $order['color']; ?>; width: <?php echo $real_width; ?>%; height: 100%;padding-left:  <?php echo $txt_left; ?>%;" >
    <p>
        <span class="sex <?php if ($order['user_sex']==2){ echo 'sex2'; }else{ echo 'sex1'; } ?>"><?php if ($order['user_sex']==2){ echo '女'; }else{ echo '男'; } ?></span>
        <?php echo $order['user_name']; ?>
    </p>
    <p class="time-comment">
        <?php echo substr($order['from_time'], 11, 5)."~".substr($order['to_time'], 11, 5); ?>
    </p>
    <p>
        <?php
            $txt = '';
            $class = '';
            switch ($order['select_staff_type']){
                case 1:
                    $txt = '指名';
                    $class = 'staff_type1';
                    break;
                case 2:
                    $txt = '希望';
                    $class = 'staff_type2';
                    break;
                case 3:
                    $txt = '指名';
                    $class = 'staff_type3';
                    break;

            }
        ?>
        <span class="staff_type <?php echo $class; ?>"><?php echo $txt; ?></span>
    </p>
</div>
