
var showMsg = function (tipo, msg) {
    var content = {};
    content.message = msg;
    if (tipo == 'success') {
        content.title = 'Éxito';
        content.icon = 'icon flaticon-interface-5';
    } else {
        content.title = 'Error';
        content.icon = 'icon flaticon-cancel';
    }

    var notify = $.notify(content, {
        type: tipo,
        allow_dismiss: true,
        newest_on_top: true,
        mouse_over: false,
        showProgressbar: false,
        spacing: 10,
        timer: 5000,
        placement: {
            from: 'top',
            align: 'right'
        },
        offset: {
            x: 30,
            y: 30
        },
        delay: 1000,
        z_index: 10000,
        animate: {
            enter: 'animated bounceInRight',
            exit: 'animated bounceOutRight'
        }
    });
};
