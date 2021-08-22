$(document).ready( function () 
{
    $('#users-list').DataTable();

    $("#tipo").change(function() 
    {
        if($("#tipo").val() != "")
        {
            $("#desde").prop('disabled', false);
        }else
        {
            $("#desde").prop('disabled', true);
            $('input[id=desde').val('');
            $('input[id=hasta').val('');
            $("#hasta").prop('disabled', true);
        }
    });
    
    $("#desde").change(function() 
    {
        $("#hasta").prop('disabled', false);
        $('#hasta').attr('min', $('#desde').val());
        if($("#desde").val() > $("#hasta").val())
        {
            $('input[id=hasta').val('');
        }
    });

    $("#grafico").click(function() 
    {
        if($("#tipo").val() == "")
        {
            alert("Tienes que elegir un tipo de indicador antes")
            return false;
        }

        if($("#desde").val() == "")
        {
            alert("Tienes que elegir una fecha desde")
            return false;
        }

        if($("#hasta").val() == "")
        {
            alert("Tienes que elegir una fecha hasta")
            return false;
        }

        var fechaInicio = new Date($("#desde").val());
        var fechaFin    = new Date($("#hasta").val());
        var fechas = [];
        var tipo = $("#tipo").val();

        //dado una fecha "desde" y "hasta", se insertan en un array, todos los dias dentro del intervalo.
        while(fechaFin.getTime() >= fechaInicio.getTime())
        {
            fechaInicio.setDate(fechaInicio.getDate() + 1);
            fechas.push(fechaInicio.getDate() + '-' + (fechaInicio.getMonth() + 1) + '-' + fechaInicio.getFullYear());
        }

        $.ajax({
            url: '/obtenerDatos',
            method: "POST",
            data : { tipos: tipo, fechas : fechas }, // serializes the form's elements.
            beforeSend :function()
            {
                $("#cargando").css('visibility', 'visible');
            },
            success: function(data)
            {
                $("#cargando").css('visibility', 'hidden');
                generarGrafico(data, fechas, tipo);
            },
            error: function()
            {
                $("#cargando").css('visibility', 'hidden');
                alert("error al consumir la api, intente con un intervalo de fechas mas pequeño");
            }
        });
    });

    $('#guardar').on('click',function(e)
    {
        e.preventDefault();

        if($("#nombre").val() == "")
        {
            alert("El campo nombre no puede estár vacío")
            return false;
        }

        if($("#codigo").val() == "")
        {
            alert("El campo código no puede estár vacío")
            return false;
        }

        if($("#unidad_medida").val() == "")
        {
            alert("El campo unidad de medida no puede estár vacío")
            return false;
        }

        if($("#fecha").val() == "")
        {
            alert("Tienes que elegir una fecha")
            return false;
        }

        if($("#valor").val() == "")
        {
            alert("El valor no puede ser vacío")
            return false;
        }

        var form = $("#formularioAgregar");
        var t = $('#users-list').DataTable();
        var nombre = $("#nombre").val();
        var codigo = $("#codigo").val();
        var unidadMedida = $("#unidad_medida").val();
        var fecha = $("#fecha").val().split('-').reverse().join('-');
        var fechaParam = $("#fecha").val();
        var valor = $("#valor").val();

        $.ajax({
            url: 'agregar',
            method: "POST",
            data: form.serialize(),
            success: function(response)
            {
                //Se inserta una nueva row con los nuevos datos y cada td se le asigna un id para poder mostrar los cambios en la tabla de forma dinamica.
                $('#addModal').modal('hide');
                t.row.add( [
                nombre,
                codigo,
                unidadMedida,
                fecha,
                valor,
                '<a id="e'+response+'" class="btn btn-info btn-sm" data-id="'+response+'" data-nombre="'+nombre+'" data-codigo="'+codigo+'" data-unidad="'+unidadMedida+'" data-fecha="'+fechaParam+'" data-valor="'+valor+'">Editar</a> <a class="btn btn-danger btn-sm" data-id="'+response+'">Borrar</a>',
                ] ).draw().node().id = response;
                $('tr[id='+response+']').find("td:eq(0)").attr('id', "n"+response);
                $('tr[id='+response+']').find("td:eq(1)").attr('id', "c"+response);
                $('tr[id='+response+']').find("td:eq(2)").attr('id', "u"+response);
                $('tr[id='+response+']').find("td:eq(3)").attr('id', "f"+response);
                $('tr[id='+response+']').find("td:eq(4)").attr('id', "v"+response);
            },
            error: function()
            {
                alert("error al agregar el dato");
            }
        });
    });

    $(document).on('click', '.btn-danger', function()
    {   
        const id = $(this).data('id');
        $('.ufBorrarId').val(id);
        $('#deleteModal').modal('show');
    });

    $(document).on('click', '.btn-info', function()
    {
        $('.ufEditarId').val($(this).attr('data-id'));
        $('#nombreEditar').val($(this).attr('data-nombre'));
        $('#codigoEditar').val($(this).attr('data-codigo'));
        $('#unidad_medidaEditar').val($(this).attr('data-unidad'));
        $('#fechaEditar').val($(this).attr('data-fecha'));
        $('#valorEditar').val($(this).attr('data-valor'));
        
        $('#editModal').modal('show');
    });

    $('#editar').on('click',function(e)
    {
        e.preventDefault();

        if($("#nombreEditar").val() == "")
        {
            alert("El campo nombre no puede estár vacío")
            return false;
        }

        if($("#codigoEditar").val() == "")
        {
            alert("El campo código no puede estár vacío")
            return false;
        }

        if($("#unidad_medidaEditar").val() == "")
        {
            alert("El campo unidad de medida no puede estár vacío")
            return false;
        }

        if($("#fechaEditar").val() == "")
        {
            alert("Tienes que elegir una fecha")
            return false;
        }

        if($("#valorEditar").val() == "")
        {
            alert("El valor no puede ser vacío")
            return false;
        }

        id = $('.ufEditarId').val();
        form = $("#formularioEditar");
        
        $.ajax({
            url: 'editar/'+id,
            method: "POST",
            data: form.serialize(),
            success: function(data)
            {
                //Para que cuando se edite una row, se muestren los datos de manera dinamica y se asignan nuevos valores a los data para evitar conflictos
                //en los botones con los campos editados
                nuevo = JSON.parse(data);
                console.log(nuevo);
                $("#n"+id).html(nuevo.nombre);
                $("#c"+id).html(nuevo.codigo);
                $("#u"+id).html(nuevo.unidad_medida);
                $("#f"+id).html(nuevo.fecha.split('-').reverse().join('-'));
                $("#v"+id).html(nuevo.valor);
                $("#e"+id).attr('data-nombre', nuevo.nombre);
                $("#e"+id).attr('data-codigo', nuevo.codigo);
                $("#e"+id).attr('data-unidad', nuevo.unidad_medida);
                $("#e"+id).attr('data-fecha', nuevo.fecha);
                $("#e"+id).attr('data-valor', nuevo.valor);
                $('#editModal').modal('hide');
            },
            error: function()
            {
                alert("error al editar el dato");
            }
        });
    });

});

function generarGrafico(data, fechas, tipo)
{
    var speedCanvas = document.getElementById("speedChart");
    var label = "Valor - "+tipo;
    let array = JSON.parse(data);
    
    if(window.lineChart != null)
    {
        window.lineChart.destroy();
    }

    var dataFirst = {
        label: label,
        data: array,
        lineTension: 0,
        fill: false,
        borderColor: 'black'
    };

    var speedData = {
        labels: fechas,
        datasets: [dataFirst]
    };

    var chartOptions = {
        legend: {
        display: true,
        position: 'top',
        labels: {
        boxWidth: 80,
        fontColor: 'black'
        }
    }
    };

    window.lineChart = new Chart(speedCanvas, {
    type: 'line',
    data: speedData,
    options: chartOptions
    });
}

function borrar()
{
    var id = $('.ufBorrarId').val();
    
    $.ajax({
        url: 'delete/'+id,
        method: "GET",
        success: function(data)
        {
            $("#"+id).fadeOut();
            $('#deleteModal').modal('hide');
        },
        error: function()
        {
            alert("error al borrar el dato");
        }
    });
}


        





