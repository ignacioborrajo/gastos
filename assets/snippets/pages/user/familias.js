var getFamilias = function (tabla) {
    $.ajax({
        url: 'api/familias',
        type: 'GET',
        dataType: "json",
        data: {
            tabla: tabla
        }
    }).done(function(data) {
        actualizarFamilias(data);
    });
};

var actualizarFamilias = function (familias) {
    $('#gastos_picker').html(htmlFamilias(familias));
    $("#gastos_picker").selectpicker("refresh");
    if($('#categoria') !== undefined) {
        $('#categoria').html(htmlCategorias(familias));
        $("#categoria").selectpicker("refresh");
    }
    if($('#lista_familias') !== undefined) {
        $('#lista_familias').html(htmlListaFamilias(familias));
    }
};

var htmlFamilias = function (familias) {
    var html = [];
    var padre_actual = -1;
    for(var i=0; i < familias.length; i++) {
        var familia = familias[i];
        if(familia.padre == 0) {
            if(familia.padre == padre_actual) {
                html.push('</optgroup>');
            } else {
                html.push('<optgroup label="' + familia.nombre + '" data-max-options="2">');
            }
        } else {
            html.push('<option value="' + familia.id + '" data-icon="' + familia.icono + '" data-tokens="' + familia.ticket + '">' + familia.nombre + '</option>');
        }
    }
    return html.join('');
};

var htmlCategorias = function (familias) {
    var html = [];
    for(var i=0; i < familias.length; i++) {
        var familia = familias[i];
        if(familia.padre == 0) {
            html.push('<option value="' + familia.id + '">' + familia.nombre + '</option>');
        }
    }
    return html.join('');
};

var htmlListaFamilias = function (familias) {
    var html = [];
    var padre_actual = -1;
    for(var i=0; i < familias.length; i++) {
        var familia = familias[i];
        if(familia.padre == 0) {
            if(padre_actual != -1) {
                html.push('</ul></li>');
            }
            html.push('<li class="m-nav__item m-nav__item--active">' +
                        '<a href="" class="m-nav__link">' +
                            '<i class="m-nav__link-icon fa fa-trash-o"></i>' +
                            '<span class="m-nav__link-title">' +
                                '<span class="m-nav__link-wrap">' +
                                    '<span class="m-nav__link-text">' + familia.nombre + '</span>' +
                                '</span>' +
                            '</span>' +
                        '</a>' +
                        '<ul class="m-nav__sub">'
                      );
            padre_actual = familia.id;
        } else {
            html.push('<li class="m-nav__item">' +
                            '<a href="#" class="m-nav__link eliminar-familia" data-id="' + familia.id + '" data-usuario="">' +
                                '<span class="m-nav__link-bullet m-nav__link-bullet--line">' +
                                    '<span></span>' +
                                '</span>' +
                                '<span class="m-nav__link-text">' + familia.nombre + '</span>' +
                            '</a>' +
                        '</li>'
                    );
        }
    }
    return html.join('');
};

$('#guardar_categoria').click(function (e) {
    e.preventDefault();
    $("#categoria_form").submit();
});

$("#categoria_form").validate({
    // define validation rules
    rules: {
        categorianombre_categoria: {
            required: true
        }
    },
    //display error alert on form submit  
    invalidHandler: function (event, validator) {
        showMsg('danger', 'Faltan datos para guardar la categoría.<br>Por favor, inténtelo de nuevo.');
    },
    submitHandler: function (form) {
        $("#categoria_form").ajaxSubmit({
            url: 'api/categoria',
            type: 'POST',
            dataType: "json",
            data: $("#categoria_form").serialize(),
            success: function (response, status, xhr, $form) {
                showMsg('success', response.mensaje);
                actualizarFamilias(response.familias);
                $('#nombre_categoria').val('');
            },
            error: function (response, status, xhr, $form) {
                showMsg('danger', response.responseJSON.mensaje);
            }
        });
    }
});

$('#guardar_familia').click(function (e) {
    e.preventDefault();
    $("#familia_form").submit();
});

$("#familia_form").validate({
    // define validation rules
    rules: {
        categoria: {
            required: true
        },
        nombre_familia: {
            required: true
        }
    },
    //display error alert on form submit  
    invalidHandler: function (event, validator) {
        showMsg('danger', 'Faltan datos para guardar la familia.<br>Por favor, inténtelo de nuevo.');
    },
    submitHandler: function (form) {
        $("#familia_form").ajaxSubmit({
            url: 'api/familia',
            type: 'POST',
            dataType: "json",
            data: $("#familia_form").serialize(),
            success: function (response, status, xhr, $form) {
                showMsg('success', response.mensaje);
                actualizarFamilias(response.familias);
                $('#nombre_familia').val('');
            },
            error: function (response, status, xhr, $form) {
                showMsg('danger', response.responseJSON.mensaje);
            }
        });
    }
});