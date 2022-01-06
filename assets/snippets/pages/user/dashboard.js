//== Class definition

var BootstrapSelect = (function () {
  //== Private functions
  var demos = function () {
    $(".m_datetimepicker").datetimepicker({
      todayHighlight: true,
      autoclose: true,
      startView: 2,
      minView: 2,
      forceParse: 0,
      pickerPosition: "bottom-left",
      todayBtn: true,
      format: "dd/mm/yyyy",
    });

    Highcharts.chart("historico", {
      chart: {
        type: "spline",
      },
      title: {
        text: "",
      },

      yAxis: {
        visible: false,
      },

      xAxis: {
        categories: categorias,
      },

      legend: {
        enabled: false,
      },

      plotOptions: {
        line: {
          dataLabels: {
            enabled: false,
          },
          enableMouseTracking: true,
        },
      },

      series: [
        {
          name: "Gastos Privados",
          data: gastos_privados,
          color: "#f56464",
          marker: {
            enabled: false,
          },
        },
        {
          name: "Gastos Comunes",
          data: gastos_comunes,
          color: "#f5a864",
          marker: {
            enabled: false,
          },
        },
        {
          name: "Ingresos",
          data: ingresos,
          color: "#90ed7c",
          marker: {
            enabled: false,
          },
        },
        {
          name: "Beneficios",
          data: beneficios,
          marker: {
            enabled: false,
          },
        },
        {
          name: "Gastos Totales",
          data: gastos_totales,
          color: "#00c5dc",
          marker: {
            enabled: false,
          },
        },
        {
          name: "Ceros",
          data: ceros,
          color: "#f00",
          marker: {
            enabled: false,
          },
        },
      ],

      responsive: {
        rules: [
          {
            condition: {
              maxWidth: 500,
            },
            chartOptions: {
              legend: {
                layout: "horizontal",
                align: "center",
                verticalAlign: "bottom",
              },
            },
          },
        ],
      },
      exporting: {
        enabled: false,
      },
    });

    Highcharts.chart("gastos_ingresos", {
      chart: {
        type: "pie",
        height: "100%",
      },
      title: {
        text: "",
      },
      tooltip: {
        headerFormat: "",
        pointFormat:
          '<span style="color:{point.color}">\u25CF</span> <b> {point.name}</b><br/>' +
          "<b>{point.y}</b><br/>" +
          "<br/>",
      },
      plotOptions: {
        pie: {
          allowPointSelect: true,
          cursor: "pointer",
          dataLabels: {
            enabled: false,
          },
        },
      },
      exporting: {
        enabled: false,
      },
      series: [
        {
          minPointSize: 10,
          innerSize: "70%",
          zMin: 0,
          name: "countries",
          data: data_pie,
        },
      ],
    });
  };

  return {
    // public functions
    init: function () {
      demos();
    },
  };
})();

jQuery(document).ready(function () {
  BootstrapSelect.init();
});
