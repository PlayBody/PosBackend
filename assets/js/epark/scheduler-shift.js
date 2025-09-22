
function loadShiftAddModal(){
    loadMask();
   $('#ShiftAddModal').modal('show');
    $.ajax({
        url: base_url + "api/loadStaffs",
        type: 'post',
        data: {
            'organ_id': $('#sel_organ_id').val()
        },
    }).done(function (res) {
        data = JSON.parse(res);
        var staffs = data['staffs'];
        var html = "<option value=''></option>";
        for (i = 0; i < staffs.length; i++) {
            var staff = staffs[i];
            html += "<option value='" + staff['staff_id']+ "'>" + staff['staff_first_name'] + " " + staff['staff_last_name'] + "</option>";
        }
        $('.shift_add_staff_name').html(html);


        $.ajax({
            url: base_url + "apiorgans/loadOrganOpenCloseHour",
            type: 'post',
            data: {
                'organ_id': $('#sel_organ_id').val(),
                'select_date': $('#select_date').val()
            },
        }).done(function (res) {
            data = JSON.parse(res);
            var openH = data['open_hour'];
            var openM = data['open_min'];
            var closeH = data['close_hour'];
            var closeM = data['close_min'];
            var html = "";
            for (i = openH; i <= closeH; i++) {
                str = ("0" + i).slice(-2);
                html += "<option value='" + str+ "'>" + str + "</option>";
            }
            $('#shift_add_from_hour').html(html);
            $('#shift_add_to_hour').html(html);
            $('#shift_add_from_min').val(("0" + openM).slice(-2));
            var closeHourTime = closeM > 0 ? closeH-1: closeH;
            $('#shift_add_to_hour').val(("0" + closeHourTime).slice(-2));
            $('#shift_add_to_min').val(("0" + closeM).slice(-2));

            loadReserveList();
            hideMask();

        });
    });
}

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
    //
    // function show_warning(msg){
    //     Lobibox.notify('warning', {
    //         title: 'データエラー',
    //         sound: false,
    //         msg: msg
    //     });
    // }
    // function show_error(msg){
    //     Lobibox.notify('error', {
    //         title: 'エラーが発生しました。',
    //         sound: false,
    //         msg: msg
    //     });
    // }

    $('.epark-shift-other').on('click', function(){

    });