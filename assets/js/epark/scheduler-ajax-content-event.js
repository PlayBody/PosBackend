

$('#schedule_mod_time button').on('click', function(){
    var v = $(this).val();
    if ($('#time_mode').val() == v) return;
    $('#time_mode').val(v);
    loadMain();
})

$('#schedule_mod_type button').on('click', function(){
    var v = $(this).val();
    if ($('#type_mode').val() == v) return;
    $('#type_mode').val(v);
    loadMain();
})

$('#time_mark_line button').on('click', function(){
    var v = $(this).val();
    if ($('#sel_time').val() == v) return;
    $('#sel_time').val(v);

    if ($('#time_mode').val()==2){
        $('#time_mark_line button').removeClass('active');
        $(this).addClass('active');
        return;
    }
    loadMain();
})

$('#btn_shift_add').on('click', function () {
    loadShiftAddModal();
})

$('.cell.shift-apply').on('click', function () {
    loadReserveAddModal($(this).parent().attr('staff'), $(this).attr('title'));

    shift_id = $(this).attr('shift');
    if (!shift_id) return;

    close_leftPan('shift_edit');
    openLeftPanel();
    loadShitEditData(shift_id);
})

$('.epark-appoint').on('mouseover', function(){
    id = $(this).attr('order');
    $(this).addClass('pre-select');
    $(".epark-order[order='"+id+"']").addClass('pre-select');
})

$('.epark-appoint').on('mouseout', function(){
    id = $(this).attr('order');
    $(this).removeClass('pre-select');
    $(".epark-order[order='"+id+"']").removeClass('pre-select');
})


$('.epark-order').on('click', function(){
    if($(this).attr('order')){
        leftPanels = [];
        loadLeftReserveDetail($(this).attr('order'));
        openLeftPanel();
    }
})


$('.epark-shift-other').click(function(){
    if ($(this).hasClass('ap-lock')) return;
    shift_id = $(this).attr('shift');
    if (!shift_id) return;
    close_leftPan('shift_edit');
    openLeftPanel();
    loadShitEditData(shift_id);
})
