
var orderobj = '';
var dragObj = '';
var target_staff_id = '';
var target_position = '';

$( document ).ready(function() {
    isDrag = false;
    isDrop = false;

    isOrderDrag = false;
    isOrderDrop = false;
    isOrderMoveEnable = false;
    isWaitingDrop = false;

    $('.staff_name').draggable({
        start: function(event, ui) {
            isDrag = true;
            console.log('dragstart');
            $(this).css('z-index', 9999);
        },
        stop: function(event, ui) {
            if(!isDrop) {
                $(this).css('left', ui.originalPosition.left);
                $(this).css('top', ui.originalPosition.top);
            }
            if(isDrop){
                loadMask();
                $.ajax({
                    url: base_url + "api/exchangeSort",
                    type: 'post',
                    data : {'staff_id' : 1,
                        'move_staff' : $(this).attr('data'),
                        'target_staff' : drop_staff
                    },
                }).done(function(res) {
                    data = JSON.parse(res);
                    if(data['isSave']){
                        loadMain();
                    }else{
                        hideMask();
                        alert(data['message']);
                    }
                });
            }
            isDrop = false;
        }
    });

    $( ".staff_name" ).droppable({
        hoverClass: "staff_droppable_active",
        drop: function( event, ui ) {
            if (isDrag){
                isDrop = true;
                drop_staff = $(this).attr('data');
            }
        }
    });

    $(".cell-row .epark-order:not('.waiting')").draggable({
        start: function(event, ui) {
            isOrderDrag = true;
            $(this).css('z-index', 9999);
            $(this).css('opacity', 0.3);
            orderobj = $(this).clone();
            orderobj.attr('order', 'clone');
            dragObj = $(this);
        },
        stop: function(event, ui) {
            $(this).css('left', ui.originalPosition.left);
            $(this).css('top', ui.originalPosition.top);
            $(this).css('z-index', 1);
            if (isWaitingDrop){
                updateOrderWaiting();
            }

            tmpTime = orderobj.attr('from').split(':');
            t_f= tmpTime[0] * 60 + parseInt(tmpTime[1]);

            if(isOrderDrop && isOrderMoveEnable && dragObj.attr('from') != t_f) {
                updateReserve();
            }else{
                orderobj.hide();
                $('#clone').remove();
                $(this).css('opacity', 1);
            }
            isWaitingDrop = false;
            isOrderMoveEnable = false;
            isOrderDrop = false;
        }
    });

    $( ".shift-apply" ).droppable({
        hoverClass: "staff_droppable_active",
        drop: function( event, ui ) {
            if (isOrderDrag){
                isOrderDrop = true;
                //isOrderDrop = true;
                //drop_staff = $(this).attr('data');
            }
            if (isWaitingDrag){
                isWaitingDragDrop = true;
            }


        },
        over: function () {
            // if (!isOrderDrag) return;
            $(this).append(orderobj);
            orderobj.css('opacity', 1);
            orderobj.css('top', 0);
            time = $(this).attr('title');
            from = parseInt(time.split(':')[0]) * 60 + parseInt(time.split(':')[1]);
            var obj_length = parseInt(orderobj.attr('length'));

            target_from = parseInt((from-obj_length/2)/5)*5;
            target_to = target_from + parseInt(obj_length);

            tf_h = ("0" + parseInt(target_from/60).toString()).slice(-2);
            tf_m = ("0" + (target_from % 60).toString()).slice(-2);
            tt_h = ("0" + parseInt(target_to/60).toString()).slice(-2);
            tt_m = ("0" + (target_to % 60).toString()).slice(-2);

            left = (target_from-$('#line_from').val())/($('#line_to').val() - $('#line_from').val()) * 100;
            orderobj.css('left', left+'%');
            // orderobj.children('p.time-comment').html(tf_h+":"+tf_m+"~"+tt_h+":"+tt_m);
            orderobj.children('div').children('.time-comment').html(tf_h+":"+tf_m+"~"+tt_h+":"+tt_m);
            orderobj.attr('from', tf_h+":"+tf_m);

            if ($(this).parent().hasClass('shift-cell')){
                target_staff_id = $(this).parent(".shift-cell").attr('staff');
                target_position ='';
                //target_position = $(".table-cell .epark-order[order='"+ dragObj.attr('order')+"']").parent().attr('position');
            }else{
                //target_staff_id = $(".shift-cell .epark-order[order='"+ dragObj.attr('order')+"']").parent().attr('staff');
                target_staff_id ='';
                target_position = $(this).parent(".table-cell").attr('position');
            }

            isApply = true;
            for (i=target_from ; i<target_to; i+=5){
                t_h = parseInt(i/60).toString();
                t_m = ("0" + (i % 60).toString()).slice(-2);
                if (!$(this).parent().children("[title='"+t_h+":"+t_m+"']").hasClass('shift-apply')){
                    isApply = false;
                    break;
                }
                $(this).parent().children('.epark-order').each(function( index ) {
                    if ($(this).attr('order')!=dragObj.attr('order') && $(this).attr('order')!='clone'){
                        if ($(this).attr('from')<=i && (parseInt($(this).attr('from'))+parseInt($(this).attr('length')))>i){
                            isApply = false;
                        }
                    }
                });
                if (!isApply) break;
            }

            if (!isApply) orderobj.hide(); else orderobj.show();
            isOrderMoveEnable = isApply;
            //alert(time);
        },
        out : function () {
            //isOrderDrop = false;
           // orderobj.hide();
            //$(this).html('');

        }
    });

    $( ".none" ).droppable({
        over : function (){
                orderobj.hide();
        }
    });

    $("#target_to_other_date").droppable({
        drop:function () {
            isWaitingDrop = true;
        },
        over:function () {

            if (isWaitingDrag) {


            }else{
                $(this).addClass('waiting');
                orderobj.addClass('waiting');
                $(this).append(orderobj);
                orderobj.show();
            }
        },
        out:function (){
            if (isWaitingDrag) {

            }else{
                orderobj.removeClass('waiting');
                if ($(this).childElementCount===0)
                    $(this).removeClass('waiting');
                orderobj.hide();
            }
        }
    })


    isWaitingDrag = false;
    isWaitingDragDrop = false;

    $("#target_to_other_date .epark-order").draggable({
        start: function(event, ui) {
            isWaitingDrag = true;
            orderobj = $(this).clone();
            orderobj.attr('order', 'clone');
            dragObj = $(this);
            $(this).css('z-index', 9999);
            $(this).css('opacity', 0.3);
            $(this).removeClass('waiting');
        },
        stop: function(event, ui) {
            $(this).css('left', ui.originalPosition.left);
            $(this).css('top', ui.originalPosition.top);
            // $(this).css('z-index', 1);
            console.log(isWaitingDrop);
             if (isWaitingDragDrop && isOrderMoveEnable){
                 updateReserve();
             }else{
                orderobj.hide();
                //$('#clone').remove();
                $(this).css('opacity', 1);
            }
            isWaitingDragDrop = false;
            isOrderMoveEnable = false;
            isWaitingDrag = false;
        }
    });
});

function updateOrderWaiting(){
    loadMain();
    $.ajax({
        url: base_url + "epark/scheduler/ajaxMoveReserveWaiting",
        type: 'post',
        data : {
            'order_id' : dragObj.attr('order'),
            'is_wait' : 1,
        },
    }).done(function(res) {
        data = JSON.parse(res);
        if(data['isUpdate']){
            loadMain();
        }else{
            hideMask();
            show_error('選択した時間には予約できません。');
            dragObj.css('opacity', 1);
            orderobj.hide();
            $('#clone').remove();
        }
    });
}

function updateReserve(){
    $.confirm({
        title: '予約時間変更',
        content: '予約時間を'+orderobj.attr('from')+'で変更しますか。',
        buttons: {
            変更: function () {
                loadMask();
                $.ajax({
                    url: base_url + "epark/scheduler/ajaxUpdateReserveTime",
                    type: 'post',
                    data : {
                        'staff_id' : target_staff_id,
                        'position' : target_position,
                        'reserve_time' : $('#select_date').val() + " " + orderobj.attr('from') + ":00",
                        'reserve_id' : dragObj.attr('order'),
                        'time_length' : dragObj.attr('to') - dragObj.attr('from')
                    },
                }).done(function(res) {
                    data = JSON.parse(res);
                    if(data['isSave']){
                        loadMain();
                    }else{
                        hideMask();
                        show_error("選択した時間には予約できません。");
                        dragObj.css('opacity', 1);
                        orderobj.hide();
                        $('#clone').remove();
                    }
                });
            },
            キャンセル: function () {
                dragObj.css('opacity', 1);
                orderobj.hide();
                $('#clone').remove();
            },
        }
    });
}
