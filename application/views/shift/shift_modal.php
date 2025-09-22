
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/shift/shift-modal.css">

<div id="ShiftAddModal" class="modal modal-edu-general default-popup-PrimaryModal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header header-color-modal bg-color-1">
                <h4 class="modal-title">シフト新規登録</h4>
                <div class="modal-close-area modal-close-df">
                    <a class="close" data-dismiss="modal" href="#"><i class="fa fa-close"></i></a>
                </div>
            </div>
            <div class="modal-body">
                <div class="chosen-select-single mg-b-30">
                    <select id="shift_add_staff_id" data-placeholder="Choose a Country..." class="form-control shift_add_staff_name" tabindex="-1">
                        <option value="">Select</option>
                    </select>
                </div>
                <div class="shift_add_from_row">
                    <label class="login2 pull-right pull-right-pro">開始時間</label>
                    <div class="form-select-list">
                        <select id="shift_add_from_hour" class="form-control custom-select-value" name="account">
                        </select>
                    </div>
                    <label class="login2 pull-right pull-right-pro"> : </label>
                    <div class="form-select-list">
                        <select id="shift_add_from_min" class="form-control custom-select-value" name="account">
                            <?php for($i=0; $i<60; $i+=5){
                                $v = $i<10 ? "0".$i : $i;
                                ?>
                                <option value="<?php echo $v; ?>"><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="shift_add_from_row">
                    <label class="login2 pull-right pull-right-pro">終了時間</label>
                    <div class="form-select-list">
                        <select id="shift_add_to_hour" class="form-control custom-select-value" name="account">
                        </select>
                    </div>
                    <label class="login2 pull-right pull-right-pro"> : </label>
                    <div class="form-select-list">
                        <select id="shift_add_to_min" class="form-control custom-select-value" name="account">
                            <?php for($i=0; $i<60; $i+=5){
                                $v = $i<10 ? "0".$i : $i;
                                ?>
                                <option value="<?php echo $v; ?>"><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="shift_add_from_row">
                    <label class="login2 pull-right pull-right-pro">シフト状態</label>
                    <div class="form-select-list">
                        <select id="shift_add_shift_type" class="form-control custom-select-value" name="account">
                            <option value=""></option>
                            <?php foreach ($shift_status as $status){ ?>
                                <option value="<?= $status['id'] ?>"><?= $status['title'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a data-dismiss="modal" class="btn-cancel" href="#" >キャンセル</a>
                <a class="btn-process" href="#">登録</a>
            </div>
        </div>
    </div>
</div>
