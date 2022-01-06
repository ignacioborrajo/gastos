//== Class definition

var BootstrapSelect = function () {

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

    var recuperar_viajes = function () {
        $.ajax({
            url: "api/viajes",
            method: "GET",
            dataType: "json"
        }).done(function (data) {
            console.log(data);
            $('#total_viajes').text(data.total + '€');

            for(var i = 0;i<data.viajes.length;i++) {
                var viaje = data.viajes[i];
                var $items = $('<div class="grid-item col-xs-12 col-md-4">' +
                                    '<div class="grid-item-content">' +
                                        '<div class="m-portlet m-portlet--skin-light m-portlet--bordered-semi">' +
                                            '<div class="m-portlet__head">' +
                                                '<div class="m-portlet__head-caption">' +
                                                    '<div class="m-portlet__head-title">' +
                                                        '<span class="m-portlet__head-icon"><i class="fa fa-plane"></i></span>' +
                                                        '<h3 class="m-portlet__head-text">' + viaje.nombre + '</h3>' +
                                                    '</div>' +
                                                '</div>' +
                                            '</div>' +
                                            '<div class="m-portlet__body">' +
                                                '<div class="row">' +
                                                    '<div class="col-6">' +
                                                        '<center><i class="fa fa-sign-out"></i> ' + viaje.fecha_inicio + '</center>' +
                                                    '</div>' +
                                                    '<div class="col-6">' +
                                                        '<center><i class="fa fa-sign-in"></i> ' + viaje.fecha_fin + '</center>' +
                                                    '</div>' +
                                                '</div>' +
                                                '<div class="row">' +
                                                    '<div class="col-12">' +
                                                        '<div class="m-widget25 text-center">' +
                                                            '<span id="total_viajes" class="m-widget25__price m--font-brand">' + viaje.total + '€</span>' +
                                                        '</div>' +
                                                    '</div>' +
                                                '</div>' +
                                                '<div class="row">' +
                                                    '<a href="viaje.php?id=' + viaje.id + '" class="ver_viaje btn m-btn--pill btn-block btn-primary">Ver Viaje</a>' +
                                                '</div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>');
                // append items to grid
                isotope.append($items).isotope('appended', $items);
            }

        });
    };

    $('#fecha_inicio').datetimepicker({
        todayHighlight: true,
        autoclose: true,
        startView: 2,
        minView: 2,
        forceParse: 0,
        pickerPosition: 'bottom-left',
        todayBtn: true,
        format: 'dd/mm/yyyy'
    });

    $('#fecha_fin').datetimepicker({
        todayHighlight: true,
        autoclose: true,
        startView: 2,
        minView: 2,
        forceParse: 0,
        pickerPosition: 'bottom-left',
        todayBtn: true,
        format: 'dd/mm/yyyy'
    });

    $('#guardar_viaje').click(function (e) {
        e.preventDefault();
        $("#viaje_form").submit();
    });

    $("#viaje_form").validate({
        // define validation rules
        rules: {
            nombre: {
                required: true
            },
            fecha_inicio: {
                required: true
            },
            fecha_fin: {
                required: true
            }
        },

        //display error alert on form submit  
        invalidHandler: function (event, validator) {
            var alert = $('#m_form_1_msg');
            alert.removeClass('m--hide').show();
            mApp.scrollTo(alert, -200);
        },

        submitHandler: function (form) {
            $("#viaje_form").ajaxSubmit({
                url: 'api/viaje',
                method: 'POST',
                dataType: "json",
                data: $("#viaje_form").serialize(),
                success: function (response, status, xhr, $form) {

                    showMsg('success', response.mensaje);

                    $("#viaje_form").trigger("reset");
                    $('#viaje_modal').modal('toggle');

                },
                error: function (response, status, xhr, $form) {
                    showMsg('danger', response.responseJSON.mensaje);
                }
            });
        }
    });

    var isotope = $('.grid').isotope({
        itemSelector: '.grid-item', // use a separate class for itemSelector, other than .col-
        percentPosition: true,
        masonry: {
            columnWidth: '.grid-sizer'
        }
    });

    return {
        // public functions
        init: function () {
            recuperar_viajes();
        }
    };
}();

jQuery(document).ready(function () {
    BootstrapSelect.init();
});