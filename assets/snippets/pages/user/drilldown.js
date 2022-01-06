    
function cargarPieChart (comunes, data) {
    Highcharts.chart('grafico-pie-gastos', {
        chart: {
            type: 'pie',
            backgroundColor: null,
            events: {
                drilldown: function (e) {
                    if (parseInt(e.point.drilldown) > 0) {

                        var chart = this;
                        var punto = e;
                        // e.point.name is info which bar was clicked
                        chart.showLoading('Cargando datos...');
                        $.ajax({
                            url: 'api/drilldown',
                            method: 'GET',
                            dataType: 'json',
                            data: {
                                nombre:e.point.name, 
                                tabla_familias: 'viajes_familias',
                                familia: e.point.drilldown,
                                privado: 'N',
                                tabla_gastos: 'viajes_gastos',
                                filtro: viaje,
                                filtro_nombre: 'viaje',
                                comunes: 'N',
                                tabla_comunes: ''
                            }
                        }).done(function(data) {
                            chart.hideLoading();
                            chart.addSeriesAsDrilldown(punto.point, data);
                        });
                    }
                }
            }
        },
        exporting: { enabled: false },
        title: {
            text: null
        },
        subtitle: {
            text: null
        },
        series: [{
            name: 'Gastos',
                colorByPoint: true,
                data: data
            }],
        drilldown: {}
    });
};

function getPieChart (tabla_familias, privado, tabla_gastos, filtro, filtro_nombre, comunes, tabla_comunes) {
    $.ajax({
        url: 'api/drilldown',
        type: 'GET',
        dataType: "json",
        data: {
            tabla_familias: tabla_familias,
            familia: 0,
            privado: privado,
            tabla_gastos: tabla_gastos,
            filtro: filtro,
            filtro_nombre: filtro_nombre,
            comunes: comunes,
            tabla_comunes: tabla_comunes
        }
    }).done(function(data) {

        cargarPieChart('N',data);

    });
}


