var BootstrapSelect = function () {

    var productos = new Array();
         
    var recuperar_viaje = function () {
        $.ajax({
        url: "api/viaje",
            method: "GET",
            dataType: "json",
            data: {id: viaje}
        }).done(function (data) {
            console.log(data);
            $('#nombre_viaje').text(data.nombre);
            $('#fecha_inicio').text(data.fecha_inicio);
            $('#fecha_fin').text(data.fecha_fin);
            $('#total_viaje').text(data.total + '€');
        });
    };
        
    $('.m_selectpicker').selectpicker();
    $('.bootstrap-select').css('background-color','transparent');
    $('#gastos_picker').on('changed.bs.select', function (event, clickedIndex, newValue, oldValue) {

        if ($(this)[0][clickedIndex].attributes[2].value == "N") {
            $('#ticket_modal').modal('hide');
            $('#mostrar_ticket').css('visibility','hidden');
        } else {
            $('#ticket_modal').modal();
            $('#mostrar_ticket').css('visibility','visible');
        }

    });
    
    $('#m_datetimepicker').datetimepicker({
        todayHighlight: true,
        autoclose: true,
        startView: 2,
        minView: 2,
        forceParse: 0,
        pickerPosition: 'bottom-left',
        todayBtn: true,
        format: 'dd/mm/yyyy'
    });
    
    var repeater = $('#m_repeater_1').repeater({
        initEmpty: false,
        defaultValues: {
            'text-input': 'foo'
        },
        show: function () {
            $(this).slideDown();
            $('.t_producto').removeClass('t_producto').typeahead({
                hint: true,
                    highlight: true,
                    minLength: 2
                }, {
                name: 'states',
                    source: substringMatcher(productos)
                }).focus();
            $('.importe_ticket').change(function () {
                calcular_total_ticket(false);
            }).keypress(function (e) {
                if (e.which == 13) {
                    calcular_total_ticket(true);
                }
            });
        },
        hide: function (deleteElement) {
            $(this).slideUp(deleteElement);
        }
    });
    var substringMatcher = function (strs) {
        return function findMatches(q, cb) {
            var matches, substringRegex;
            // an array that will be populated with substring matches
            matches = [];
            // regex used to determine if a string contains the substring `q`
            substrRegex = new RegExp(q, 'i');
            // iterate through the pool of strings and for any string that
            // contains the substring `q`, add it to the `matches` array
            $.each(strs, function (i, str) {
                if (substrRegex.test(str)) {
                    matches.push(str);
                }
            });
            cb(matches);
        };
    };
                
    $('.t_producto').removeClass('t_producto').typeahead({
        hint: true,
            highlight: true,
            minLength: 2
        }, {
        name: 'states',
            source: substringMatcher(productos)
        });
                
    $('.importe_ticket').change(function () {
        calcular_total_ticket(false);
    });
                
    $(".importe_ticket").keypress(function (e) {
        if (e.which == 13) {
            calcular_total_ticket(true);
        }
    });
                
    var calcular_total_ticket = function (crear_nuevo) {
        var total = 0.0;
        $('.importe_ticket').each(function () {
            if ($.isNumeric($(this).val()))
                total += parseFloat($(this).val());
        });
                        
        $('#importe_total').val(total);
                        
        $('#temp_total_ticket').text(total + '€');
                        
        if (crear_nuevo) {
            $('[data-repeater-create]').trigger('click');
        }
    };
                
    $('#cancel_modal').click(function (e) {
        $('[data-repeater-delete]').trigger('click');
        $('[data-repeater-create]').trigger('click');
        $('#temp_total_ticket').text('0€');
        $('#ticket_modal').modal('hide');
    });
                
    $('#enviar_gasto').click(function (e) {
        e.preventDefault();
        $("#gastos_form").submit();
    });
                
    $("#gastos_form").validate({
        // define validation rules
        rules: {
            usuario: {
                required: true
            },
            familia: {
                required: true
            },
            fecha: {
                required: true
            },
            importe: {
                required: true
            }
        },
        //display error alert on form submit  
        invalidHandler: function (event, validator) {
            var alert = $('#m_form_1_msg');
            alert.removeClass('m--hide').show();
            mApp.scrollTo(alert, - 200);
        },
        submitHandler: function (form) {
            $("#gastos_form").ajaxSubmit({
                url: 'api/gasto',
                type: 'POST',
                dataType: "json",
                data: {
                    tipo: 'viaje',
                    viaje: viaje,
                    gastos: $("#gastos_form").serialize(),
                    ticket: $("#ticket_form").serialize()
                },
                success: function (response, status, xhr, $form) {
                    showMsg('success', response.mensaje);
                    $("#gastos_form").trigger("reset");
                    $("#usuario_picker").selectpicker("refresh");
                    $("#gastos_picker").selectpicker("refresh");
                    $('#cancel_modal').trigger('click');
                    recuperar_viaje();
                },
                error: function (response, status, xhr, $form) {
                    showMsg('danger', 'Se ha producido un error al guardar los nuevos datos.<br>Por favor, inténtelo de nuevo.');
                }
            });
        }
    });
     
    return {
        // public functions
        init: function () {
            recuperar_viaje();
        }
    };
}();

jQuery(document).ready(function () {
    getFamilias('viajes_familias');
    getPieChart ('viajes_familias', 'N', 'viajes_gastos', viaje, 'viaje', 'N', '');
    getDataTable ('viajes_gastos', 'viajes_familias', 'viaje', viaje);
    BootstrapSelect.init();
});