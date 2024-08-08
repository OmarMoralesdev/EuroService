<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Week Picker</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        .active { background-color: #5cb85c; color: white; }
    </style>
</head>
<body>
    <div class="form">
        <div id="week-picker" class="input-group date">
            <div class="form-control">Select a week...</div>
            <input type="hidden" id="firstDate">
            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
        <br>
        <div id="belowContainer"></div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function () {
            var container, display, input, datepicker, dates;

            dates = [];
            container = $('#week-picker');
            input = $('#firstDate');
            display = container.find('.form-control');

            container.datepicker({
                weekStart: 3, // Start of the week (0 - Sunday, 1 - Monday, ..., 6 - Saturday)
                autoclose: true,
                format: "yyyy-mm-dd",
                calendarWeeks: true,
                beforeShowDay: function (date) {
                    var i, j;
                    for (i = 0, j = dates.length; i < j; i += 1) {
                        if (dates[i].getTime() === date.getTime()) {
                            return { classes: 'active' };
                        }
                    }
                }
            });
            datepicker = container.data('datepicker');

            function setDates(date) {
                var diffToWeekStart, weekStart, i, nd;
                diffToWeekStart = datepicker.o.weekStart - date.getDay();
                if (diffToWeekStart > 0) {
                    diffToWeekStart = diffToWeekStart - 7;
                }

                weekStart = new Date(date.valueOf());
                weekStart.setDate(weekStart.getDate() + diffToWeekStart);
                input.val(weekStart.toISOString().split('T')[0]);

                dates = [];
                for (i = 0; i < 7; i += 1) {
                    nd = new Date(weekStart.valueOf());
                    nd.setDate(nd.getDate() + i);
                    dates.push(nd);
                }
                datepicker.update();
            }

            function setDisplay() {
                display.html(dates[0].toDateString() + ' - ' + dates[6].toDateString());
            }

            function ajaxCall() {
                $("#belowContainer").load('@(Url.Action("Getcourses","Home",null, Request.Url.Scheme))?dateVal=' + dates[0].toISOString().split('T')[0]);
            }

            container.on('changeDate', function () {
                setDates(datepicker.getDate());
                setDisplay();
                ajaxCall();
            });

            display.on('click', function () {
                container.find('.input-group-addon').trigger('click');
            });
        });
    </script>
</body>
</html>
