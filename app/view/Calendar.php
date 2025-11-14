<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendrier des rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

</head>
<body>
    <h1 class="titre-calendrier">Calendrier des rendez-vous</h1>
        <?php if (!empty($_SESSION['flash'])): ?>
        <?php $t = htmlspecialchars((string)($_SESSION['flash_type'] ?? 'success')); ?>
        <div class="flash <?= $t ?>"><?= htmlspecialchars((string)$_SESSION['flash']) ?></div>
        <?php unset($_SESSION['flash'], $_SESSION['flash_type']); ?>
        <?php endif; ?>
    <div id="calendar"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            locale: 'fr',
            selectable: true,
            editable: false,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'timeGridWeek,timeGridDay'
            },
            events: '/administrateur/api-rdv',

            select: function(info) {
                const date = info.startStr;
                if (confirm(`Créer un RDV le ${date} ?`)) {
                    window.location.href = `/administrateur/creer-entretien?date=${date}`;
                }
            },

            eventClick: function(info) {
                window.location.href = `/administrateur/rdv?id=${info.event.id}`;
            },

            eventDidMount: function(info) {
                // Ajout d’un tooltip au survol
                const tooltip = `${info.event.title}\nType : ${info.event.extendedProps.type || ''}`;
                info.el.setAttribute('title', tooltip);
            }
        });

        calendar.render();
    });
    </script>
</body>
</html>
