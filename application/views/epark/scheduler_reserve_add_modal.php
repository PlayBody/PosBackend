
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/epark/scheduler-reserve-add-modal.css">

<div id="ReserveAddModal" class="modal modal-edu-general default-popup-PrimaryModal fade" role="dialog">
    <div class="modal-dialog reserve">
        <div class="modal-content">
            <div class="modal-header header-color-modal bg-color-1">
                <h4 class="modal-title">予約登録<span id="reserve_time"></span></h4>
                <div class="modal-close-area modal-close-df">
                    <a class="close" data-dismiss="modal" href="#"><i class="fa fa-close"></i></a>
                </div>
            </div>
            <div class="modal-body">
                <h3>お客様</h3>
                <div class="chosen-select-single mg-b-30">
                    <select id="reserve_add_user_id" data-placeholder="Choose a Country..." class="form-control" tabindex="-1">
                    </select>
                </div>
                <h3>スタッフ</h3>
                <div class="chosen-select-single mg-b-30">
                    <select id="reserve_add_staff_id" data-placeholder="Choose a Country..." class="form-control" tabindex="-1">
                    </select>
                </div>
                <h3>メニュー</h3>
                <div class="reserve_add_menu_content">
                    <div class="reserve_add_category">
                    </div>
                    <div class="reserve_add_menu">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" class="btn-cancel" href="#" >Cancel</a>
                <a class="btn-process" href="#">Process</a>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/epark/scheduler-reserve.js"></script>