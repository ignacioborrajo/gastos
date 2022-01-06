var BootstrapSelect = (function () {
  //== Private functions
  var demos = function () {
    $(".editar-cuenta").click(function (e) {
      e.preventDefault();
      $(this).hide();
      let cuenta = $(this).data("cuenta");
      $("#importe-" + cuenta).hide();
      $("#progress-" + cuenta).hide();
      $("#form-" + cuenta).css("display", "flex");
    });

    $(".cancelar-edicion").click(function (e) {
      e.preventDefault();
      let cuenta = $(this).data("cuenta");
      $("#importe-" + cuenta).show();
      $("#progress-" + cuenta).show();
      $("#editar-" + cuenta).show();
      $("#form-" + cuenta).css("display", "none");
    });

    $(".eliminar-cuenta").click(function (e) {
      e.preventDefault();
      debugger;
      let cuenta = $(this).data("cuenta");
      swal({
        title: "¿Estás seguro de eliminar esta cuenta?",
        text: "Si borras esta cuenta se borrarán todos los datos asociados.",
        type: "error",
        showCancelButton: true,
        confirmButtonText: "Sí, ¡Bórralo!",
        cancelButtonText: "No, ¡Cancélalo!",
        reverseButtons: true,
      }).then(function (result) {
        if (result.value) {
          $("#eliminar-form-" + cuenta).submit();
        } else if (result.dismiss === "cancel") {
          swal("Cancelado", "El gasto permanece en la base de datos.", "error");
        }
      });
    });

    Highcharts.chart("historico", {
      chart: {
        type: "line",
      },
      title: {
        text: "",
      },

      yAxis: {
        visible: false,
      },

      xAxis: {
        type: "datetime",
        visible: true,
      },

      legend: {
        enabled: false,
      },
      tooltip: {
        headerFormat: "<b>{series.name}</b><br>",
        pointFormat: "{point.x:%e. %b}: {point.y:.2f} m",
      },
      plotOptions: {
        line: {
          dataLabels: {
            enabled: false,
          },
          enableMouseTracking: true,
        },
      },

      series: series,

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
