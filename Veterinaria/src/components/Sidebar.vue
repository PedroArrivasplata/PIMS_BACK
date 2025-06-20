<template>
  <nav class="vet-sidebar" aria-label="Navegación principal">
  <div class="vet-sidebar">
    <div class="vet-sidebar-header">
      <img src="@/assets/images/happy_dog.png" alt="Medical Vice Logo">
      <h3>Medical Vice</h3>
      <p>Panel Veterinario</p>
    </div>
    <ul class="vet-sidebar-nav" role="menubar">
      <li>
        <router-link to="/" active-class="active"><i class="fas fa-home"></i> Inicio</router-link>
      </li>
      <li>
        <router-link to="/agenda"><i class="fas fa-calendar-alt"></i> Agenda</router-link>
      </li>
      <li>
        <router-link to="/cartilla-vacunacion"><i class="fas fa-syringe"></i> Cartilla de Vacunación</router-link>
      </li>
      <li>
        <router-link to="/consulta-medica"><i class="fas fa-stethoscope"></i> Consulta Médica</router-link>
      </li>
      <li>
        <router-link to="/examenes-medicos"><i class="fas fa-microscope"></i> Exámenes Médicos </router-link>
      </li>
      <li>
        <router-link to="/historial-medico"><i class="fas fa-file-medical"></i> Historial Médico</router-link>
      </li>
      <li>
        <router-link to="/informes"><i class="fas fa-chart-line"></i> Informes</router-link>
      </li>
      <li>
        <router-link to="/configuracion"><i class="fas fa-cog"></i> Configuración</router-link>
      </li>
    </ul>
    <router-link to="/login" class="vet-logout">
      <i class="fas fa-sign-out-alt"></i>
      Cerrar sesión
    </router-link>
  </div>
  </nav>
</template>

<script>
export default {
  name: 'Sidebar',
};
</script>

