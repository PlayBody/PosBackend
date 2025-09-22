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
        url: base_url + "epark/scheduler/ajaxMainLoad",
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
        loadReserveList();
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


$('#btn_sync_epark').on('click', function () {
    var conf = $(confirm('Eparkに同期しますか？'));
    if (!conf) return;
   
        var select_date = $('#select_date').val();
        if (select_date=='') {
            select_date = $('#today_date').val();
            $('#select_date').val(select_date);
        }
        var organ_id = $('#sel_organ_id').val();
        loadMask();
        $.ajax({
            url: base_url + "api/epark-sync",
            type: 'post',
            data: {
                'organ_id' : organ_id,
                'from_date' : select_date,
            }
        }).done(function(res) {
            hideMask();
            data = JSON.parse(res);
            var result = data['is_load'];
            if (result){
                
                Lobibox.notify('info', {
                    title: '',
                    sound: false,
                    msg: '同期しました。'
                });
            }else{
                
                Lobibox.notify('error', {
                    title: '',
                    sound: false,
                    msg: 'エラーが発生しました。'
                });
            }
            loadMain();
        });
             
})

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


