
<div class="page-title-box">
    <form method="post">
        <div style="display: flex;">
            <div style="display: flex; width: 100%;">
                <button id="prev_select_date" type="button" class="btn btn-default outline-btn"> < </button>
                <h3 style="margin:0; margin-top: 4px; padding-left: 12px; padding-right: 12px;"> <?php echo $string_date; ?></h3>
                <button id="next_select_date" type="button" class="btn btn-default outline-btn"> > </button>
            </div>
            <div style="display: flex;">
                <button type="button" class="btn btn-success">シフト作成</button>
                <div style="width: 8px;"></div>
                <button type="button" class="btn btn-danger">ヘルプ作成</button>
                <div style="width: 8px;"></div>
                <button type="button" class="btn btn-primary">シフト変更・削除</button>
            </div>
        </div>
        <input type="hidden" name = 'mod' id="mod" value="" />
    </form>
</div>

<style>
    .epark_shift_content th, .epark_shift_content td{
        padding: 4px 12px;
    }
    .epark_shift_content .table_header th {
        padding-top: 12px;
        padding-bottom: 12px;
        color: #b37037;
    }
    .epark_shift_content .table_header{
        background-color: #e7d8bc;
    }
</style>
<div style="position: relative;">
    <div id="scroll_table" style="overflow: scroll; height: 600px; " class="epark_shift_content">
        <table border="1" style="width: <?php echo count($staffs)*140 + 150; ?>px; border-color: #d3c7b2;background-color: white;">
            <tr class="table_header">
                <th style="width: 150px;"></th>
                <?php foreach ($staffs as $staff){ ?>
                    <th style="width: 140px;text-align: center;"><?php echo empty($staff['staff_nick']) ? ($staff['staff_first_name'].' '.$staff['staff_last_name']): $staff['staff_nick']; ?></th>
                <?php } ?>
            </tr>
            <?php foreach ($shifts as $shift){ ?>
                <tr>
                    <th style="text-align: center; ">
                        <div style="padding: 12px; border: solid #9d6300 1px; background-color: #ffe1ac; border-radius: 6px; color: #9d6300">
                            <?php echo $shift['day_string']; ?>
                        </div>
                    </th>
                    <?php foreach ($staffs as $staff){ ?>
                        <td>
                            <?php if (!empty($shift[$staff['staff_id']])){ ?>
                                <?php foreach ($shift[$staff['staff_id']] as $_shift){ ?>
                                    <div>
                                        <input type="checkbox" />
                                        <?php echo date_format(new DateTime($_shift['from_time']), 'H:i') . ' ~ ' . date_format(new DateTime($_shift['to_time']), 'H:i'); ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div id="scroll_col_header" style="width: 150px; height: 580px; overflow: hidden;position:absolute; top: 0 " class="epark_shift_content">
        <table border="1" style="150px; border-color: #d3c7b2;background-color: white;">
            <tr class="table_header">
                <th style="width: 150px;">&nbsp;</th>
            </tr>
            <?php foreach ($shifts as $shift){ ?>
                <tr>
                    <th style="text-align: center; ">
                        <div style="padding: 12px; border: solid #9d6300 1px; background-color: #ffe1ac; border-radius: 6px; color: #9d6300">
                            <?php echo $shift['day_string']; ?>
                        </div>
                    </th>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div id="scroll_data_header" style="width: 98.5%; overflow: hidden;position:absolute; top: 0 " class="epark_shift_content">
        <table border="1" style="width: <?php echo count($staffs)*140 + 150; ?>px; border-color: #d3c7b2;background-color: white;">
            <tr class="table_header">
                <th style="width: 150px;"></th>
                <?php foreach ($staffs as $staff){ ?>
                    <th style="width: 140px;text-align: center;"><?php echo empty($staff['staff_nick']) ? ($staff['staff_first_name'].' '.$staff['staff_last_name']): $staff['staff_nick']; ?></th>
                <?php } ?>
            </tr>
        </table>
    </div>
    <div style="position:absolute; top: 0; width:150px; " class="epark_shift_content">
        <table border="1" style="width: 150px; border-color: #d3c7b2;background-color: white;">
            <tr class="table_header">
                <th style="width: 150px;">&nbsp;</th>
            </tr>
        </table>
    </div>
</div>

<script>
    $('#scroll_table').scroll(function(e){
        $('#scroll_data_header').scrollLeft($('#scroll_table').scrollLeft());
        $('#scroll_col_header').scrollTop($('#scroll_table').scrollTop());
    });
</script>

