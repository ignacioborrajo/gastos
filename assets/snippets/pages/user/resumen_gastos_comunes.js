//== Class definition

var BootstrapSelect = (function () {
  var productos = new Array();

  var nacho = 0;
  var nelly = 0;

  var rango = "anual";
  var detallado = "N";

  var solo_mios = "N";
  var rango_fam = "T";

  var opciones_historico = {
    chart: {
      type: "column",
    },
    title: {
      text: null,
    },
    xAxis: {
      categories: [],
    },
    yAxis: {
      min: 0,
      title: null,
      stackLabels: {
        enabled: true,
        style: {
          fontWeight: "bold",
          color: (Highcharts.theme && Highcharts.theme.textColor) || "gray",
        },
      },
    },
    legend: {
      align: "right",
      verticalAlign: "top",
      floating: false,
      backgroundColor:
        (Highcharts.theme && Highcharts.theme.background2) || "white",
      borderColor: "#CCC",
      borderWidth: 1,
      shadow: false,
    },
    tooltip: {
      headerFormat: "<b>{point.x}</b><br/>",
      pointFormat: "{series.name}: {point.y}<br/>Total: {point.stackTotal}",
    },
    plotOptions: {
      column: {
        stacking: "normal",
        dataLabels: {
          enabled: true,
          color:
            (Highcharts.theme && Highcharts.theme.dataLabelsColor) || "white",
        },
      },
    },
    series: [],
  };

  var showMsg = function (tipo, msg) {
    var content = {};

    content.message = msg;

    if (tipo == "success") {
      content.title = "Éxito";
      content.icon = "icon flaticon-interface-5";
    } else {
      content.title = "Error";
      content.icon = "icon flaticon-cancel";
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
        from: "top",
        align: "right",
      },
      offset: {
        x: 30,
        y: 30,
      },
      delay: 1000,
      z_index: 10000,
      animate: {
        enter: "animated bounceInRight",
        exit: "animated bounceOutRight",
      },
    });
  };

  var ultimos_gastos = function () {
    $.ajax({
      url: "assets/snippets/pages/user/ultimos_gastos.php",
      method: "POST",
      dataType: "html",
    }).done(function (data) {
      $("#ultimos_gastos").html(data);
    });
  };

  var cargarPieChart = function (comunes, data) {
    Highcharts.chart("grafico-pie-gastos", {
      chart: {
        type: "pie",
        events: {
          drilldown: function (e) {
            if (parseInt(e.point.drilldown) > 0) {
              var chart = this;
              var punto = e;

              // e.point.name is info which bar was clicked
              chart.showLoading("Cargando datos...");

              $.ajax({
                url: "assets/snippets/pages/user/gastos_drilldown_comunes.php",
                method: "POST",
                data: {
                  nombre: e.point.name,
                  familia: e.point.drilldown,
                  rango: rango_fam,
                  solo_mios: solo_mios,
                },
                dataType: "json",
              }).done(function (data) {
                chart.hideLoading();
                chart.addSeriesAsDrilldown(punto.point, data);
              });
            }
          },
        },
      },
      title: {
        text: null,
      },
      subtitle: {
        text: null,
      },
      series: [
        {
          name: "Gastos",
          colorByPoint: true,
          data: data.pie_data,
        },
      ],
      drilldown: {},
    });
  };

  var calcular_gastos_comunes = function () {
    debugger;
    $.ajax({
      url: "assets/snippets/pages/user/calcular_gastos_comunes.php",
      method: "POST",
      dataType: "json",
      data: {
        rango: rango_fam,
        solo_mios: solo_mios,
      },
    }).done(function (data) {
      $("#gasto_este_mes").text(
        data.total_este_mes.toFixed(2).toString() + "€"
      );
      $("#gasto_total").text(data.total.toFixed(2).toString() + "€");

      cargarPieChart("N", data);
    });
  };

  //== Private functions
  var demos = function () {
    calcular_gastos_comunes();

    $(".cambio_rango_fam").click(function (e) {
      e.preventDefault();
      rango_fam = $(this).data("rango");
      calcular_gastos_comunes();
      $("#sel_gastos_fam").text($(this).text());
    });

    $.ajax({
      url: "assets/snippets/pages/user/recuperar_productos.php",
      method: "POST",
      dataType: "json",
    }).done(function (data) {
      $.each(data.options, function (i, item) {
        productos.push(item);
      });
    });

    $.ajax({
      url: "assets/snippets/pages/user/calcular_balance.php",
      method: "POST",
      dataType: "json",
    }).done(function (data) {
      nacho = data.nacho;
      nelly = data.nelly;

      var dif_nacho = data.nacho_total - data.nelly_total;
      var dif_nelly = data.nelly_total - data.nacho_total;

      $("#gastos_nelly").text(dif_nelly.toFixed(2).toString() + "€");
      $("#gastos_nelly").removeClass("m--font-danger m--font-success");
      if (dif_nelly < 0) $("#gastos_nelly").addClass("m--font-danger");
      else $("#gastos_nelly").addClass("m--font-success");

      $("#gastos_nacho").text(dif_nacho.toFixed(2).toString() + "€");
      $("#gastos_nacho").removeClass("m--font-danger m--font-success");
      if (dif_nacho < 0) $("#gastos_nacho").addClass("m--font-danger");
      else $("#gastos_nacho").addClass("m--font-success");

      $("#gasto_este_mes").text(
        data.total_este_mes.toFixed(2).toString() + "€"
      );
      $("#gasto_total").text(data.total.toFixed(2).toString() + "€");

      Highcharts.chart("grafico-balance", {
        chart: {
          plotBackgroundColor: null,
          plotBorderWidth: 0,
          plotShadow: false,
        },
        title: {
          text: "Balance",
          align: "center",
          verticalAlign: "middle",
          y: 0,
        },
        tooltip: {
          pointFormat: "{series.name}: <b>{point.percentage:.1f}%</b>",
        },
        plotOptions: {
          pie: {
            dataLabels: {
              enabled: true,
              distance: -50,
              style: {
                fontWeight: "bold",
                color: "white",
              },
            },
            startAngle: -180,
            endAngle: 180,
            center: ["50%", "50%"],
          },
        },
        series: [
          {
            type: "pie",
            name: "Browser share",
            innerSize: "50%",
            colors: ["#716aca", "#00c5dc"],
            data: [
              ["Nelly", nelly],
              ["Nacho", nacho],
              {
                name: "balance",
                y: 0.2,
                dataLabels: {
                  enabled: false,
                },
              },
            ],
          },
        ],
      });
    });

    $("#switch_detallado").bootstrapSwitch();
    $("#switch_detallado").on("switchChange.bootstrapSwitch", function (
      e,
      data
    ) {
      if (data) detallado = "S";
      else detallado = "N";

      console.log(detallado + "-" + rango);

      $.ajax({
        url: "assets/snippets/pages/user/rango_gastos_comunes.php",
        method: "GET",
        data: { rango: rango, detallado: detallado },
        dataType: "json",
      }).done(function (data) {
        opciones_historico.xAxis.categories = data.categorias;
        opciones_historico.series = data.datos;
        Highcharts.chart("grafico-historico", opciones_historico);
      });
    });

    $("#switch_solo_mios").bootstrapSwitch();
    $("#switch_solo_mios").on("switchChange.bootstrapSwitch", function (
      e,
      data
    ) {
      if (data) solo_mios = "S";
      else solo_mios = "N";

      console.log(detallado + "-" + rango);

      calcular_gastos_comunes();
    });

    $(".cambio_rango").click(function (e) {
      e.preventDefault();

      rango = $(this).data("rango");

      $.ajax({
        url: "assets/snippets/pages/user/rango_gastos_comunes.php",
        method: "GET",
        data: { rango: rango, detallado: detallado },
        dataType: "json",
      }).done(function (data) {
        opciones_historico.xAxis.categories = data.categorias;
        opciones_historico.series = data.datos;
        Highcharts.chart("grafico-historico", opciones_historico);
      });
    });

    $.ajax({
      url: "assets/snippets/pages/user/rango_gastos_comunes.php",
      method: "GET",
      data: { rango: rango, detallado: detallado },
      dataType: "json",
    }).done(function (data) {
      opciones_historico.xAxis.categories = data.categorias;
      opciones_historico.series = data.datos;
      Highcharts.chart("grafico-historico", opciones_historico);
    });

    $("#m_repeater_1").css("display", "none");

    $(".m_selectpicker").selectpicker();
    $("#gastos_picker").on("changed.bs.select", function (
      event,
      clickedIndex,
      newValue,
      oldValue
    ) {
      if ($(this)[0][clickedIndex].attributes[2].value == "N") {
        $("#m_repeater_1").css("display", "none");
      } else {
        $("#m_repeater_1").css("display", "block");
      }
    });

    $("#m_datetimepicker").datetimepicker({
      todayHighlight: true,
      autoclose: true,
      startView: 2,
      minView: 2,
      forceParse: 0,
      pickerPosition: "bottom-left",
      todayBtn: true,
      format: "dd/mm/yyyy",
    });

    $("#m_repeater_1").repeater({
      initEmpty: false,

      defaultValues: {
        "text-input": "foo",
      },

      show: function () {
        $(this).slideDown();
        $(".t_producto")
          .removeClass("t_producto")
          .typeahead(
            {
              hint: true,
              highlight: true,
              minLength: 2,
            },
            {
              name: "states",
              source: substringMatcher(productos),
            }
          );
        $(".importe_ticket").change(function () {
          var total = 0.0;
          $(".importe_ticket").each(function () {
            total += parseFloat($(this).val());
          });
          $("#importe_total").val(total);
        });
      },

      hide: function (deleteElement) {
        $(this).slideUp(deleteElement);
      },
    });

    var substringMatcher = function (strs) {
      return function findMatches(q, cb) {
        var matches, substringRegex;

        // an array that will be populated with substring matches
        matches = [];

        // regex used to determine if a string contains the substring `q`
        substrRegex = new RegExp(q, "i");

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

    $(".t_producto")
      .removeClass("t_producto")
      .typeahead(
        {
          hint: true,
          highlight: true,
          minLength: 2,
        },
        {
          name: "states",
          source: substringMatcher(productos),
        }
      );

    $(".importe_ticket").change(function () {
      var total = 0.0;
      $(".importe_ticket").each(function () {
        total += parseFloat($(this).val());
      });
      $("#importe_total").val(total);
    });

    $("#enviar_gasto").click(function (e) {
      e.preventDefault();
      $("#gastos_form").submit();
    });
    $("#gastos_form").validate({
      // define validation rules
      rules: {
        usuario: {
          required: true,
        },
        familia: {
          required: true,
        },
        fecha: {
          required: true,
        },
        importe: {
          required: true,
        },
      },

      //display error alert on form submit
      invalidHandler: function (event, validator) {
        var alert = $("#m_form_1_msg");
        alert.removeClass("m--hide").show();
        mApp.scrollTo(alert, -200);
      },

      submitHandler: function (form) {
        $("#gastos_form").ajaxSubmit({
          url: "assets/snippets/pages/user/guardar_nuevo_gasto.php",
          type: "POST",
          data: $("#gastos_form").serialize(),
          success: function (response, status, xhr, $form) {
            console.log("response: " + response);

            if (response != "0") {
              showMsg(
                "success",
                "Datos guardados correctamente.<br>Actualiza la página principal de la web para ver el resultado."
              );

              $("#linea-repetida")
                .children()
                .each(function (index, element) {
                  if (index > 0) $(this).remove();
                });
              $("#m_repeater_1").css("display", "none");

              $("#gastos_form").trigger("reset");
              $("#usuario_picker").selectpicker("refresh");
              $("#gastos_picker").selectpicker("refresh");

              ultimos_gastos();
            } else {
              showMsg(
                "danger",
                "Se ha producido un error al guardar los nuevos datos.<br>Por favor, inténtelo de nuevo."
              );
            }
          },
          error: function (response, status, xhr, $form) {
            showMsg(
              "danger",
              "Se ha producido un error al guardar los nuevos datos.<br>Por favor, inténtelo de nuevo."
            );
          },
        });
      },
    });
  };

  return {
    // public functions
    init: function () {
      ultimos_gastos();
      demos();
    },
  };
})();

jQuery(document).ready(function () {
  BootstrapSelect.init();
});
