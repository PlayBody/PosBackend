var leftPanels = [];
$(window).load(function() {
    $('.epark-left-nav button').on('click', function () {
        var id = $(this).attr('id');
        if (id == 'open' || id == 'close') {
            ctrlLeftPanel(id);
        }
        if (id == 'reserve') {
            leftPanels = [];
            openLeftPanel();
            loadReserveList();
        }
    });

    loadReserveList();

})
function openLeftPanel(){
    if ($('#epark-left-content').css('display')=='none')
        $('#epark-left-content').toggle();
    $('.epark-left-nav button#open').show();
    $('.epark-left-nav button#close').hide();
}
function ctrlLeftPanel(id){
    activePanelParts();
    $('#epark-left-content').toggle();
    if ($('#epark-left-content').css('display')=='none'){
        $('.epark-left-nav button#open').show();
        $('.epark-left-nav button#close').hide();
    }else{
        $('.epark-left-nav button#open').hide();
        $('.epark-left-nav button#close').show();
        leftPanels=[];
        loadReserveList();
    }
}

$('#left_reserves .tab_header1 button').click(function () {
    $('#left_reserves .tab_header1 button').removeClass('active');
    $(this).addClass('active');
    loadReserveList();
})
function loadReserveList(){
    var view_type="reserve";
    $('#left_reserves .tab_header1 button').each(function(){
        if ($(this).hasClass('active')){
            view_type = $(this).attr('id');
        }
    })

    if (view_type=='reserve'){
        loadLeftMask();
        $.ajax({
            url: base_url + "epark/scheduler/loadReserveList",
            type: 'post',
            data: {
                'select_date' : $('#select_date').val(),
                'organ_id' : $('#sel_organ_id').val(),
            },
        }).done(function(res) {
            $('#left_reserves .tab_content1').html(res);
            hideLeftMask();
        });
    }else{
        $('#left_reserves .tab_content1').html('<div class="no-data">予約情報がありません。</div>');
        hideLeftMask();

    }

    activePanelParts();
}
function loadLeftReserveDetail(order_id){
    loadLeftMask();
    $.ajax({
        url: base_url + "apiorders/loadOrderInfo",
        type: 'post',
        data: {
            'order_id' : order_id,
        },
    }).done(function(res) {
        var data = JSON.parse(res);
        if (!data['isLoad']){
            show_error('API ERROR');
            return;
        }
        var order = data['order'];
        $('#left_user_head .user_name').html(order['user_name']);
        $('#left_user_head .user_no').html(order['user_no']);
        $('#left_user_head .user_tel').html(order['user_tel']);
        $('#left_user_head .user_comment').html(order['user_comment']);
        $('#left_user_head button').attr('user', order['user_id']);
        $('#left_user_head .user_title i').removeClass('i-sex-1').removeClass('i-sex-2').addClass('i-sex-'+order['user_sex']);

        $('#left_reserve_detail .user_name').html(order['user_name']);
        $('#left_reserve_detail .order_reg_date').html(order['reg_date'].substring(0, 16));
        $('#left_reserve_detail .order_from_time').html(order['from_time'].substring(0, 16));
        $('#left_reserve_detail .order_table_position').html(order['table_position']);
        $('#left_reserve_detail .order_staff_name').html(order['staff_name']);
        if (order['select_staff_type']==3) $('#left_reserve_detail .order_sel_staff').show(); else $('#left_reserve_detail .order_sel_staff').hide();
        $('#left_reserve_detail .menus').html('');
        $('#left_reserve_detail .menus_plus').remove();
        $('#left_reserve_detail button').attr('order', order['id']);

        $('#left_reserve_detail #btn_complete_order').attr('paymethod', order['pay_method']);
        $('#left_reserve_detail #btn_reset_order').attr('paymethod', order['pay_method']);

        if (order['status'] == 7){
            $('#left_reserve_detail #btn_complete_order').show();
        }else{
            $('#left_reserve_detail #btn_complete_order').hide();
        }

        if (order['is_reset_temp']){
            $('#left_reserve_detail #btn_reset_order').show();
        }else{
            $('#left_reserve_detail #btn_reset_order').hide();
        }

        if (order['status'] == 6 || order['status']==4){
            $('#left_reserve_detail #btn_exit_order').prop('disabled', false);
        }else{
            $('#left_reserve_detail #btn_exit_order').prop('disabled', true);
        }

        if (order['is_reset']==1){
            $('#left_reserve_detail button#btn_reset_order').hide()
        }else{
            $('#left_reserve_detail button#btn_reset_order').show()
        }

        leftPanels.push('reserve_detail');
        activePanelParts();
        hideLeftMask();
    });
}

function loadLeftMask(){
    $('#load-left-mask').show();
}
function hideLeftMask(){
    $('#load-left-mask').hide();
}

$('#left_reserve_detail #btn_complete_order').on('click', function () {
    order_id = $(this).attr('order');
    pay_method = $(this).attr('paymethod');
    if (!order_id) return;
    if (!pay_method) pay_method = 3;

    $.confirm( {
        title: '予約完了',
        content: function(){
            return '<div class="order_complete_confirm">お支払い方法を選択してください。　' +
                '<select id="aSelect" class="form-control">' +
                    '<option value="2">現金</option>' +
                    '<option value="3">その他電子マネー</option>' +
                    '<option value="1">クレジットカード</option>' +
                '</select></div>'; // put in the #aSelect html,
        },
        onContentReady : function() {
            this.$content.find('#aSelect').val(pay_method); // initialize the plugin when the model opens.
        },

        buttons: {
            完了: function () {
                loadMask();
                $.ajax({
                    url: base_url + "apiorders/resetOrder",
                    type: 'post',
                    data : {'order_id' : order_id, 'pay_method':this.$content.find('#aSelect').val()},
                    context: document.body
                }).done(function(res) {
                    data = JSON.parse(res);
                    if(data['isUpdate']){
                        close_leftPan('reserve_detail')
                        loadReserveList();
                        loadMain();
                    }else{
                        show_error('完了できません。');
                    }
                });
            },
            キャンセル: function () {
            },
        }

    });


})

$('#left_reserve_detail #btn_reset_order').on('click', function () {
    order_id = $(this).attr('order');
    if (!order_id) return;
    loadMask();
    $.ajax({
        url: base_url + "apiorders/resetOrderTemp",
        type: 'post',
        data : {'order_id' : order_id},
        context: document.body
    }).done(function(res) {
        data = JSON.parse(res);
        if(data['isUpdate']){
            close_leftPan('reserve_detail')
            loadReserveList();
            loadMain();
        }else{
            hideMask();
            show_error('リセットできません。');
        }
    });
})

$('#btn_user_reserve_history').on('click', function () {
    $('#left_reserve_detail').hide();
    $('#left_user_reserves').show();
    $('#left_user_reserves .tab_header1 button').attr('user', $(this).attr('user'));
    loadUserReserves($(this).attr('user'))
    // user_id = $(this).attr('user');
    // if (!user_id) return;
    // $.ajax({
    //     url: base_url + "apiorders/resetOrderTemp",
    //     type: 'post',
    //     data : {'order_id' : order_id},
    //     context: document.body
    // }).done(function(res) {
    //     data = JSON.parse(res);
    //     if(data['isUpdate']){
    //         close_leftPan('reserve_detail')
    //         loadReserveList();
    //         loadMain();
    //     }else{
    //         show_error('リセットできません。');
    //     }
    // });
})

$('#left_reserve_detail #btn_delete_order').on('click', function (){
    order_id = $(this).attr('order');
    if (!order_id) return;


    $.confirm({
        title: '予約削除',
        content: 'この予約データを削除しますか？',
        buttons: {
            削除: function () {
                loadMask();
                $.ajax({
                    url: base_url + "apiorders/deleteOrder",
                    type: 'post',
                    data : {'order_id' : order_id},
                }).done(function(res) {
                    data = JSON.parse(res);
                    if(data['isDelete']){
                        loadMask();
                        close_leftPan('reserve_detail');
                        loadReserveList();
                        loadMain();
                    }else{
                        hideMask();
                        show_error('エラーが発生しました。');
                    }
                });
            },
            キャンセル: function () {
                return;
            },
        }
    });
})

$('#left_reserve_detail #btn_exit_order').on('click', function (){
    order_id = $(this).attr('order');
    if (!order_id) return;

    $.ajax({
        url: base_url + "apiorders/updateStatus",
        type: 'post',
        data : {'order_id' : order_id, 'status' : 7},
        context: document.body
    }).done(function(res) {
        data = JSON.parse(res);
        if(data['isUpdate']){
            loadMask();
            close_leftPan('reserve_detail');
            loadReserveList();
            loadMain();
        }else{
            hideMask();
            show_error('完了できません。');
        }
    });
})

function loadUserReserves(user){
    var service = '';
    $('#left_user_reserves .tab_header1 button').each(function () {
        if ($(this).hasClass('active')) service = $(this).attr('service');
    })

    loadLeftMask();
    $.ajax({
        url: base_url + "epark/scheduler/ajaxLoadUserReserves",
        type: 'post',
        data : {
            'user_id' : user,
            'service' : service,
        },
    }).done(function(res) {
        $('#left_user_reserves .tab_content1').html(res);
        hideLeftMask();
    });
}

$('#left_user_reserves .tab_header1 button').on('click', function(){
    $('#left_user_reserves .tab_header1 button').removeClass('active');
    $(this).addClass('active');
    user_id = $(this).attr('user');
    if (!user_id) return;
    loadUserReserves(user_id);
});


$('#epark-left-content button.close_btn').on('click', function () {
    var panel = $(this).attr('panel');
    close_leftPan(panel);
    activePanelParts();
})

function loadShitEditData(shift_id){
    loadLeftMask();

    $.ajax({
        url: base_url + "epark/scheduler/ajaxLoadShiftInfo",
        type: 'post',
        data : {'shift_id' : shift_id,
            'organ_from_time' : $('#organ_from_time').val(),
            'organ_to_time' : $('#organ_to_time').val()
        },
        context: document.body
    }).done(function(res) {
        $('#left_shift_edit .content').html(res);
        leftPanels.push('shift_edit');
        activePanelParts();
        $('#left_shift_edit button').attr('shift', shift_id);
        hideLeftMask();
    });
}

$('#left_shift_edit button#btn_delete_shift').click(function () {

    $.confirm( {
        title: 'シフト削除',
        content: '選択したシフトを本当に削除しますか。',
        buttons: {
            削除: function () {
                loadMask();
                $.ajax({
                    url: base_url + "apishifts/deleteShift",
                    type: 'post',
                    data : {'shift_id' : shift_id},
                }).done(function(res) {
                    if(res){
                        close_leftPan('shift_edit');
                        activePanelParts();
                        loadMain();
                    }
                });
            },
            キャンセル: function () {
            },
        }
    });
})

$('#left_shift_edit button#btn_update_shift').click(function () {
    from_time = $('#select_date').val()+" " + $('#shift_edit_from_hour').val() + ":" + $('#shift_edit_from_min').val() + ":00";
    to_time = $('#select_date').val()+" " + $('#shift_edit_to_hour').val() + ":" + $('#shift_edit_to_min').val() + ":00";
    shift_id = $(this).attr('shift');
    if (shift_id==null || shift_id=='') return;

    loadMask();
    $.ajax({
        url: base_url + "apishifts/saveShift",
        type: 'post',
        data : {
            'shift_id' : shift_id,
            'organ_id' : $('#sel_organ_id').val(),
            'staff_id' : $('#shift_edit_staff_id').val(),
            'from_time' : from_time,
            'to_time' : to_time,
            'shift_type' : $('#shift_edit_type').val(),
        },
    }).done(function(res) {
        data = JSON.parse(res);
        if(data['isSave']){
            close_leftPan('shift_edit');
            activePanelParts();
            loadMain();
        }else{
            hideMask();
            show_error(data['message']);
        }
    });
})

function close_leftPan(idStr){
    var i = leftPanels.indexOf(idStr);
    leftPanels.splice(i, 1);

}

function activePanelParts(){
    $('.left-content').hide();
    var foc = 'reserve_list';
    if (leftPanels.length>0){
        foc = leftPanels[leftPanels.length-1];
    }
    switch (foc){
        case 'reserve_detail':
            $('#left_user_head').show();
            $('#left_reserve_detail').show();
            break;
        case 'shift_edit':
            $('#left_shift_edit').show();
            break;
        default:
            $('#left_reserves').show();
    }
}