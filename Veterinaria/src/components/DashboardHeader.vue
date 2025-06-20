<template>
  <div class="vet-header">
    <div class="d-flex align-items-center">
      <img 
        src="@/assets/images/user_icon.png" 
        alt="Usuario" 
        class="vet-user-icon mr-3"
      >
    </div>
    <div>
      <h1>Bienvenido, {{ userName }}</h1>
      <p class="text-muted">{{ currentDate }}</p>
    </div>
  </div>
</template>


<script>
export default {
  name: 'DashboardHeader',
  data() {
    return {
      userName: 'Dr. Veterinario',
      currentDate: new Date().toLocaleDateString('es-ES', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }),
    };
  },
};
</script>

