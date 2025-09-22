<style>
    .ajax_user_item{
        border:solid #DDC8B8 1px; display: flex; align-items: center;
        border-top: none;
        background-color: #FFE5D8;
            padding: 8px 0px;

    }
    #section_left_users .inner{
        border-top:solid #DDC8B8 1px;
    }

    .ajax_user_item i.fa:before{
        font-size:28px;
    }
    .record_sex{ padding : 8px; }
    .record_sex_male{
        color : black;
    }
    .record_sex_female{
        color : #CC3340;
    }

    .ajax_user_item p{
        margin:0px;
    }
</style>
<div class="inner">
<?php foreach ($users as $user){ ?>
            <div class="ajax_user_item" data="<?php echo $user['user_id']; ?>" style="">
                <div class="record_sex"><i class="fa <?php if ($user['user_sex']==1){ ?> fa-male record_sex_male <?php }else{ ?> fa-female record_sex_female  <?php } ?>"></i></div>
                <div style="width:100%; padding:0 8px;">
                    <p><?php echo $user['user_first_name']. ' '.$user['user_last_name']; ?></p>
                    <p><span>TEL</span><span style="float:right;"><?php echo $user['user_tel']; ?></span></p>
                    <p><span>会員番号</span><span style="float:right;"><?php echo $user['user_no']; ?></span></p>
                </div>
            </div>
<?php } ?>
</div>

<script>
    $('.ajax_user_item').click(function(e){
        user_id = $(this).attr('data');
        user_detail(user_id);
    })
</script>
