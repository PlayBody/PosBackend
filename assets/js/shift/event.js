$('#schedule_mod_time button').on('click', function(){
    var v = $(this).val();
    if ($('#time_mode').val() == v) return;
    $('#time_mode').val(v);
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

$('#schedule_mod_type button').on('click', function(){
    var v = $(this).val();
    if ($('#type_mode').val() == v) return;
    $('#type_mode').val(v);
    loadMain();
})

$('#btn_shift_add').on('click', function () {
    loadShiftAddModal();
})


$('#ShiftAddModal .btn-process').on('click', function () {
    var staff_id = $('#shift_add_staff_id').val();
    var from_time = $('#select_date').val() + " " + $('#shift_add_from_hour').val() + ":" + $('#shift_add_from_min').val() + ":00";
    var to_time = $('#select_date').val() + " " + $('#shift_add_to_hour').val() + ":" + $('#shift_add_to_min').val() + ":00";
    var shift_type = $('#shift_add_shift_type').val();
    
    if (staff_id == '') {
        show_warning('スタッフを指定してください。');
        return;
    }
    if (shift_type == '') {
        show_warning('シフト状態を指定してください。');
        return;
    }
    loadMask();

    $.ajax({
        url: base_url + "apishifts/saveShift",
        type: 'post',
        data : {
            'organ_id' : $('#sel_organ_id').val(),
            'staff_id' : staff_id,
            'from_time' : from_time,
            'to_time' : to_time,
            'shift_type' : shift_type,
        },
        context: document.body
    }).done(function(res) {
        data = JSON.parse(res);
        if(data['isSave']){
            $('#ShiftAddModal').modal('hide');
            loadMain();
        }else{
            hideMask();
            show_error(data['message']);
        }
    });

    // $('#ShiftAddModal .btn-cancel').trigger('click');
});