<style>
    .ajax_reserve_item{
        border:solid #DDC8B8 1px; display: flex; align-items: center;
        border-top: none;
    }
    #section_left_users .inner{
        border-top:solid #DDC8B8 1px;
    }
</style>
<div class="inner">
    <?php foreach ($users as $user){ ?>
        <div class="ajax_reserve_item" style="">
            <div class="record_prefix"><i class="fa fa-male"></i></div>
            <div>
                <p><?php echo $user['user_first_name']. ' '.$user['user_last_name']; ?></p>
                <p>TEL : <?php echo $user['user_tel']; ?></p>
                <p class="left_manager">会員番号 ：<?php echo $user['user_no']; ?></p>
            </div>
        </div>
    <?php } ?>
</div>
