<template>
  <div class="vet-main-content scroll-sections">
    <div class="vet-header">
      <h1>Cartilla de Vacunación</h1>
      <p class="text-muted">Gestiona las cartillas de vacunación de tus pacientes.</p>
    </div>

    <div class="consulta-container">
      <div class="consulta-box">
        <i class="fas fa-plus-circle"></i>
        <h2>Nueva cartilla</h2>
        <router-link to="/cartilla-vacunacion/nueva" class="vet-btn-link">
          Iniciar cartilla
        </router-link>
      </div>
      <div class="consulta-box">
        <i class="fas fa-edit"></i>
        <h2>Editar cartilla anterior</h2>
        <router-link to="/cartilla-vacunacion/modificar" class="vet-btn-link">
          Modificar cartilla
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CartillaVacunacionPage',
  // Si necesitaras cargar datos dinámicos aquí
};
</script>

<style scoped>

.consulta-container {
  display: flex;
  justify-content: center;
  gap: 30px; 
  padding: 30px;
  flex-wrap: wrap; 
}

.consulta-box {
  background-color: #ffffff;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  text-align: center;
  width: 300px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.consulta-box:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
}

.consulta-box i {
  font-size: 3em; 
  color: #3498db; 
  margin-bottom: 20px;
}

.consulta-box h2 {
  font-size: 1.8em;
  color: #333;
  margin-bottom: 15px;
}

.vet-btn-link { 
  display: inline-block;
  background-color: #2ecc71; 
  color: white;
  padding: 12px 25px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.vet-btn-link:hover {
  background-color: #27ae60;
}
</style>
