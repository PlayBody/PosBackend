<?php foreach($list as $item){ ?>
    <div><?php echo $item[0]['organ_name']; ?> <?php echo $item[0]['staff_name']; ?></div>
    <?php foreach($item as $shift){ ?>
        <div >
            <?php 
                $shift_staus_array = array(
                    SHIFT_STATUS_SUBMIT => '申請中',
                    SHIFT_STATUS_REJECT => '拒否',
                    SHIFT_STATUS_OUT => '店外待機',
                    SHIFT_STATUS_REST => '休み',
                    SHIFT_STATUS_REQUEST => '出勤依頼',
                    SHIFT_STATUS_ME_REJECT => '出勤依頼 - 拒否',
                    SHIFT_STATUS_ME_REPLY => '回答済み',
                    SHIFT_STATUS_ME_APPLY => '回答済み-承認',
                    SHIFT_STATUS_APPLY => '承認',
                );
                echo $shift['from_time'] . ' ~ ' . $shift['to_time'] . '  ' . $shift_staus_array[$shift['shift_type']];
            ?>
            <a style="margin-left:30px;" href="<?php echo base_url(); ?>system/shiftDelete?shift_id=<?php echo $shift['shift_id']; ?>">削除</a>
        </div>
    <?php } ?>
<?php } ?>