<template>
  <div class="vet-main-content scroll-sections">
    <div class="vet-header">
      <div>
        <h1>Agenda de Consultas y Vacunaciones</h1>
        <p class="text-muted">Aquí puedes gestionar tus citas.</p>
      </div>
    </div>

    <div class="calendar-container">
      <FullCalendar :options="calendarOptions" />
    </div>
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';

// Importaciones CORRECTAS para v6.1.17
import '@fullcalendar/common/main.css' // Obligatorio primero
import '@fullcalendar/daygrid/daygrid.css' // ¡Sin /main.css!
import '@fullcalendar/timegrid/timegrid.css'
import '@fullcalendar/list/list.css'

export default {
  name: 'Agenda',
  components: {
    FullCalendar,
  },
  data() {
    return {
      calendarOptions: {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
        },
        locales: [esLocale], // Cambiado a array (NUEVO)
        locale: 'es', // Especifica el locale a usar (NUEVO)
        buttonText: {
          today: 'Hoy',
          month: 'Mes',
          week: 'Semana',
          day: 'Día',
          list: 'Lista',
        },
        events: [],
        editable: true,
        selectable: true,
        nowIndicator: true, // Muestra marcador de hora actual (OPCIONAL)
        eventDisplay: 'auto', // Mejor visualización de eventos (NUEVO)
      },
      exampleEvents: [
        {
          title: 'Consulta Dr. Pelusa',
          start: new Date().setHours(10, 0, 0, 0),
          end: new Date().setHours(10, 30, 0, 0),
          backgroundColor: '#3498db', // Cambiado de 'color' a 'backgroundColor'
        },
        {
          title: 'Vacunación Max',
          start: new Date().setHours(14, 0, 0, 0),
          end: new Date().setHours(14, 45, 0, 0),
          backgroundColor: '#2ecc71',
        }
      ],
    };
  },
  mounted() {
    this.fetchCalendarEvents();
  },
  methods: {
    fetchCalendarEvents() {
      // Formatea correctamente las fechas (NUEVO)
      this.calendarOptions.events = this.exampleEvents.map(event => ({
        ...event,
        start: new Date(event.start),
        end: event.end ? new Date(event.end) : null
      }));
    },
  },
};
</script>

<style>
/* Importante: Usa las clases CSS actualizadas */
.calendar-container {
  margin: 20px;
  padding: 20px;
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  flex-grow: 1;
}

/* Estilos actualizados para FullCalendar v6 */
.fc .fc-toolbar-title {
  font-size: 1.5em;
  color: #2c3e50;
}

.fc .fc-button {
  background-color: #3498db !important;
  border-color: #3498db !important;
  color: white !important;
  text-transform: capitalize;
}

.fc .fc-button:hover {
  background-color: #2980b9 !important;
}

.fc .fc-button-primary:not(:disabled).fc-button-active {
  background-color: #145a92 !important;
}

.fc-event {
  cursor: pointer;
  font-size: 0.85em;
  padding: 2px 4px;
}
</style>
