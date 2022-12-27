<?php
     set_time_limit(0);
     error_reporting(0);
     session_start();


     include_once('../main.php');

     include_once('../../controlador/ejecutar_sql.php');
     define(RAIZ, '');


     encabezado('Menu Principal - Sistema de Administracion - Campana');
     


?>
<style type="text/css">


</style>

  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <!-- Bootstrap core CSS -->
  <link href="/vista/js/MDB-Free_4/css/bootstrap.min.css" rel="stylesheet">
  <!-- Material Design Bootstrap -->
  <link href="/vista/js/MDB-Free_4/css/mdb.min.css" rel="stylesheet">
  <!-- Your custom styles (optional) -->
  <link href="/vista/js/MDB-Free_4/css/style.css" rel="stylesheet">
  <link href="/vista/js/MDB-Free_4/css/addons/datatables.min.css" rel="stylesheet">


<BODY>
<?php
     menu();



?>

<div class="container">
        <!-- Default form register -->
        <form class="p-5" action="#!">

            <p class="h4 mb-4">Nuevo Ticket</p>

            <div class="form-row mb-4">
                <div class="col-lg-6">
                    <!-- First name -->
                    <label for="defaultRegisterFormFirstName">Asunto</label>
                    <input type="text" id="defaultRegisterFormFirstName" class="form-control">
                </div>
                <div class="col-lg-6">
                    <!-- First name -->
                    <label for="destinatario">Destinatario</label>
                    <select id="destinatario" class="browser-default custom-select">
                        <?php

                            $sql = "SELECT id, apenom FROM usuarios u where activo order by apenom";
                            $result = ejecutarSQLPDO($sql);
                            while ($row = mysqli_fetch_array($result))
                            {
                                print "<option value='$row[id]'>".strtoupper($row['apenom'])."</option>";
                            }


                        ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
              <label for="exampleFormControlTextarea1">Descripcion</label>
              <textarea class="form-control rounded-0" id="exampleFormControlTextarea1" rows="5"></textarea>
            </div>

            <!-- Sign up button -->
            <button class="btn  red darken-4 text-white my-4 btn-block" type="submit">Guardar</button>

        </form>
</div>

</BODY>


  <script type="text/javascript" src="/vista/js/MDB-Free_4/js/jquery-3.4.1.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/bootstrap.min.js"></script> 
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/popper.min.js"></script>

  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/mdb.min.js"></script>   
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/addons/datatables.js"></script>     
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/bootbox/bootbox.all.min.js"></script>     
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>  

  <script type="text/javascript">
    var v34 = $.noConflict(true);
</script>

 <script>

  v34(function() {

                    v34('.checked').click(function(event) {
                                                            event.preventDefault();
                                                            var a = $(this);
                                                            if (confirm('Seguro chequear la orden '+ a.data('servicio') +'?'))
                                                            {
                                                                $.post('/modelo/ordenes/ordenessur.php', 
                                                                      {
                                                                            accion:'check', 
                                                                            ord: a.data('orden')
                                                                       },
                                                                       function(data){
                                                                                        var response = $.parseJSON(data);
                                                                                        if (response.ok)
                                                                                        {
                                                                                            a.parent().parent().addClass('bg-primary ');
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            alert(a.msge);
                                                                                        }
                                                                       });
                                                            }
                    });

                    v34('.fa-edit').on('click', function (e) {
                                                            v34('#exampleModalLabel').html('Detalle de la orden '+ $(this).data('id'));
                                                            v34('.modal-body').load('/modelo/ordenes/ordenessur.php', {accion:'load', orden: $(this).data('id')});
                                                            v34('#basicExampleModal').modal({show:true})
                    });
                    v34('#dtBasicExample').DataTable({
                                                        "paging": false,
                                                        "searching": false // false to disable pagination (or any other option)
                                                      });
                    v34('.dataTables_length').addClass('bs-select');

                   v34(".ocultar").hide();

                  $("#back").button({icons: {primary: "ui-icon-circle-triangle-w"},text: false}).click(function(event){
                                                                                                                  event.preventDefault();
                                                                                                                  $("#direction").val('b');
                                                                                                                  $('#cargar').trigger('click');
                                                                                                                  });
                  $("#next").button({icons: {primary: "ui-icon-circle-triangle-e"},text: false}).click(function(event){
                                                                                                                  event.preventDefault();
                                                                                                                  $("#direction").val('n');
                                                                                                                  $('#cargar').trigger('click');
                                                                                                                  });
                  $('#cargar').button();
});
  </script>


</HTML>