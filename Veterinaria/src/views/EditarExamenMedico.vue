<template>
  <div class="vet-dashboard">
    <Sidebar />
    <div class="vet-main-content">
      <div class="vet-content-header">
        <h1 class="vet-page-title"><i class="fas fa-edit"></i> Editar Examen Médico</h1>
        <div class="vet-breadcrumb">
          <router-link to="/inicio">Inicio</router-link> /
          <router-link to="/examenes-medicos">Exámenes Médicos</router-link> /
          <span>Editar Examen</span>
        </div>
      </div>

      <div class="vet-card">
        <div class="vet-card-header">
          <h2 class="vet-card-title">Detalles del Examen</h2>
        </div>
        <div class="vet-card-body">
          <form @submit.prevent="saveChanges" class="vet-form">
            <div class="vet-form-group">
              <label for="nombreExamen">Nombre del Examen</label>
              <input type="text" id="nombreExamen" class="vet-input" v-model="examen.nombre" required>
            </div>
            <div class="vet-form-group">
              <label for="fechaRealizacion">Fecha de Realización</label>
              <input type="date" id="fechaRealizacion" class="vet-input" v-model="examen.fecha" required>
            </div>
            <div class="vet-form-group">
              <label for="consultaAsociada">Consulta Asociada</label>
              <input type="text" id="consultaAsociada" class="vet-input" v-model="examen.consultaId" required>
            </div>
            <div class="vet-form-group">
              <label for="estado">Estado</label>
              <select id="estado" class="vet-input" v-model="examen.estado" required>
                <option value="Completado">Completado</option>
                <option value="Pendiente">Pendiente</option>
                <option value="En Revisión">En Revisión</option>
              </select>
            </div>
            <div class="vet-form-group">
              <label for="resultados">Resultados</label>
              <textarea id="resultados" class="vet-textarea" v-model="examen.resultados"></textarea>
            </div>
            <div class="vet-form-actions">
              <button type="submit" class="vet-btn primary">
                <i class="fas fa-save"></i> Guardar Cambios
              </button>
              <router-link to="/examenes-medicos" class="vet-btn secondary">
                <i class="fas fa-times"></i> Cancelar
              </router-link>
              <button type="button" class="vet-btn alert" @click="handleDeleteExamen">
                <i class="fas fa-trash"></i> Eliminar Examen
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <ConfirmDeleteModal
    :is-visible="showDeleteModal"
    @cancel="showDeleteModal = false"
    @confirm="confirmDeleteExamen"
  />
</template>

<script>
import Sidebar from '@/components/Sidebar.vue';
import EliminarExamen from '@/components/EliminarExamen.vue';

export default {
  name: 'EditarExamenMedico',
  components: {
    Sidebar,
    ConfirmDeleteModal
  },
  data() {
    return {
      examen: {
        id: null,
        nombre: '',
        fecha: '',
        consultaId: '',
        estado: '',
        resultados: ''
      },
      showDeleteModal: false,
      examenToDeleteId: null
    };
  },
  created() {
    const examenId = this.$route.params.id; 
    this.loadExamenData(examenId);
  },
  methods: {
    loadExamenData(id) {

      const mockExams = [
        { id: 1, nombre: 'Análisis de sangre completo', fecha: '2025-03-01', consultaId: '001', estado: 'Completado', resultados: 'Resultados normales, sin anomalías.' },
        { id: 2, nombre: 'Radiografía torácica', fecha: '2025-03-10', consultaId: '002', estado: 'Completado', resultados: 'No se observan hallazgos significativos.' },
        { id: 3, nombre: 'Examen de orina', fecha: '2025-03-15', consultaId: '003', estado: 'Pendiente', resultados: '' },
      ];
      const foundExamen = mockExams.find(e => e.id == id);
      if (foundExamen) {
        this.examen = { ...foundExamen };
      } else {
        alert('Examen no encontrado.');
        this.$router.push('/examenes-medicos'); 
      }
    },
    saveChanges() {
      alert(`Guardando cambios para el examen: ${this.examen.nombre}`);
      this.$router.push('/examenes-medicos');
    },
    handleDeleteExamen() {
      this.examenToDeleteId = this.examen.id;
      this.showDeleteModal = true;
    },
    confirmDeleteExamen() {
      alert('Examen médico eliminado correctamente');
      this.showDeleteModal = false;
      this.examenToDeleteId = null;
      this.$router.push('/examenes-medicos'); 
    }
  }
};
</script>
