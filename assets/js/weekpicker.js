$(document).ready(function () {
    var contenedor, display, input, datepicker, fechas;

    fechas = [];
    contenedor = $('#week-picker');
    input = $('#semana');
    display = contenedor.find('.form-control');

    contenedor.datepicker({
        weekStart: 1, // Inicio de la semana (0 - Domingo, 1 - Lunes, ..., 6 - Sábado)
        autoclose: true,
        format: "yyyy-mm-dd",
        calendarWeeks: true,
        beforeShowDay: function (fecha) {
            for (var i = 0; i < fechas.length; i++) {
                if (fechas[i].getTime() === fecha.getTime()) {
                    return { classes: 'active' };
                }
            }
        }
    });
    datepicker = contenedor.data('datepicker');

    function establecerFechas(fecha) {
        var diferenciaInicioSemana, inicioSemana, nuevaFecha;

        diferenciaInicioSemana = datepicker.o.weekStart - fecha.getDay();
        if (diferenciaInicioSemana > 0) {
            diferenciaInicioSemana -= 7;
        }

        inicioSemana = new Date(fecha.valueOf());
        inicioSemana.setDate(inicioSemana.getDate() + diferenciaInicioSemana);
        input.val(inicioSemana.toISOString().split('T')[0]);

        fechas = [];
        for (var i = 0; i < 7; i++) {
            nuevaFecha = new Date(inicioSemana.valueOf());
            nuevaFecha.setDate(nuevaFecha.getDate() + i);
            fechas.push(nuevaFecha);
        }
        datepicker.update();
    }

    function establecerDisplay() {
        display.html(fechas[0].toDateString() + ' - ' + fechas[6].toDateString());
    }

    contenedor.on('changeDate', function () {
        establecerFechas(datepicker.getDate());
        establecerDisplay();
    });

    display.on('click', function () {
        contenedor.find('.input-group-addon').trigger('click');
    });
});
