<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h2 class="header-title">手温イオンモール木曽川店予算 </h2>
            <form action="<?php echo base_url() ?>excelexport/export2" method="POST" id="excel_export_2">
                <input type="hidden" id="mod" name="mod" />
                <div class="form-group" style="padding-top: 12px; padding-left: 20px;">
                    <div style="display: inline-flex">
                        <select class="form-control" name="date_year">
                            <?php for($i=$date_year-5;$i<=$date_year+5;$i++){ ?>
                                <option <?php if($date_year==$i) echo 'selected'; ?> value="<?php echo $i; ?>"><?php echo $i.'年'; ?></option>
                            <?php }  ?>
                        </select>
                    </div>
                    <div style="display: inline-flex">
                        <select class="form-control" name="date_month">
                            <?php for($i=1;$i<=12;$i++){ ?>
                                <option <?php if(intval($date_month)==$i) echo 'selected'; ?> value="<?php echo $i; ?>"><?php echo $i.'月'; ?></option>
                            <?php }  ?>
                        </select>
                    </div>

                </div>
                <div style="height: 1px; background-color: #e1e1e1;margin-bottom: 12px;"></div>
                <div style="padding-left: 20px;padding-bottom: 12px;">
                    <a class="btn-primary btn" onclick="exportExcel();">EXCELエクスポート</a>
                    <a href="<?php echo base_url(); ?>excelexport" class="btn-default btn">戻る</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function exportExcel(){
        $('#mod').val('export');
        $('#excel_export_2').submit();
    }
</script>
