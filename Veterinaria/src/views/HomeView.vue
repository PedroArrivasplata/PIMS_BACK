
<template>
  <div>
    <DashboardHeader />

    <div class="vet-cards">
      <InfoCard
        title="Vacunaciones Pendientes"
        icon-class="fas fa-syringe"
        description1="Tienes 3 mascotas con vacunas pendientes."
        description2="Próxima vacuna: mañana a las 10:30 AM"
        link="/cartilla-vacunacion"
        button-text="Ver cartillas"
        type=""
      />

      <InfoCard
        title="Exámenes Médicos"
        icon-class="fas fa-microscope"
        description1="4 resultados pendientes de revisión."
        description2="2 análisis de laboratorio, 2 radiografías"
        link="/examenes-medicos"
        button-text="Revisar"
        type="info"
      />

      <InfoCard
        title="Consultas Programadas"
        icon-class="fas fa-calendar-alt"
        description1="Tienes 5 citas programadas para hoy."
        description2="3 consultas y 2 vacunaciones"
        link="/agenda"
        button-text="Ver agenda"
        type="warning"
      />

      <InfoCard
        title="Historial Médico"
        icon-class="fas fa-file-medical"
        description1="2 pacientes requieren actualización de historial."
        description2="Última actualización: hace 3 días"
        link="/historial-medico"
        button-text="Actualizar"
        type="alert"
      />
    </div>
  </div>
</template>

<script>
import DashboardHeader from '../components/DashboardHeader.vue';
import InfoCard from '../components/InfoCard.vue';

export default {
  name: 'HomePage',
  components: {
    DashboardHeader,
    InfoCard,
  },
  // Aquí es donde llamas a la API para obtener datos dinámicos.
 
};
</script>

