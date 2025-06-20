<template>
  <div class="vet-card vet-card-examen mb-3">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h5>{{ examen.nombre }}</h5>
        <p class="text-muted">Realizado el {{ examen.fecha }} - Consulta #{{ examen.consultaId }}</p>
        <p><strong>Estado:</strong> <span :class="['vet-chip', examen.estado === 'Completado' ? '' : 'vet-chip-pendiente']">{{ examen.estado }}</span></p>
      </div>
      <div>
        <button class="vet-btn-icon" aria-label="Ver resultado" @click="viewResult">
          <i class="fas fa-eye"></i>
        </button>
        <button class="vet-btn-icon" aria-label="Descargar examen" @click="downloadExamen">
          <i class="fas fa-download"></i>
        </button>
        <router-link :to="`/editar-examen-medico/${examen.id}`" class="vet-btn-icon" aria-label="Editar examen">
          <i class="fas fa-edit"></i>
        </router-link>
        <button class="vet-btn-icon" style="color: #e74c3c;" aria-label="Eliminar examen" @click="confirmDelete">
          <i class="fas fa-trash"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ExamenCard',
  props: {
    examen: {
      type: Object,
      required: true,
      validator: (examen) => {
        return [
          'id',
          'nombre',
          'fecha',
          'consultaId',
          'estado'
        ].every(prop => prop in examen);
      }
    }
  },
  methods: {
    viewResult() {
      try {
        // Lógica para ver el resultado
        alert(`Ver resultado del examen: ${this.examen.nombre}`);
      } catch (error) {
        console.error('Error al ver el resultado:', error);
        alert('Ocurrió un error al ver el resultado');
      }
    },
    downloadExamen() {
      try {
        // Lógica para descargar
        alert(`Descargar examen: ${this.examen.nombre}`);
      } catch (error) {
        console.error('Error al descargar:', error);
        alert('Ocurrió un error al descargar el examen');
      }
    },
    confirmDelete() {
      if (confirm('¿Estás seguro de querer eliminar este examen?')) {
        this.$emit('delete-examen', this.examen.id);
      }
    }
  }
}
</script>

<style scoped>
/* Solo estilos específicos que no existen en los archivos globales */
.vet-card-examen {
  transition: transform 0.2s ease;
}

.vet-card-examen:hover {
  transform: translateY(-3px);
}

.vet-chip-pendiente {
  background-color: #f39c12;
  color: white;
}
</style>