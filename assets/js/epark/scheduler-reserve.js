function loadReserveAddModal(staff_id, time){
    loadMask();
    $('#ReserveAddModal').modal('show');

    $('#ReserveAddModal span#reserve_time').html($('#select_date').val()+' ' + time + ':00');

    $.ajax({
        url: base_url + "epark/scheduler/loadReserveAddResource",
        type: 'post',
        data: {
            'organ_id': $('#sel_organ_id').val(),
        },
    }).done(function (res) {
        data = JSON.parse(res);
        var categories = data['categories'];
        var menus = data['menus'];
        var staffs = data['staffs'];
        var users = data['users'];

        var cat_html = "<button data='0' class=\"btn\">すべて</button>";
        for (i = 0; i < categories.length; i++) {
            category = categories[i];
            cat_html += "<button data='"+category['id']+"' class=\"btn\" style='background-color: " + category['color'] + ";'>"+category['alias']+"</button>";
        }

        var menu_html = "";
        for (i = 0; i < menus.length; i++) {
            menu = menus[i];
            menu_html += "<button menu='"+menu['menu_id']+"' data='"+menu['category_id']+"' class=\"btn\" style='background-color: " + menu['color'] + ";'>"+menu['menu_title']+"</button>";
        }

        var staff_html = "<option value=''></option>";
        for (i = 0; i < staffs.length; i++) {
            staff = staffs[i];
            staff_html += "<option value='"+staff['staff_id']+"'>"+staff['staff_first_name']+" " + staff['staff_last_name']+"</option>";
        }

        var user_html = "<option value=''></option>";
        for (i = 0; i < users.length; i++) {
            user = users[i];
            user_html += "<option value='"+user['user_id']+"'>"+user['user_first_name']+" " + user['user_last_name']+"</option>";
        }

        $('.reserve_add_category').html(cat_html);
        $('.reserve_add_menu').html(menu_html);
        $('#reserve_add_staff_id').html(staff_html);
        $('#reserve_add_staff_id').val(staff_id);
        $('#reserve_add_user_id').html(user_html);

        loadEvent();
        hideMask();
    });
}

$('#ReserveAddModal .btn-process').on('click', function (){
    var reserve_time = $('#ReserveAddModal span#reserve_time').html();
    var user_id = $('#reserve_add_user_id').val();
    var staff_id = $('#reserve_add_staff_id').val();
    var menu_ids = [];
    $('.reserve_add_menu button.selected').each(function( index ) {
        menu_ids.push($(this).attr('menu'));
    });

    if (!reserve_time){
        show_warning('予約時間を正確に選択してください。');
        return;
    }

    if (!user_id){
        show_warning('お客様を選択してください。');
        return;
    }
    if (!staff_id){
        staff_id = '';
        // show_warning('スタフを選択してください。');
        // return;
    }
    if (menu_ids.length===0){
        show_warning('予約メーニュを選択してください。');
        return;
    }

    console.log({
        'organ_id' : $('#sel_organ_id').val(),
        'staff_id' : staff_id,
        'user_id' : user_id,
        'reserve_start_time' : reserve_time,
        'menus' : menu_ids
    });

    loadMask();
    $.ajax({
        url: base_url + "epark/scheduler/saveUserReserve",
        type: 'post',
        data : {
            'organ_id' : $('#sel_organ_id').val(),
            'staff_id' : staff_id,
            'user_id' : user_id,
            'reserve_start_time' : reserve_time,
            'menus' : menu_ids
        },
        context: document.body
    }).done(function(res) {
        data = JSON.parse(res);
        if(data['isSave']){
            $('#ReserveAddModal').modal('hide');
            loadMain();
        }else{
            hideMask();
            show_error('予約できません。 ネットワーク状態、入力データを確認してください。');
        }
    });

});
function loadEvent(){
    $('.reserve_add_category button').on('click', function(){
        $('.reserve_add_menu button').removeClass('selected');
        var category = $(this).attr('data');
        if (category=='0'){
            $('.reserve_add_menu button').show();
        }else{
            $('.reserve_add_menu button').hide();
            $('.reserve_add_menu button[data="'+category+'"]').show();
        }
    })
    $('.reserve_add_menu button').on('click', function(){
        if ($(this).hasClass('selected')){
            $(this).removeClass('selected');
        }else{
            $(this).addClass('selected');
        }
    })
}
