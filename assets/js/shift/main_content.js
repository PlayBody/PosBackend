$(window).load(function() {
    setInterval(function () {
        loadMain();
    },1000 * 60);
});

$( document ).ready(function() {
    loadMain();
});


function loadMain(){
    var select_date = $('#select_date').val();
    if (select_date=='') {
        select_date = $('#today_date').val();
        $('#select_date').val(select_date);
    }
    var organ_id = $('#sel_organ_id').val();
    var time_mode = $('#time_mode').val();
    var type_mode = $('#type_mode').val();
    var sel_time = $('#sel_time').val();
    loadMask();
    $.ajax({
        url: base_url + "shift/ajax_load_main",
        type: 'post',
        data: {
            'select_date' : select_date,
            'organ_id' : organ_id,
            'time_mode' : time_mode,
            'type_mode' : type_mode,
            'sel_time' : sel_time,
        },
        context: document.body
    }).done(function(res) {
        $('#epark-content').html(res);
        //loadReserveList();
        hideMask();
    });
}

function loadMask(){
    $('#load-mask').show();
}

function hideMask(){
    $('#load-mask').hide();
}

$('#btn_to_before_date').on('click', function () {
    var select_date = $('#select_date').val();
    $('#select_date').val(getBeforeOrNextDate(select_date, true));
    $('#epark-left-content').hide();
    loadMain();
});

$('#btn_to_today_date').on('click', function () {
    $('#select_date').val($('#today_date').val());
    $('#epark-left-content').hide();
    loadMain();
});

$('#btn_to_next_date').on('click', function () {
    var select_date = $('#select_date').val();
    $('#select_date').val(getBeforeOrNextDate(select_date, false));
    $('#epark-left-content').hide();
    loadMain();
});

$('#btn_refresh').on('click', function () {
    loadMain();
});

$('#sel_organ_id').on('change', function () {
    $('#epark-left-content').hide();
    loadMain();
});
//
// $('#select_date').on('change', function (){
//     alert('tets');
//     $('#epark-left-content').hide();
//     loadMain();
// });


function getBeforeOrNextDate(curDate, isBefore){

    var d = new Date(curDate);
    if (isBefore){
        d.setDate(d.getDate() - 1);
    }else{
        d.setDate(d.getDate() + 1);
    }

    var getYear = d.getUTCFullYear();
    var getMonth = ('0' + (d.getUTCMonth() + 1)).slice(-2);
    var getDay = ('0' + d.getUTCDate()).slice(-2);
    return getYear + "-" + getMonth + "-" + getDay;

}

function show_warning(msg){
    Lobibox.notify('warning', {
        title: 'データエラー',
        sound: false,
        msg: msg
    });
}
function show_error(msg){
    Lobibox.notify('error', {
        title: 'エラーが発生しました。',
        sound: false,
        msg: msg
    });
}

function loadShiftAddModal(){
    loadMask();
   $('#ShiftAddModal').modal('show');
    $.ajax({
        url: base_url + "api/staff-list",
        type: 'post',
        data: {
            'organ_id': $('#sel_organ_id').val()
        },
    }).done(function (res) {
        result = JSON.parse(res);
        if (result['is_load']){
            var staffs = result['data'];
            var html = "<option value=''></option>";
            for (i = 0; i < staffs.length; i++) {
                var staff = staffs[i];
                html += "<option value='" + staff['staff_id']+ "'>" + staff['staff_first_name'] + " " + staff['staff_last_name'] + "</option>";
            }
            $('.shift_add_staff_name').html(html);
            
            var from = $('#organ_from_time').val();
            var to = $('#organ_to_time').val();

            var openH = Math.floor(from / 60);
            var openM = from % 60;
            var closeH = Math.floor(to / 60);
            var closeM = to % 60;

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
        
            // $.ajax({
            //     url: base_url + "apiorgans/loadOrganOpenCloseHour",
            //     type: 'post',
            //     data: {
            //         'organ_id': $('#sel_organ_id').val(),
            //         'select_date': $('#select_date').val()
            //     },
            // }).done(function (res) {
            //     data = JSON.parse(res);
            //     var openH = data['open_hour'];
            //     var openM = data['open_min'];
            //     var closeH = data['close_hour'];
            //     var closeM = data['close_min'];
    
            //     loadReserveList();
    
            // });
        }else{
            
        }
        
        hideMask();
    });
}

function show_warning(msg){
    Lobibox.notify('warning', {
        title: 'データエラー',
        sound: false,
        msg: msg
    });
}
function show_error(msg){
    Lobibox.notify('error', {
        title: 'エラーが発生しました。',
        sound: false,
        msg: msg
    });
}