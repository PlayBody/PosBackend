
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/epark/common.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/epark/schedule-left.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/epark/appoint.css">

<div>
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 epark-condition">
        <div class="col-md-6 cond_left">
            <div>
                <button id="btn_to_before_date" type="button" class="btn btn-custon-rounded-four btn-default"> < </button>
                <button id="btn_to_today_date" type="button" class="btn btn-custon-rounded-four btn-default"> &nbsp;&nbsp;&nbsp;今日&nbsp;&nbsp;&nbsp;</button>
                <button id="btn_to_next_date" type="button" class="btn btn-custon-rounded-four btn-default"> > </button>
            </div>
            <div class="input-group date">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input id="select_date" type="text" class="form-control" value="<?php echo $select_date; ?>">
            </div>
            <div class="breadcome-heading">
                <button id="btn_refresh"  type="button" class="btn btn-custon-rounded-four btn-success" > 更新 </button>
                <button id="btn_sync_epark"  type="button" class="btn btn-custon-rounded-four btn-warning" > Epark同期化 </button>
            </div>
        </div>
        <div class="col-md-3"></div>
        <div class="col-md-3">
                <div class="form-select-list">
                    <select id="sel_organ_id" class="form-control custom-select-value" >
                        <?php foreach ($organs as $organ){ ?>
                            <option value="<?php echo $organ['organ_id']; ?>" <?php if ($sel_organ_id==$organ['organ_id']){ echo 'selected'; }?> >
                                <?php echo $organ['organ_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
        </div>
    </div>
    <div id="schedule-content" >
        <div class="epark-left-nav">
            <button id="reserve" type="button" class="btn btn-custon-rounded-four btn-default">
                <span class="epark-fa epark_fa_clock"></span>
            </button>
            <button id="open" type="button" class="btn btn-custon-rounded-four btn-default">
                <span class="epark-fa epark_fa_open"></span>
            </button>
            <button id="close" type="button" class="btn btn-custon-rounded-four btn-default" style="display: none;">
                <span class="epark-fa epark_fa_close"></span>
            </button>
            <button type="button" class="btn btn-custon-rounded-four btn-default">
                <span class="epark-fa epark_fa_jpy"></span>
            </button>
<!--            <button type="button" class="btn btn-custon-rounded-four btn-default">-->
<!--                <span class="epark-fa epark_fa_user"></span>-->
<!--            </button>-->
<!--            <button type="button" class="btn btn-custon-rounded-four btn-default">-->
<!--                <span class="epark-fa epark_fa_card"></span>-->
<!--            </button>-->
        </div>
        <div id="epark-left-content">
            <div id="left_search_header">
                <input type="text" value="" class="form-control"/>
                <div class="left_search_action">
                    <div>
                        <input type="checkbox" class="i-checks" />
                        <span>自店のみ</span>
                    </div>

                    <button type="button" class="btn btn-primary" >検索</button>
                </div>
            </div>
            <div class="left-flow">
                <?php include "scheduler_left_reserve.php"; ?>
                <?php include "scheduler_left_reserve_detail.php"; ?>
                <?php include "scheduler_left_shift_edit.php"; ?>
            </div>
            <div id="load-left-mask" style="display: none;">
                <div class="spinner-border" role="status">
                </div>
            </div>
        </div>
        <div id="epark-content">
        </div>
    </div>
</div>
<input type="hidden" id="today_date" name="today_date" value="<?php echo $today_date; ?>" />
<input type="hidden" id="time_mode" name="time_mode" value="<?php echo $time_mode; ?>" />
<input type="hidden" id="type_mode" name="type_mode" value="<?php echo $type_mode; ?>" />

<script src="<?php echo base_url(); ?>assets/js/epark/scheduler.js"></script>
<script src="<?php echo base_url(); ?>assets/js/epark/scheduler-left-nav.js"></script>

<?php include 'schedule_shift_add_modal.php'; ?>
<?php include 'scheduler_reserve_add_modal.php'; ?>

