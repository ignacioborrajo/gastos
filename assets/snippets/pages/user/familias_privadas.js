var BootstrapSelect = (function () {
  //== Private functions
  var demos = function () {
    $(".editar-familia").click(function (e) {
      e.preventDefault();
      $("#id_familia").val($(this).data("id"));
      $("#nombre_categoria").val($(this).data("nombre"));
      $("#familia_categoria").val($(this).data("padre"));
      if ($(this).data("ticket") == "S") {
        $("#tiene_ticket").removeAttr("checked");
      } else {
        $("#tiene_ticket").attr("checked", "checked");
      }

      $("#borrar_familia").val($(this).data("id"));
      $("#form_borrar").show();
    });
  };

  $("#cancelar_categoria").click(function (e) {
    e.preventDefault();
    $("#id_familia").val("");
    $("#borrar_familia").val("");
    $("#nombre_categoria").val("");
    $("#familia_categoria").val("");
    $("#tiene_ticket").removeAttr("checked");
    $("#form_borrar").hide();
  });

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
