<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
    <style>
        #calendar{
            position:relative;
            max-width:500px;
            margin: 20px;
        }
    </style>
</head>
<body>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <div id="calendar"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth', // vue par mois
            selectable: true,
            select: function(info) {
                alert('Vous avez sélectionné : ' + info.startStr);
            }
        });
        calendar.render();
    });
    </script>

</body>
</html>